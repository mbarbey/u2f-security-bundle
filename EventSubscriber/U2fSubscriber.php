<?php

/*
 * This file is part of the U2F Security bundle.
 *
 * (c) Michael Barbey <michael@barbey-family.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

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

/**
 * U2F event subscriber
 *
 * An event subscriber which will automatically detect when a user successfuly log in and define of the user
 * must be redirected it to the U2F authentication page or not.
 *
 * The listened events are :
 * - security.interactive_login : When a user successfully log in
 * - kernel.request             : When a page is requested
 *
 * @author Michael Barbey <michael@barbey-family.ch>
 */
class U2fSubscriber implements EventSubscriberInterface
{
    const U2F_SECURITY_KEY = 'u2f_must_validate';

    private $redirectToRoute;
    private $whitelistOfRoutes;

    private $router;
    private $session;
    private $dispatcher;

    /**
     * @param string $redirectToRoute               The route where the user must be redirect to perform the U2F authentication
     * @param array $whitelistOfRoutes              An array of routes to add to the whitelist
     * @param RouterInterface $router               The routing engine
     * @param SessionInterface $session             The session handler
     * @param EventDispatcherInterface $dispatcher  The event dispatcher
     */
    public function __construct($redirectToRoute, array $whitelistOfRoutes, RouterInterface $router, SessionInterface $session, EventDispatcherInterface $dispatcher)
    {
        $this->redirectToRoute = $redirectToRoute;
        $this->whitelistOfRoutes = $whitelistOfRoutes;
        $this->router = $router;
        $this->session = $session;
        $this->dispatcher = $dispatcher;
    }

    /**
     * This function is called when a user successfully log in with is basic credentials.
     *
     * This function will determine if the user must be redirected to the U2F authentication page or not.
     *
     * @param InteractiveLoginEvent $event
     */
    public function onSecurityInteractiveLogin(InteractiveLoginEvent $event): void
    {
        $user = $event->getAuthenticationToken()->getUser();

        /*
         * If the user has at least one security key linked to it account, he must be redirected
         * to the U2F authentication page.
         */
        if ($user instanceof U2fUserInterface && $user->getU2fKeys()->count()) {
            $authenticate = true;

            /*
             * We give a change to cancel this authentication request by dropping a bottle to the sea and waiting for
             * someone to respond.
             */
            if ($this->dispatcher->hasListeners(U2fAuthenticationRequiredEvent::getName())) {
                $shouldAuthenticate = new U2fAuthenticationRequiredEvent($user);
                $this->dispatcher->dispatch($shouldAuthenticate::getName(), $shouldAuthenticate);
                $authenticate = $shouldAuthenticate->mustAuthenticate();
            }

            /*
             * If the user must be redirected, we put a flag in his session.
             */
            if ($authenticate) {
                $event->getRequest()->getSession()->set(static::U2F_SECURITY_KEY, true);
            }
        }
    }

    /**
     * This function is called at every requests.
     *
     * This function will check if the user must be redirected and if the current route is not whitelisted. Then we return
     * a redirect response instead of the content the user expected.
     *
     * @param GetResponseEvent $event
     */
    public function onKernelRequest(GetResponseEvent $event): void
    {
        /*
         * Do nothing if the request isn't a master request and if the securtiy flag hasn't been planted
         * by the "onSecurityInteractiveLogin" function.
         */
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

            /*
             * If the user isn't on a whitelisted route, he is redirected to the authentication page.
             */
            if (!in_array($route, $whitelist)) {
                $counter = $this->session->get('u2f_registration_error_counter', -1) +1;
                $this->session->set('u2f_registration_error_counter', $counter);
                $event->setResponse(new RedirectResponse($this->router->generate($this->redirectToRoute)));
                $event->stopPropagation();
            }
        }
    }

    /**
     * Return the list of listened events
     *
     * @return array
     */
    public static function getSubscribedEvents(): array
    {
        return [
            SecurityEvents::INTERACTIVE_LOGIN => 'onSecurityInteractiveLogin',
            KernelEvents::REQUEST => 'onKernelRequest'
        ];
    }
}
