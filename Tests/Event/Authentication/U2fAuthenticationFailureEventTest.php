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
use Mbarbey\U2fSecurityBundle\Event\Authentication\U2fAuthenticationFailureEvent;
use Mbarbey\U2fSecurityBundle\Model\User\U2fUser;

class U2fAuthenticationFailureEventTest extends TestCase
{
    private $event;
    private $user;
    private $error;

    public function setUp()
    {
        $this->user = new U2fUser();
        $this->error = new \Exception();

        $this->event = new U2fAuthenticationFailureEvent($this->user, $this->error, 33);
    }

    public function testName()
    {
        $this->assertEquals('u2f.authentication.failure', U2fAuthenticationFailureEvent::getName());
        $this->assertEquals('u2f.authentication.failure', $this->event->getName());
    }

    public function testUser()
    {
        $this->assertEquals($this->user, $this->event->getUser());
    }

    public function testError()
    {
        $this->assertEquals($this->error, $this->event->getError());
    }

    public function testFailureCounter()
    {
        $this->assertEquals(33, $this->event->getFailureCounter());
    }
}
