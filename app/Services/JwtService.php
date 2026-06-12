<?php

declare(strict_types=1);

namespace App\Services;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Firebase\JWT\ExpiredException;

class JwtService
{
    private string $secret;
    private int $accessTtl;
    private int $refreshTtl;
    private string $algorithm;

    public function __construct()
    {
        $config = require __DIR__ . '/../../config/jwt.php';
        $this->secret = $config['secret'];
        $this->accessTtl = $config['access_ttl'];
        $this->refreshTtl = $config['refresh_ttl'];
        $this->algorithm = $config['algorithm'];
    }

    public function generateAccessToken(int $userId, string $email): string
    {
        $payload = [
            'iss' => 'taskmanager',
            'sub' => $userId,
            'email' => $email,
            'iat' => time(),
            'exp' => time() + $this->accessTtl,
            'type' => 'access',
        ];

        return JWT::encode($payload, $this->secret, $this->algorithm);
    }

    public function generateRefreshToken(int $userId, string $email): string
    {
        $payload = [
            'iss' => 'taskmanager',
            'sub' => $userId,
            'email' => $email,
            'iat' => time(),
            'exp' => time() + $this->refreshTtl,
            'type' => 'refresh',
        ];

        return JWT::encode($payload, $this->secret, $this->algorithm);
    }

    public function verifyToken(string $token): ?object
    {
        try {
            return JWT::decode($token, new Key($this->secret, $this->algorithm));
        } catch (ExpiredException) {
            return null;
        } catch (\Exception) {
            return null;
        }
    }

    public function getRefreshExpiry(): int
    {
        return time() + $this->refreshTtl;
    }

    public function getRefreshTtl(): int
    {
        return $this->refreshTtl;
    }
}
