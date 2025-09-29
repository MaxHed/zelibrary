<?php
declare(strict_types=1);

namespace App\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

final class AuthController
{
    #[Route('/api/logout', name: 'api_logout', methods: ['POST'])]
    public function logout(): JsonResponse
    {
        $resp = new JsonResponse(['message' => 'logged out']);

        $isDev = (($_ENV['APP_ENV'] ?? null) ?? getenv('APP_ENV') ?? 'prod') === 'dev';
        $sameSite = $isDev ? 'lax' : 'none';

        // name, path, domain, secure, httpOnly, sameSite
        $resp->headers->clearCookie('AUTH_TOKEN', '/', null, !$isDev, true, $sameSite);
        return $resp;
    }
}
