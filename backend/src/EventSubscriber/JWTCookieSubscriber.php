<?php

namespace App\EventSubscriber;

use Lexik\Bundle\JWTAuthenticationBundle\Events;
use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationSuccessEvent;
use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTInvalidEvent;
use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTExpiredEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Cookie;

final class JWTCookieSubscriber implements EventSubscriberInterface
{
    private const COOKIE_NAME = 'AUTH_TOKEN';
    private const COOKIE_PATH = '/';
    private const COOKIE_SAMESITE = 'none'; // valeur prod par défaut (cross-site)

    public static function getSubscribedEvents(): array
    {
        return [
            Events::AUTHENTICATION_SUCCESS => 'onAuthenticationSuccess',
            Events::JWT_INVALID            => 'onJwtInvalidOrExpired',
            Events::JWT_EXPIRED            => 'onJwtInvalidOrExpired',
        ];
        // Tu peux aussi écouter JWT_NOT_FOUND si besoin.
    }

    public function onAuthenticationSuccess(AuthenticationSuccessEvent $event): void
    {
        $data = $event->getData();         // contient 'token'
        $response = $event->getResponse();

        if (!isset($data['token'])) {
            return;
        }

        $token = $data['token'];
        $expires = time() + 3600; // aligne avec token_ttl

        // En dev: accepter HTTP (pas Secure) et SameSite=lax; en prod: Secure + SameSite=None
        $isDev = ($_ENV['APP_ENV'] ?? getenv('APP_ENV') ?? 'prod') === 'dev';
        $secure = $isDev ? false : true;
        $sameSite = $isDev ? 'lax' : self::COOKIE_SAMESITE;

        $cookie = Cookie::create(self::COOKIE_NAME, $token, $expires, self::COOKIE_PATH, null, $secure, true, false, $sameSite);
        // params: name, value, expire, path, domain, secure, httpOnly, raw, sameSite

        $response->headers->setCookie($cookie);

        // En prod, ne pas renvoyer le token dans le body; en dev, laisser le token pour Bearer fallback
        if (!$isDev) {
            unset($data['token']);
            $event->setData($data);
        }
    }

    public function onJwtInvalidOrExpired($event): void
    {
        $response = $event->getResponse();
        $response->headers->clearCookie(self::COOKIE_NAME, self::COOKIE_PATH, null, true, true, self::COOKIE_SAMESITE);
    }
}
