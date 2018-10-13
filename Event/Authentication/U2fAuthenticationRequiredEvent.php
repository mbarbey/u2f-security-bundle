<?php

namespace Mbarbey\U2fSecurityBundle\Event\Authentication;

use Symfony\Component\EventDispatcher\Event;
use Mbarbey\U2fSecurityBundle\Model\User\U2fUserInterface;
use Mbarbey\U2fSecurityBundle\Event\U2fEvents;

class U2fAuthenticationRequiredEvent extends Event
{
    protected $user;

    protected $mustAuthenticate = true;

    public static function getName()
    {
        return U2fEvents::U2F_AUTHENTICATION_REQUIRED;
    }

    public function __construct(U2fUserInterface $user)
    {
        $this->setUser($user);
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

    public function abort()
    {
        $this->mustAuthenticate = false;
        $this->stopPropagation();
    }

    public function mustAuthenticate()
    {
        return $this->mustAuthenticate;
    }
}