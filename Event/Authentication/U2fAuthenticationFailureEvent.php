<?php

namespace Mbarbey\U2fSecurityBundle\Event\Authentication;

use Symfony\Component\EventDispatcher\Event;
use Mbarbey\U2fSecurityBundle\Model\User\U2fUserInterface;
use Mbarbey\U2fSecurityBundle\Event\U2fEvents;

class U2fAuthenticationFailureEvent extends Event
{
    protected $user;

    protected $error;

    protected $failureCounter;

    public static function getName()
    {
        return U2fEvents::U2F_AUTHENTICATION_FAILURE;
    }

    public function __construct(U2fUserInterface $user, string $error, int $failureCounter = 1)
    {
        $this->setUser($user);
        $this->setError($error);
        $this->setFailureCounter($failureCounter);
    }

    public function getUser()
    {
        return $this->user;
    }

    public function setUser(U2fUserInterface $user)
    {
        $this->user = $user;

        return $this;
    }

    public function getError()
    {
        return $this->error;
    }

    public function setError($error)
    {
        $this->error = $error;

        return $this;
    }

    public function getFailureCounter()
    {
        return $this->failureCounter;
    }

    public function setFailureCounter(int $failureCounter)
    {
        $this->failureCounter = $failureCounter;

        return $this;
    }
}
