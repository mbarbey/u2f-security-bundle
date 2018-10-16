<?php

/*
 * This file is part of the U2F Security bundle.
 *
 * (c) Michael Barbey <michael@barbey-family.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mbarbey\U2fSecurityBundle\Tests\Event\Authentication;

use Symfony\Bundle\FrameworkBundle\Tests\TestCase;
use Mbarbey\U2fSecurityBundle\Model\User\U2fUser;
use Mbarbey\U2fSecurityBundle\Event\Authentication\U2fAuthenticationRequiredEvent;

class U2fAuthenticationRequiredEventTest extends TestCase
{
    private $event;
    private $user;

    public function setUp()
    {
        $this->user = new U2fUser();

        $this->event = new U2fAuthenticationRequiredEvent($this->user);
    }

    public function testName()
    {
        $this->assertEquals('u2f.authentication.required', U2fAuthenticationRequiredEvent::getName());
        $this->assertEquals('u2f.authentication.required', $this->event->getName());
    }

    public function testUser()
    {
        $this->assertEquals($this->user, $this->event->getUser());
    }

    public function testMustAuthenticate()
    {
        $this->assertTrue($this->event->mustAuthenticate());
    }

    public function testAbort()
    {
        $this->event->abort();
        $this->assertFalse($this->event->mustAuthenticate());
    }
}
