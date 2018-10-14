<?php

/*
 * This file is part of the U2F Security bundle.
 *
 * (c) Michael Barbey <michael@barbey-family.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mbarbey\U2fSecurityBundle\Event\Registration;

use Symfony\Component\EventDispatcher\Event;
use Mbarbey\U2fSecurityBundle\Model\User\U2fUserInterface;

/**
 * U2F registration failure event
 *
 * An event dispatched when a user fail to register an U2F security key.
 *
 * This event contain the user and the exception which triggered the failure.
 *
 * @author Michael Barbey <michael@barbey-family.ch>
 */
class U2fRegistrationFailureEvent extends Event
{
    private $user;
    private $error;

    /**
     * Return the name of the event to use when dispatching this event.
     *
     * @return string
     */
    public static function getName(): string
    {
        return 'u2f.registration.failure';
    }

    /**
     * @param U2fUserInterface $user    The user who failed to register a key
     * @param \Exception $error         The exception which triggered the failure
     */
    public function __construct(U2fUserInterface $user, \Exception $error)
    {
        $this->user = $user;
        $this->error = $error;
    }

    /**
     * Return the user who failed to register a key.
     *
     * @return U2fUserInterface
     */
    public function getUser(): U2fUserInterface
    {
        return $this->user;
    }

    /**
     * Return the exception which triggered the failure
     *
     * @return \Exception
     */
    public function getError(): \Exception
    {
        return $this->error;
    }
}
