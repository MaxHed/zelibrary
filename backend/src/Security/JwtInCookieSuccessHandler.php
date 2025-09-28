<?php
namespace App\Security;

use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Security\Http\Authentication\AuthenticationSuccessHandler;

class JwtInCookieSuccessHandler extends AuthenticationSuccessHandler
{
    public function onAuthenticationSuccess(Request $request, TokenInterface $token): JsonResponse
    {
        // 1) Récupérer le JWT (LexikJWTAuthenticationBundle vous le donne dans la réponse parente)
        $response = parent::onAuthenticationSuccess($request, $token);

        // La réponse contient déjà le token dans son body JSON, ex: {"token": "..."}.
        // On va aussi le mettre dans un cookie.

        // 2) Extraire le JWT depuis le JSON si nécessaire
        $data = json_decode($response->getContent(), true);
        $jwt = $data['token'] ?? null;

        // 3) Créer le cookie HttpOnly
        $cookie = Cookie::create('AUTH_TOKEN')
            ->withPath('/')
            ->withDomain(null)        // Pas de domaine spécifique pour localhost
            ->withValue($jwt)
            ->withHttpOnly(true)      // Pour empêcher l'accès via JavaScript
            ->withSecure(false)       // HTTP en dev, HTTPS en prod
            ->withSameSite('Lax')     // Compatible avec les requêtes same-site
            // ->withExpires(...)      // Optionnel : date d'expiration
        ;

        // 4) Ajouter le cookie à la réponse
        $response->headers->setCookie($cookie);

        return $response;
    }
}
