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
 * U2F registration success event
 *
 * An event dispatched when a user successfully register an U2F security key.
 *
 * This event contain the user and the newly registered key.
 *
 * @author Michael Barbey <michael@barbey-family.ch>
 */
class U2fRegistrationSuccessEvent extends Event
{
    private $user;
    private $key;

    /**
     * Return the name of the event to use when dispatching this event.
     *
     * @return string
     */
    public static function getName()
    {
        return 'u2f.registration.success';
    }

    /**
     * @param U2fUserInterface $user    The user who successfully registered a key
     * @param U2fKeyInterface $key      The securtiy key which was registered
     */
    public function __construct(U2fUserInterface $user, U2fKeyInterface $key)
    {
        $this->user = $user;
        $this->key = $key;
    }

    /**
     * Return the user who successfully registered a new security key
     *
     * @return U2fUserInterface
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Return the newly registered securtiy key
     *
     * @return U2fKeyInterface
     */
    public function getKey()
    {
        return $this->key;
    }
}
