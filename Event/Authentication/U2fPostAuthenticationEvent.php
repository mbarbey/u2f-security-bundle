<?php

namespace Mbarbey\U2fSecurityBundle\Event\Authentication;

use Symfony\Component\EventDispatcher\Event;
use Mbarbey\U2fSecurityBundle\Model\User\U2fUserInterface;
use Mbarbey\U2fSecurityBundle\Model\Key\U2fKeyInterface;
use Mbarbey\U2fSecurityBundle\Event\U2fEvents;

class U2fPostAuthenticationEvent extends Event
{
    protected $user;

    protected $key;

    public static function getName()
    {
        return U2fEvents::U2F_POST_AUTHENTICATION;
    }

    public function __construct(U2fUserInterface $user, U2fKeyInterface $key)
    {
        $this->setUser($user);
        $this->setKey($key);
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

    public function getKey()
    {
        return $this->key;
    }

    public function setKey(U2fKeyInterface $key)
    {
        $this->key = $key;

        return $this;
    }
}
