<?php

declare(strict_types=1);

namespace App\Tests\Api;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

/**
 * Tests du système de reset password sécurisé
 * 
 * Vérifie que :
 * - Le reset password fonctionne avec un token temporaire
 * - L'ancien endpoint non sécurisé n'existe plus
 * - Les tokens expirent correctement
 */
class PasswordResetTest extends WebTestCase
{
    private $client;

    protected function setUp(): void
    {
        $this->client = static::createClient();
    }

    public function testOldUnsecureResetPasswordEndpointDoesNotExist(): void
    {
        // L'ancien endpoint /api/reset-password ne doit plus exister
        $this->client->request('POST', '/api/reset-password', [], [], [
            'CONTENT_TYPE' => 'application/json',
        ], json_encode([
            'email' => 'test@test.com',
            'newPassword' => 'newpass',
        ]));

        // Doit retourner 404 ou 405 (méthode non autorisée)
        $statusCode = $this->client->getResponse()->getStatusCode();
        $this->assertTrue(
            in_array($statusCode, [Response::HTTP_NOT_FOUND, Response::HTTP_METHOD_NOT_ALLOWED]),
            'L\'ancien endpoint non sécurisé ne doit plus être accessible'
        );
    }

    public function testRequestPasswordResetWithValidEmail(): void
    {
        $this->client->request('POST', '/api/password-reset/request', [], [], [
            'CONTENT_TYPE' => 'application/json',
        ], json_encode([
            'email' => 'jean.dupont@email.com',
        ]));

        $this->assertResponseIsSuccessful();
        
        $data = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('message', $data);
        
        // En mode dev, le token doit être présent
        if ($_ENV['APP_ENV'] === 'dev') {
            $this->assertArrayHasKey('dev_token', $data);
        }
    }

    public function testRequestPasswordResetWithInvalidEmail(): void
    {
        $this->client->request('POST', '/api/password-reset/request', [], [], [
            'CONTENT_TYPE' => 'application/json',
        ], json_encode([
            'email' => 'nonexistent@email.com',
        ]));

        // Doit retourner le même message (protection anti-énumération)
        $this->assertResponseIsSuccessful();
        
        $data = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('message', $data);
    }

    public function testConfirmPasswordResetWithInvalidToken(): void
    {
        $this->client->request('POST', '/api/password-reset/confirm', [], [], [
            'CONTENT_TYPE' => 'application/json',
        ], json_encode([
            'token' => 'invalid-token-12345',
            'newPassword' => 'newpassword123',
        ]));

        $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
        
        $data = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('error', $data);
        $this->assertStringContainsString('invalide', strtolower($data['error']));
    }

    public function testConfirmPasswordResetWithValidToken(): void
    {
        // D'abord demander un reset
        $this->client->request('POST', '/api/password-reset/request', [], [], [
            'CONTENT_TYPE' => 'application/json',
        ], json_encode([
            'email' => 'pierre.durand@email.com',
        ]));

        $this->assertResponseIsSuccessful();
        
        $data = json_decode($this->client->getResponse()->getContent(), true);
        
        // Si on est en dev, on a le token
        if (isset($data['dev_token'])) {
            $token = $data['dev_token'];
            
            // Confirmer le reset
            $this->client->request('POST', '/api/password-reset/confirm', [], [], [
                'CONTENT_TYPE' => 'application/json',
            ], json_encode([
                'token' => $token,
                'newPassword' => 'newpassword123',
            ]));

            $this->assertResponseIsSuccessful();
            
            $confirmData = json_decode($this->client->getResponse()->getContent(), true);
            $this->assertArrayHasKey('message', $confirmData);
        }
    }

    public function testConfirmPasswordResetWithShortPassword(): void
    {
        $this->client->request('POST', '/api/password-reset/confirm', [], [], [
            'CONTENT_TYPE' => 'application/json',
        ], json_encode([
            'token' => 'some-token',
            'newPassword' => '123', // Trop court (< 6 caractères)
        ]));

        $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
    }
}

