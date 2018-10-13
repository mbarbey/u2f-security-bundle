<?php

namespace Mbarbey\U2fSecurityBundle\Event\Authentication;

use Symfony\Component\EventDispatcher\Event;
use Mbarbey\U2fSecurityBundle\Model\User\U2fUserInterface;
use Mbarbey\U2fSecurityBundle\Event\U2fEvents;

class U2fPreAuthenticationEvent extends Event
{
    protected $appId;

    protected $user;

    protected $reason;

    protected $abort = false;

    public static function getName()
    {
        return U2fEvents::U2F_PRE_AUTHENTICATION;
    }

    public function __construct(string $appId, U2fUserInterface $user)
    {
        $this->setAppId($appId);
        $this->setUser($user);
    }

    public function getAppId()
    {
        return $this->appId;
    }

    public function setAppId(string $appId)
    {
        $this->appId = $appId;

        return $this;
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