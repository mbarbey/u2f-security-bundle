<?php

namespace Mbarbey\U2fSecurityBundle\Event\Registration;

use Symfony\Component\EventDispatcher\Event;
use Mbarbey\U2fSecurityBundle\Model\User\U2fUserInterface;
use Mbarbey\U2fSecurityBundle\Event\U2fEvents;

class U2fRegistrationFailureEvent extends Event
{
    protected $user;

    protected $error;

    public static function getName()
    {
        return U2fEvents::U2F_REGISTRATION_FAILURE;
    }

    public function __construct(U2fUserInterface $user, \Exception $error)
    {
        $this->setUser($user);
        $this->setError($error);
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

    public function setError(\Exception $error)
    {
        $this->error = $error;

        return $this;
    }
}
