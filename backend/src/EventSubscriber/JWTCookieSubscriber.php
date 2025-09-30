<?php

namespace App\EventSubscriber;

use Lexik\Bundle\JWTAuthenticationBundle\Events;
use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationSuccessEvent;
use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTInvalidEvent;
use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTExpiredEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Cookie;

/**
 * Abonne les événements JWT pour synchroniser un cookie HttpOnly avec le token.
 * - Au succès d'authentification: crée/actualise le cookie.
 * - Sur token invalide/expiré: supprime le cookie.
 */
final class JWTCookieSubscriber implements EventSubscriberInterface
{
    private const COOKIE_NAME = 'AUTH_TOKEN';
    private const COOKIE_PATH = '/';
    /**
     * Valeur SameSite par défaut en production (cross-site autorisé)
     */
    private const COOKIE_SAMESITE = Cookie::SAMESITE_NONE;

    public static function getSubscribedEvents(): array
    {
        return [
            Events::AUTHENTICATION_SUCCESS => 'onAuthenticationSuccess',
            Events::JWT_INVALID            => 'onJwtInvalidOrExpired',
            Events::JWT_EXPIRED            => 'onJwtInvalidOrExpired',
        ];
        // Écoute possible: JWT_NOT_FOUND si besoin.
    }

    /**
     * Sur succès d'authentification, aligne un cookie HttpOnly avec le JWT.
     */
    public function onAuthenticationSuccess(AuthenticationSuccessEvent $event): void
    {
        $data = $event->getData();
        $response = $event->getResponse();

        if (!isset($data['token'])) {
            return;
        }

        $token = $data['token'];
        $expires = time() + 3600; // aligné sur token_ttl

        // En dev: HTTP autorisé (non Secure) et SameSite=LAX; en prod: Secure + SameSite=None
        $isDev = ($_ENV['APP_ENV'] ?? getenv('APP_ENV') ?? 'prod') === 'dev';
        $secure = $isDev ? false : true;
        $sameSite = $isDev ? Cookie::SAMESITE_LAX : self::COOKIE_SAMESITE;

        // params: name, value, expire, path, domain, secure, httpOnly, raw, sameSite
        $cookie = Cookie::create(self::COOKIE_NAME, $token, $expires, self::COOKIE_PATH, null, $secure, true, false, $sameSite);
        $response->headers->setCookie($cookie);

        // En prod, ne pas renvoyer le token dans le body; en dev, conserver pour un fallback Bearer
        if (!$isDev) {
            unset($data['token']);
            $event->setData($data);
        }
    }

    /**
     * Sur invalidation ou expiration, efface le cookie côté client.
     */
    public function onJwtInvalidOrExpired(JWTInvalidEvent|JWTExpiredEvent $event): void
    {
        $isDev = ($_ENV['APP_ENV'] ?? getenv('APP_ENV') ?? 'prod') === 'dev';
        $secure = $isDev ? false : true;
        $response = $event->getResponse();
        $response->headers->clearCookie(self::COOKIE_NAME, self::COOKIE_PATH, null, $secure, true, self::COOKIE_SAMESITE);
    }
}
