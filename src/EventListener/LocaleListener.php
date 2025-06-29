<?php

declare(strict_types=1);

namespace App\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class LocaleListener implements EventSubscriberInterface
{
    /**
     * @var string
     */
    private string $defaultLocale;

    public function __construct()
    {
        $this->defaultLocale = 'en';
    }

    /**
     * @param RequestEvent $event
     */
    public function onKernelRequest(RequestEvent $event): void
    {
        $request = $event->getRequest();
        if (!$request->hasPreviousSession()) {
            return;
        }

        $locale = $request->attributes->get('_locale');
        if (empty($locale)) {
            $request->setLocale($request->getSession()->get('_locale', $this->defaultLocale));
        } else {
            $request->getSession()->set('_locale', $locale);
        }
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => [['onKernelRequest', 17]],
        ];
    }
}
