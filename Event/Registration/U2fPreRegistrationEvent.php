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
 * Pre U2F registration event
 *
 * An event dispatched right before performing a key registration.
 *
 * This event is the last chance to cancel the registration before it will be asked to the user.
 *
 * This event contain the user who asked to register a key and the appId (protocol + domain name).
 * You can call the "abort" function if you want to cancel the authentication request.
 *
 * @author Michael Barbey <michael@barbey-family.ch>
 */
class U2fPreRegistrationEvent extends Event
{
    private $user;
    private $appId;
    private $reason;
    private $abort = false;

    /**
     * Return the name of the event to use when dispatching this event.
     *
     * @return string
     */
    public static function getName(): string
    {
        return 'u2f.registration.before';
    }

    /**
     * @param string $appId             The appId for the registration
     * @param U2fUserInterface $user    The user who will register a security key
     */
    public function __construct(string $appId, U2fUserInterface $user)
    {
        $this->appId = $appId;
        $this->user = $user;
    }

    /**
     * Return the user who asked to register a securtiy key
     *
     * @return U2fUserInterface
     */
    public function getUser(): U2fUserInterface
    {
        return $this->user;
    }

    /**
     * Return the appId of the registration.
     * The appId *must* be the protocol + your domain name (ex: https://example.com)
     *
     * @return string
     */
    public function getAppId(): string
    {
        return $this->appId;
    }

    /**
     * Cancel the kex registration request. You can attach a reason why it was canceled.
     *
     * Calling this function stop the propagation of the event.
     *
     * @param string $reason
     */
    public function abort($reason = null): void
    {
        $this->reason = $reason;
        $this->abort = true;
        $this->stopPropagation();
    }

    /**
     * Return if the key registration request must be aborted or not.
     *
     * @return bool
     */
    public function isAborted(): bool
    {
        return $this->abort;
    }

    /**
     * Return the reason why the key registration request was aborted.
     *
     * @return string|NULL
     */
    public function getReason(): ?string
    {
        return $this->reason;
    }
}