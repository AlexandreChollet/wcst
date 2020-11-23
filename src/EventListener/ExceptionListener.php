<?php

namespace App\EventListener;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\RouterInterface;

class ExceptionListener
{
    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @var RouterInterface $router
     */
    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
    }

    /**
     * @param ExceptionEvent $event
     */
    public function onKernelException(ExceptionEvent $event)
    {
        if (!$event->getThrowable() instanceof NotFoundHttpException) {
            return;
        }

        $event->setResponse(new RedirectResponse($this->router->generate('test')));
    }
}