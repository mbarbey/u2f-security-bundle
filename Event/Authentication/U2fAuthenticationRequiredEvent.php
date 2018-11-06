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

/**
 * U2F authentication required event
 *
 * An event dispatcher when a user successfully log-in with it basic username/password and own one or more
 * U2F security key(s).
 *
 * This event aim to allow subscribers to define if this user must not being forced to authenticate with a second factor.
 *
 * This event contain the user and an abort function allowing to cancel the request for a fully U2F authentication.
 *
 * @author Michael Barbey <michael@barbey-family.ch>
 */

class U2fAuthenticationRequiredEvent extends Event
{
    private $user;
    private $mustAuthenticate = true;

    /**
     * Return the name of the event to use when dispatching this event.
     *
     * @return string
     */
    public static function getName()
    {
        return 'u2f.authentication.required';
    }

    /**
     * @param U2fUserInterface $user    The user who will be requested to authenticate with a security key
     */
    public function __construct(U2fUserInterface $user)
    {
        $this->user = $user;
    }

    /**
     * Return the user who successfully logged-in with it basic username/password.
     *
     * @return U2fUserInterface
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Inform that even if this user has one or more U2F security key(s), it must be redirected
     * to the U2F authentication page.
     *
     * Calling this function stop the propagation of the event.
     */
    public function abort()
    {
        $this->mustAuthenticate = false;
        $this->stopPropagation();
    }

    /**
     * Return if the U2F authentication request must be aborted or not.
     *
     * @return bool
     */
    public function mustAuthenticate()
    {
        return $this->mustAuthenticate;
    }
}
