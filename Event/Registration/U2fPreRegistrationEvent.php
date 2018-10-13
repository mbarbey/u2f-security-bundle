<?php

namespace Mbarbey\U2fSecurityBundle\Event\Registration;

use Symfony\Component\EventDispatcher\Event;
use Mbarbey\U2fSecurityBundle\Model\User\U2fUserInterface;
use Mbarbey\U2fSecurityBundle\Event\U2fEvents;

class U2fPreRegistrationEvent extends Event
{
    protected $user;

    protected $appId;

    protected $reason;

    protected $abort = false;

    public static function getName()
    {
        return U2fEvents::U2F_PRE_REGISTRATION;
    }

    public function __construct(U2fUserInterface $user, string $appId = null)
    {
        $this->setUser($user);
        $this->setAppId($appId);
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

    public function getAppId()
    {
        return $this->appId;
    }

    public function setAppId(string $appId = null)
    {
        $this->appId = $appId;
    }

    public function abort($reason = null)
    {
        $this->reason = $reason;
        $this->abort = true;
        $this->stopPropagation();

        return $this;
    }

    public function isAborted()
    {
        return $this->abort;
    }

    public function getReason()
    {
        return $this->reason;
    }
}