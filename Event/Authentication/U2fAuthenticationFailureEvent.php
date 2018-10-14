<?php

/*
 * This file is part of the U2F Security bundle.
 *
 * (c) Michael Barbey <michael@barbey-family.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mbarbey\U2fSecurityBundle\Event\Authentication;

use Symfony\Component\EventDispatcher\Event;
use Mbarbey\U2fSecurityBundle\Model\User\U2fUserInterface;
use Mbarbey\U2fSecurityBundle\Event\U2fEvents;

/**
 * U2F authentication failure event
 *
 * An event dispatched when a user fail to authenticate with it U2F security key.
 *
 * This event contain the user, an error message and the number of time the user failed to authenticate consecutively.
 *
 * @author Michael Barbey <michael@barbey-family.ch>
 */
class U2fAuthenticationFailureEvent extends Event
{
    private $user;
    private $error;
    private $failureCounter;

    /**
     * Return the name of the event to use when dispatching this event.
     *
     * @return string
     */
    public static function getName(): string
    {
        return U2fEvents::U2F_AUTHENTICATION_FAILURE;
    }

    /**
     * @param U2fUserInterface $user    The user who failed to authenticate
     * @param string $error             An error message explaining why he failed
     * @param int $failureCounter       The number of consecutive fails of this user
     */
    public function __construct(U2fUserInterface $user, string $error, int $failureCounter = 1)
    {
        $this->user = $user;
        $this->error = $error;
        $this->failureCounter = $failureCounter;
    }

    /**
     * Return the user who failed to authenticate.
     *
     * @return U2fUserInterface
     */
    public function getUser(): U2fUserInterface
    {
        return $this->user;
    }

    /**
     * Return an error message explaining why the authentication failed.
     *
     * @return string
     */
    public function getError(): string
    {
        return $this->error;
    }

    /**
     * Return the number of consecutive times the user failed to authenticate.
     *
     * @return int
     */
    public function getFailureCounter(): int
    {
        return $this->failureCounter;
    }
}
