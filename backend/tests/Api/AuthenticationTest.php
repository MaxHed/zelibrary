<?php

declare(strict_types=1);

namespace App\Tests\Api;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

/**
 * Tests d'authentification JWT
 * 
 * Vérifie que :
 * - Le login fonctionne et retourne un cookie JWT
 * - Le logout supprime le cookie
 * - Les endpoints protégés nécessitent l'authentification
 */
class AuthenticationTest extends WebTestCase
{
    private $client;

    protected function setUp(): void
    {
        $this->client = static::createClient();
    }

    public function testLoginWithValidCredentials(): void
    {
        $this->client->request('POST', '/api/login_check', [], [], [
            'CONTENT_TYPE' => 'application/json',
        ], json_encode([
            'email' => 'jean.dupont@email.com',
            'password' => 'password',
        ]));

        $this->assertResponseStatusCodeSame(Response::HTTP_NO_CONTENT);
        
        // Vérifier que le cookie JWT est présent
        $cookies = $this->client->getResponse()->headers->getCookies();
        $authCookie = null;
        foreach ($cookies as $cookie) {
            if ($cookie->getName() === 'AUTH_TOKEN') {
                $authCookie = $cookie;
                break;
            }
        }
        
        $this->assertNotNull($authCookie, 'Le cookie AUTH_TOKEN doit être présent');
        $this->assertTrue($authCookie->isHttpOnly(), 'Le cookie doit être HttpOnly');
    }

    public function testLoginWithInvalidCredentials(): void
    {
        $this->client->request('POST', '/api/login_check', [], [], [
            'CONTENT_TYPE' => 'application/json',
        ], json_encode([
            'email' => 'wrong@email.com',
            'password' => 'wrongpassword',
        ]));

        $this->assertResponseStatusCodeSame(Response::HTTP_UNAUTHORIZED);
    }

    public function testAccessProtectedEndpointWithoutAuth(): void
    {
        $this->client->request('GET', '/api/me');
        
        $this->assertResponseStatusCodeSame(Response::HTTP_UNAUTHORIZED);
    }

    public function testAccessProtectedEndpointWithAuth(): void
    {
        // Login d'abord
        $this->client->request('POST', '/api/login_check', [], [], [
            'CONTENT_TYPE' => 'application/json',
        ], json_encode([
            'email' => 'jean.dupont@email.com',
            'password' => 'password',
        ]));

        $this->assertResponseIsSuccessful();

        // Maintenant accéder à /me avec le cookie
        $this->client->request('GET', '/api/me');
        
        $this->assertResponseIsSuccessful();
        
        $data = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('email', $data);
        $this->assertEquals('jean.dupont@email.com', $data['email']);
    }

    public function testLogout(): void
    {
        // Login d'abord
        $this->client->request('POST', '/api/login_check', [], [], [
            'CONTENT_TYPE' => 'application/json',
        ], json_encode([
            'email' => 'jean.dupont@email.com',
            'password' => 'password',
        ]));

        // Logout
        $this->client->request('POST', '/api/logout');
        
        $this->assertResponseIsSuccessful();
        
        // Vérifier que le cookie est supprimé (expires in the past)
        $cookies = $this->client->getResponse()->headers->getCookies();
        $authCookie = null;
        foreach ($cookies as $cookie) {
            if ($cookie->getName() === 'AUTH_TOKEN') {
                $authCookie = $cookie;
                break;
            }
        }
        
        // Le cookie doit être expiré
        if ($authCookie) {
            $this->assertLessThan(time(), $authCookie->getExpiresTime(), 
                'Le cookie doit être expiré après logout');
        }
    }
}

