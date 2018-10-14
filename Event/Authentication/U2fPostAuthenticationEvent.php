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
use Mbarbey\U2fSecurityBundle\Model\Key\U2fKeyInterface;
use Mbarbey\U2fSecurityBundle\Event\U2fEvents;

/**
 * Post U2F authentication event
 *
 * An event dispatched everytime an authentication is performed, no matter if it was a success or a failure.
 *
 * This event contain the user who tried to authenticate and *can* contain the key if the authentication was a success.
 * If the key is provided, it mean that the authentication was a success.
 * The counter of the key is already updated.
 *
 * @author Michael Barbey <michael@barbey-family.ch>
 */
class U2fPostAuthenticationEvent extends Event
{
    private $user;
    private $key;
    private $success = false;

    /**
     * Return the name of the event to use when dispatching this event.
     *
     * @return string
     */
    public static function getName(): string
    {
        return U2fEvents::U2F_POST_AUTHENTICATION;
    }

    /**
     * @param U2fUserInterface $user
     * @param U2fKeyInterface $key
     */
    public function __construct(U2fUserInterface $user, U2fKeyInterface $key = null)
    {
        $this->user = $user;

        if ($key) {
            $this->key = $key;
            $this->success = true;
        }
    }

    /**
     * Return the user who tried to authenticate, no matter if it was a success or not.
     *
     * @return U2fUserInterface
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * If the authentication was a success, then return the key used, else return nothing.
     * The counter of the key is already updated.
     *
     * @return U2fKeyInterface|NULL
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * Inform if the authentication was a success or a failure.
     *
     * @return bool
     */
    public function isSuccess(): bool
    {
        return $this->success;
    }
}
