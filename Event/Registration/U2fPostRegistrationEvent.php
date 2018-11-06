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
use Mbarbey\U2fSecurityBundle\Model\Key\U2fKeyInterface;

/**
 * Post U2F registration event
 *
 * An event dispatched everytime the registration of a security key is performed, no matter if it was a success or a failure.
 *
 * This event contain the user who tried to register a key and *can* contain the key if the registration was a success.
 *
 * @author Michael Barbey <michael@barbey-family.ch>
 */

class U2fPostRegistrationEvent extends Event
{
    private $user;
    private $key;
    private $success = false;

    /**
     * Return the name of the event to use when dispatching this event.
     *
     * @return string
     */
    public static function getName()
    {
        return 'u2f.registration.after';
    }

    /**
     * @param U2fUserInterface $user    The user who tried to register a key
     * @param U2fKeyInterface $key      The security key which was registered
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
     * Return the user who tried to register a key, no matter if it was a success or not.
     *
     * @return U2fUserInterface
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * If the registration was a success, then return the key registered, else return nothing.
     *
     * @return U2fKeyInterface|NULL
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * Inform if the key registration was a success or a failure.
     *
     * @return bool
     */
    public function isSuccess()
    {
        return $this->success;
    }
}
