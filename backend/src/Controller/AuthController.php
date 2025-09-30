<?php
declare(strict_types=1);

namespace App\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\Routing\Annotation\Route;

final class AuthController
{
    /**
     * Invalide la session côté client en supprimant le cookie JWT.
     */
    #[Route('/api/logout', name: 'api_logout', methods: ['POST'])]
    public function logout(): JsonResponse
    {
        $response = new JsonResponse(['message' => 'logged out']);

        $isDev = (($_ENV['APP_ENV'] ?? null) ?? getenv('APP_ENV') ?? 'prod') === 'dev';
        $sameSite = $isDev ? Cookie::SAMESITE_LAX : Cookie::SAMESITE_NONE;

        // name, path, domain, secure, httpOnly, sameSite
        $response->headers->clearCookie('AUTH_TOKEN', '/', null, !$isDev, true, $sameSite);
        return $response;
    }
}
