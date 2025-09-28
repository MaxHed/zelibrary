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
    private const COOKIE_SAMESITE = 'none'; // cross-site SPA: nécessite HTTPS

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

        $cookie = Cookie::create(self::COOKIE_NAME, $token, $expires, self::COOKIE_PATH, null, true, true, false, self::COOKIE_SAMESITE);
        // params: name, value, expire, path, domain, secure, httpOnly, raw, sameSite

        $response->headers->setCookie($cookie);

        // Optionnel : ne pas renvoyer le token dans le body
        unset($data['token']);
        $event->setData($data);
    }

    public function onJwtInvalidOrExpired($event): void
    {
        $response = $event->getResponse();
        $response->headers->clearCookie(self::COOKIE_NAME, self::COOKIE_PATH, null, true, true, self::COOKIE_SAMESITE);
    }
}
