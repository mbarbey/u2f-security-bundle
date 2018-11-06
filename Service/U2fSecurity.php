<?php

/*
 * This file is part of the U2F Security bundle.
 *
 * (c) Michael Barbey <michael@barbey-family.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mbarbey\U2fSecurityBundle\Service;

use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Samyoul\U2F\U2FServer\U2FServer;
use Samyoul\U2F\U2FServer\U2FException;
use Mbarbey\U2fSecurityBundle\Model\U2fRegistration\U2fRegistrationInterface;
use Mbarbey\U2fSecurityBundle\Model\User\U2fUserInterface;
use Mbarbey\U2fSecurityBundle\Model\U2fAuthentication\U2fAuthenticationInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Mbarbey\U2fSecurityBundle\Event\Authentication\U2fAuthenticationSuccessEvent;
use Mbarbey\U2fSecurityBundle\Event\Registration\U2fPreRegistrationEvent;
use Mbarbey\U2fSecurityBundle\Event\Registration\U2fRegistrationSuccessEvent;
use Mbarbey\U2fSecurityBundle\Event\Registration\U2fPostRegistrationEvent;
use Mbarbey\U2fSecurityBundle\Event\Registration\U2fRegistrationFailureEvent;
use Mbarbey\U2fSecurityBundle\Event\Authentication\U2fPreAuthenticationEvent;
use Mbarbey\U2fSecurityBundle\Event\Authentication\U2fAuthenticationFailureEvent;
use Mbarbey\U2fSecurityBundle\Event\Authentication\U2fPostAuthenticationEvent;
use Mbarbey\U2fSecurityBundle\Model\Key\U2fKeyInterface;
use Mbarbey\U2fSecurityBundle\EventSubscriber\U2fSubscriber;

/**
 * U2F security engine
 *
 * This class allow to manage U2F exchanges by using the following structure :
 * - Create a registration request
 * - Validate a registration response
 * - Create an authentication request
 * - Validate an authentication response
 *
 * @author Michael Barbey <michael@barbey-family.ch>
 */
class U2fSecurity
{
    private $session;
    private $dispatcher;

    /**
     * @param SessionInterface $session                 The session manager
     * @param EventDispatcherInterface $dispatcher      The event dispatcher
     */
    public function __construct(SessionInterface $session, EventDispatcherInterface $dispatcher)
    {
        $this->session = $session;
        $this->dispatcher = $dispatcher;
    }

    /**
     * If you want to add an extra layer of personalization to your system, you can call this function from your controller.
     * This function will send a U2fPreRegistrationEvent and check if any event listener want to cancel the current
     * registration process.
     *
     * It will return the dispatched event, so you can check if you should abort the registration or not.
     *
     * @param U2fUserInterface $user    The user which want to register a security key
     * @param string $appId             The appId (must be the HTTP protocol and your domain name (ex: https://example.com)
     * @return U2fPreRegistrationEvent  The event dispathed and updated by event listeners
     */
    public function canRegister(U2fUserInterface $user, $appId)
    {
        $event = new U2fPreRegistrationEvent($appId, $user);

        if ($this->dispatcher->hasListeners($event->getName())) {
            $this->dispatcher->dispatch($event::getName(), $event);
        }

        return $event;
    }

    /**
     * Create a registration request array which will be used to communicate with the U2F security key.
     *
     * @param string $appId     The appId (must be the HTTP protocol and your domain name (ex: https://example.com)
     * @return array            The array containing all the data required for the authentication
     */
    public function createRegistration($appId)
    {
        $registrationData = U2FServer::makeRegistration($appId);
        $this->session->set('registrationRequest', $registrationData['request']);

        return ['request' => $registrationData['request'], 'signatures' => json_encode($registrationData['signatures'])];
    }

    /**
     * Validate the given U2F registration response and fill the given key with the correct data.
     *
     * @param U2fUserInterface $user                    The user which want to register a security key
     * @param U2fRegistrationInterface $registration    The registration response
     * @param U2fKeyInterface $key                      The security key to fill with valid data
     *
     * @throws \Exception                               If there is an error, an exception is thrown to explain what went wrong
     */
    public function validateRegistration(U2fUserInterface $user, U2fRegistrationInterface $registration, U2fKeyInterface $key)
    {
        $u2fRequest = $this->session->get('registrationRequest');
        $u2fResponse = (object)json_decode($registration->getResponse(), true);

        try {
            /*
             * Check if the response is correct
             */
            $validatedRegistration = U2FServer::register($u2fRequest, $u2fResponse);

            /*
             * Check if the key hasn't already been registered
             */
            foreach ($user->getU2fKeys() as $existingKey) {
                if ($existingKey->getCertificate() == $validatedRegistration->getCertificate()) {
                    throw new U2FException('Key already registered', 4);
                }
            }
        } catch (\Exception $e) {
            /*
             * In case of error, dispatch a failure-registration event and a post-registration event
             */
            if ($this->dispatcher->hasListeners(U2fRegistrationFailureEvent::getName())) {
                $this->dispatcher->dispatch(U2fRegistrationFailureEvent::getName(), new U2fRegistrationFailureEvent($user, $e));
            }
            if ($this->dispatcher->hasListeners(U2fPostRegistrationEvent::getName())) {
                $this->dispatcher->dispatch(U2fPostRegistrationEvent::getName(), new U2fPostRegistrationEvent($user));
            }
            throw $e;
        }

        /*
         * If everything went good, we fill the given key with correct data and dispatch both a success-registration event
         * and a post-registration event
         */
        $key->setCertificate($validatedRegistration->getCertificate());
        $key->setCounter((int)$validatedRegistration->getCounter());
        $key->setKeyHandle($validatedRegistration->getKeyHandle());
        $key->setPublicKey($validatedRegistration->getPublicKey());

        if ($this->dispatcher->hasListeners(U2fRegistrationSuccessEvent::getName())) {
            $this->dispatcher->dispatch(U2fRegistrationSuccessEvent::getName(), new U2fRegistrationSuccessEvent($user, $key));
        }

        $this->session->remove('registrationRequest');

        if ($this->dispatcher->hasListeners(U2fPostRegistrationEvent::getName())) {
            $this->dispatcher->dispatch(U2fPostRegistrationEvent::getName(), new U2fPostRegistrationEvent($user, $key));
        }
    }

    public function canAuthenticate($appId, U2fUserInterface $user)
    {
        $event = new U2fPreAuthenticationEvent($appId, $user);

        if ($this->dispatcher->hasListeners($event->getName())) {
            $this->dispatcher->dispatch($event->getName(), $event);
        }

        if ($event->isAborted()) {
            $this->session->remove('authenticationRequest');
        }

        return $event;
    }

    public function createAuthentication($appId, U2fUserInterface $user)
    {
        $authenticationRequest = U2FServer::makeAuthentication($user->getU2fKeys()->toArray(), $appId);
        $this->session->set('authenticationRequest', $authenticationRequest);

        return [
            'appId' => $appId,
            'version' => U2FServer::VERSION,
            'challenge' => $authenticationRequest[0]->challenge(),
            'registeredKeys' => json_encode($authenticationRequest)
        ];
    }

    public function validateAuthentication(U2fUserInterface $user, U2fAuthenticationInterface $authentication)
    {
        try {
            $updatedKey = U2FServer::authenticate(
                $this->session->get('authenticationRequest'),
                $user->getU2fKeys()->toArray(),
                json_decode($authentication->getResponse())
            );
        } catch (\Exception $e) {
            if ($this->dispatcher->hasListeners(U2fAuthenticationFailureEvent::getName())) {
                $counter = $this->session->get('u2f_registration_error_counter', 0) +1;
                $this->session->set('u2f_registration_error_counter', $counter);
                $this->dispatcher->dispatch(U2fAuthenticationFailureEvent::getName(), new U2fAuthenticationFailureEvent($user, $e, $counter));
            }
            if ($this->dispatcher->hasListeners(U2fPostAuthenticationEvent::getName())) {
                $this->dispatcher->dispatch(U2fPostAuthenticationEvent::getName(), new U2fPostAuthenticationEvent($user));
            }

            throw $e;
        }

        if ($this->dispatcher->hasListeners(U2fAuthenticationSuccessEvent::getName())) {
            $this->dispatcher->dispatch(U2fAuthenticationSuccessEvent::getName(), new U2fAuthenticationSuccessEvent($user, $updatedKey));
        }

        $this->stopRequestingAuthentication();

        if ($this->dispatcher->hasListeners(U2fPostAuthenticationEvent::getName())) {
            $this->dispatcher->dispatch(U2fPostAuthenticationEvent::getName(), new U2fPostAuthenticationEvent($user, $updatedKey));
        }

        return $updatedKey;
    }

    public function stopRequestingAuthentication()
    {
        if ($this->session->has(U2fSubscriber::U2F_SECURITY_KEY)) {
            $this->session->remove(U2fSubscriber::U2F_SECURITY_KEY);
        }

        $this->session->remove('authenticationRequest');

        if ($this->session->has('u2f_registration_error_counter')) {
            $this->session->remove('u2f_registration_error_counter');
        }
    }
}
