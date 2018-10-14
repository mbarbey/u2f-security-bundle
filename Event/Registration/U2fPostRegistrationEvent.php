<?php

namespace Mbarbey\U2fSecurityBundle\Event\Registration;

use Symfony\Component\EventDispatcher\Event;
use Mbarbey\U2fSecurityBundle\Model\User\U2fUserInterface;
use Mbarbey\U2fSecurityBundle\Model\Key\U2fKeyInterface;
use Mbarbey\U2fSecurityBundle\Event\U2fEvents;

class U2fPostRegistrationEvent extends Event
{
    protected $user;

    protected $key;

    protected $success = false;

    public static function getName()
    {
        return U2fEvents::U2F_POST_REGISTRATION;
    }

    public function __construct(U2fUserInterface $user, U2fKeyInterface $key = null)
    {
        $this->user = $user;

        if ($key) {
            $this->key = $key;
            $this->success = true;
        }
    }

    public function getUser()
    {
        return $this->user;
    }

    public function getKey()
    {
        return $this->key;
    }

    public function isSuccess()
    {
        return $this->success;
    }
}
