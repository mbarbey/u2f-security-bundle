<?php

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

class U2fSecurity
{
    private $session;
    private $dispatcher;

    public function __construct(SessionInterface $session, EventDispatcherInterface $dispatcher)
    {
        $this->session = $session;
        $this->dispatcher = $dispatcher;
    }

    public function canRegister(U2fUserInterface $user, $appId = null)
    {
        $event = new U2fPreRegistrationEvent($user, $appId);
        $this->dispatcher->dispatch($event::getName(), $event);

        return $event;
    }

    public function createRegistration($appId)
    {
        $registrationData = U2FServer::makeRegistration($appId);
        $this->session->set('registrationRequest', $registrationData['request']);

        $jsRequest = $registrationData['request'];
        $jsSignatures = json_encode($registrationData['signatures']);

        return ['request' => $jsRequest, 'signatures' => $jsSignatures];
    }

    public function validateRegistration(U2fUserInterface $user, U2fRegistrationInterface $registration, U2fKeyInterface $key)
    {
        $u2fRequest = $this->session->get('registrationRequest');
        $u2fResponse = (object)json_decode($registration->getResponse(), true);

        try {
            $validatedRegistration = U2FServer::register($u2fRequest, $u2fResponse);
            foreach ($user->getU2fKeys() as $existingKey) {
                if ($existingKey->getCertificate() == $validatedRegistration->getCertificate()) {
                    throw new U2FException('Key already registered', 4);
                }
            }
        } catch (\Exception $e) {
            $this->dispatcher->dispatch(U2fRegistrationFailureEvent::getName(), new U2fRegistrationFailureEvent($user, $e));
            throw $e;
        }

        $key->setCertificate($validatedRegistration->getCertificate());
        $key->setCounter($validatedRegistration->getCounter());
        $key->setKeyHandle($validatedRegistration->getKeyHandle());
        $key->setPublicKey($validatedRegistration->getPublicKey());

        $this->dispatcher->dispatch(U2fRegistrationSuccessEvent::getName(), new U2fRegistrationSuccessEvent($user, $key));

        $this->session->remove('registrationRequest');

        $this->dispatcher->dispatch(U2fPostRegistrationEvent::getName(), new U2fPostRegistrationEvent($user, $key));
    }

    public function canAuthenticate($appId, U2fUserInterface $user)
    {
        $event = new U2fPreAuthenticationEvent($appId, $user);
        $this->dispatcher->dispatch($event->getName(), $event);

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
            $counter = $this->session->get('u2f_registration_error_counter', 0) +1;
            $error = new U2fAuthenticationFailureEvent($user, $e->getMessage(), $counter);
            $this->dispatcher->dispatch($error->getName(), $error);

            throw $e;
        }

        $this->dispatcher->dispatch(U2fAuthenticationSuccessEvent::getName(), new U2fAuthenticationSuccessEvent($user, $updatedKey));

        $this->session->remove('authenticationRequest');

        if ($this->session->has('u2f_registration_error_counter')) {
            $this->session->remove('u2f_registration_error_counter');
        }

        $this->dispatcher->dispatch(U2fPostAuthenticationEvent::getName(), new U2fPostAuthenticationEvent($user, $updatedKey));

        return $updatedKey;
    }
}
