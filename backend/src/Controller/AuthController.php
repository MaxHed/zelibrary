<?php
namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\Routing\Annotation\Route;

final class AuthController
{
    #[Route('/api/logout', name: 'api_logout', methods: ['POST'])]
    public function logout(): JsonResponse
    {
        $resp = new JsonResponse(['message' => 'logged out']);
        $resp->headers->clearCookie('AUTH_TOKEN', '/', null, true, true, 'lax'); // même SameSite que mis à l’auth
        return $resp;
    }
}
