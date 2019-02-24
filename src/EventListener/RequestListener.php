<?php

namespace App\EventListener;

use App\Helpers\Authenticator;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;

class RequestListener
{
    /** @var Authenticator */
    private $authenticator;

    /**
     * @param Authenticator $authenticator
     */
    public function __construct(Authenticator $authenticator)
    {
        $this->authenticator = $authenticator;
    }

    /**
     * @param GetResponseEvent $event
     */
    public function onKernelRequest(GetResponseEvent $event)
    {
        $route = $event->getRequest()->attributes->get("_route");

        if ("app_user_login" === $route) {
            return;
        }

        $token         = $event->getRequest()->headers->get('token', null);
        $errorResponse = new JsonResponse(['message' => "You don't have enough access."]);

        // Terminate request and return access denied response if there is no toke in header
        if (!$token) {
            return $event->setResponse($errorResponse);
        }

        // Terminate request and return access denied response if token is not valid
        if (!$this->authenticator->isAuthenticated($token)) {
            return $event->setResponse($errorResponse);
        }
    }
}
