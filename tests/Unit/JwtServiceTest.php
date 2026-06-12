<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Services\JwtService;
use PHPUnit\Framework\TestCase;

class JwtServiceTest extends TestCase
{
    private JwtService $jwtService;

    protected function setUp(): void
    {
        $_ENV['JWT_SECRET'] = 'test-secret';
        $_ENV['JWT_ACCESS_TTL'] = '3600';
        $_ENV['JWT_REFRESH_TTL'] = '604800';
        $this->jwtService = new JwtService();
    }

    public function testGenerateAccessToken(): void
    {
        $token = $this->jwtService->generateAccessToken(1, 'test@example.com');
        $this->assertNotEmpty($token);
        $this->assertIsString($token);
    }

    public function testGenerateRefreshToken(): void
    {
        $token = $this->jwtService->generateRefreshToken(1, 'test@example.com');
        $this->assertNotEmpty($token);
        $this->assertIsString($token);
    }

    public function testVerifyValidToken(): void
    {
        $token = $this->jwtService->generateAccessToken(1, 'test@example.com');
        $decoded = $this->jwtService->verifyToken($token);
        $this->assertNotNull($decoded);
        $this->assertEquals(1, $decoded->sub);
        $this->assertEquals('test@example.com', $decoded->email);
        $this->assertEquals('access', $decoded->type);
    }

    public function testVerifyInvalidToken(): void
    {
        $decoded = $this->jwtService->verifyToken('invalid.token.here');
        $this->assertNull($decoded);
    }

    public function testTokenType(): void
    {
        $accessToken = $this->jwtService->generateAccessToken(1, 'test@example.com');
        $refreshToken = $this->jwtService->generateRefreshToken(1, 'test@example.com');

        $accessDecoded = $this->jwtService->verifyToken($accessToken);
        $refreshDecoded = $this->jwtService->verifyToken($refreshToken);

        $this->assertEquals('access', $accessDecoded->type);
        $this->assertEquals('refresh', $refreshDecoded->type);
    }
}
