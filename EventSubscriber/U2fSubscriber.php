<?php

namespace Mbarbey\U2fSecurityBundle\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\Security\Http\SecurityEvents;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Mbarbey\U2fSecurityBundle\Event\Authentication\U2fAuthenticationRequiredEvent;
use Mbarbey\U2fSecurityBundle\Model\User\U2fUserInterface;

class U2fSubscriber implements EventSubscriberInterface
{
    const U2F_SECURITY_KEY = 'u2f_must_validate';

    private $redirectToRoute;
    private $whitelistOfRoutes;

    private $router;
    private $session;
    private $dispatcher;

    public function __construct($redirectToRoute, array $whitelistOfRoutes, RouterInterface $router, SessionInterface $session, EventDispatcherInterface $dispatcher)
    {
        $this->redirectToRoute = $redirectToRoute;
        $this->whitelistOfRoutes = $whitelistOfRoutes;

        $this->router = $router;
        $this->session = $session;
        $this->dispatcher = $dispatcher;
    }

    public function onSecurityInteractiveLogin(InteractiveLoginEvent $event)
    {
        $user = $event->getAuthenticationToken()->getUser();
        if ($user instanceof U2fUserInterface && $user->getU2fKeys()->count()) {
            $authenticate = true;
            if ($this->dispatcher->hasListeners(U2fAuthenticationRequiredEvent::getName())) {
                $shouldAuthenticate = new U2fAuthenticationRequiredEvent($user);
                $this->dispatcher->dispatch($shouldAuthenticate::getName(), $shouldAuthenticate);
                $authenticate = $shouldAuthenticate->mustAuthenticate();
            }
            if ($authenticate) {
                $event->getRequest()->getSession()->set(static::U2F_SECURITY_KEY, true);
            }
        }
    }

    public function onKernelRequest(GetResponseEvent $event)
    {
        if (
            $event->isMasterRequest() &&
            $event->getRequest()->getSession()->get(static::U2F_SECURITY_KEY)
            ) {
                $route = $event->getRequest()->get('_route');
                $whitelist = array_merge(
                    [
                        $this->redirectToRoute
                    ],
                    $this->whitelistOfRoutes,
                    [
                        '_wdt',
                        '_profiler_home',
                        '_profiler_search',
                        '_profiler_search_bar',
                        '_profiler_phpinfo',
                        '_profiler_search_results',
                        '_profiler_open_file',
                        '_profiler',
                        '_profiler_router',
                        '_profiler_exception',
                        '_profiler_exception_css'
                    ]
                    );

                if (!in_array($route, $whitelist)) {
                    $event->setResponse(new RedirectResponse($this->router->generate($this->redirectToRoute)));
                }
            }
    }

    public static function getSubscribedEvents()
    {
        return [
            SecurityEvents::INTERACTIVE_LOGIN => 'onSecurityInteractiveLogin',
            KernelEvents::REQUEST => 'onKernelRequest'
        ];
    }
}
