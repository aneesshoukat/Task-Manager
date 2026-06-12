<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\Database;
use App\Core\Logger;

class AuthService
{
    private Database $db;
    private JwtService $jwtService;

    public function __construct()
    {
        $this->db = Database::getInstance();
        $this->jwtService = new JwtService();
    }

    public function register(array $data): array
    {
        $existing = $this->db->fetch("SELECT id FROM users WHERE email = ?", [$data['email']]);

        if ($existing) {
            return ['success' => false, 'message' => 'Email already registered.'];
        }

        $userId = $this->db->insert('users', [
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => password_hash($data['password'], PASSWORD_BCRYPT),
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        $this->db->insert('activity_logs', [
            'user_id' => $userId,
            'action' => 'register',
            'ip_address' => $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1',
            'created_at' => date('Y-m-d H:i:s'),
        ]);

        Logger::info('User registered', ['user_id' => $userId, 'email' => $data['email']]);

        $tokens = $this->generateTokens($userId, $data['email']);
        return ['success' => true, 'message' => 'Registration successful.', ...$tokens];
    }

    public function login(string $email, string $password): array
    {
        $user = $this->db->fetch("SELECT * FROM users WHERE email = ?", [$email]);

        if (!$user || !password_verify($password, $user['password'])) {
            $this->recordFailedAttempt($email);
            return ['success' => false, 'message' => 'Invalid email or password.'];
        }

        $this->db->insert('activity_logs', [
            'user_id' => $user['id'],
            'action' => 'login',
            'ip_address' => $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1',
            'created_at' => date('Y-m-d H:i:s'),
        ]);

        $this->resetFailedAttempts($email);
        Logger::info('User logged in', ['user_id' => $user['id']]);

        $tokens = $this->generateTokens((int) $user['id'], $user['email']);
        return ['success' => true, 'message' => 'Login successful.', 'user' => $user, ...$tokens];
    }

    public function logout(int $userId): void
    {
        $this->db->delete('refresh_tokens', 'user_id = ?', [$userId]);

        $this->db->insert('activity_logs', [
            'user_id' => $userId,
            'action' => 'logout',
            'ip_address' => $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1',
            'created_at' => date('Y-m-d H:i:s'),
        ]);

        Logger::info('User logged out', ['user_id' => $userId]);
    }

    public function refreshAccessToken(string $refreshToken): array
    {
        $decoded = $this->jwtService->verifyToken($refreshToken);

        if (!$decoded || ($decoded->type ?? '') !== 'refresh') {
            return ['success' => false, 'message' => 'Invalid or expired refresh token.'];
        }

        $stored = $this->db->fetch(
            "SELECT * FROM refresh_tokens WHERE token_hash = ? AND user_id = ?",
            [hash('sha256', $refreshToken), $decoded->sub]
        );

        if (!$stored) {
            return ['success' => false, 'message' => 'Refresh token not found.'];
        }

        $this->db->delete('refresh_tokens', 'id = ?', [$stored['id']]);

        $tokens = $this->generateTokens((int) $decoded->sub, $decoded->email);
        return ['success' => true, 'message' => 'Token refreshed.', ...$tokens];
    }

    private function generateTokens(int $userId, string $email): array
    {
        $accessToken = $this->jwtService->generateAccessToken($userId, $email);
        $refreshToken = $this->jwtService->generateRefreshToken($userId, $email);

        $this->db->insert('refresh_tokens', [
            'user_id' => $userId,
            'token_hash' => hash('sha256', $refreshToken),
            'expires_at' => date('Y-m-d H:i:s', $this->jwtService->getRefreshExpiry()),
            'created_at' => date('Y-m-d H:i:s'),
        ]);

        return [
            'access_token' => $accessToken,
            'refresh_token' => $refreshToken,
        ];
    }

    private function recordFailedAttempt(string $email): void
    {
        $attempt = $this->db->fetch(
            "SELECT * FROM login_attempts WHERE email = ?",
            [$email]
        );

        if ($attempt) {
            $count = (int) $attempt['attempt_count'] + 1;
            $lockedUntil = $count >= 5 ? date('Y-m-d H:i:s', time() + 900) : null;

            $this->db->update(
                'login_attempts',
                [
                    'attempt_count' => $count,
                    'locked_until' => $lockedUntil,
                ],
                'id = ?',
                [$attempt['id']]
            );
        } else {
            $this->db->insert('login_attempts', [
                'email' => $email,
                'attempt_count' => 1,
                'locked_until' => null,
                'created_at' => date('Y-m-d H:i:s'),
            ]);
        }
    }

    private function resetFailedAttempts(string $email): void
    {
        $this->db->delete('login_attempts', 'email = ?', [$email]);
    }
}
