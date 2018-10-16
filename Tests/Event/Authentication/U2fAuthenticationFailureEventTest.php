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
    private $counter;

    public function setUp()
    {
        $this->user = $this->getMockForAbstractClass(U2fUser::class);
        $this->error = new \Exception();
        $this->counter = rand(10, 100);

        $this->event = new U2fAuthenticationFailureEvent($this->user, $this->error, $this->counter);
    }

    public function testName()
    {
        $name = 'u2f.authentication.failure';

        $this->assertEquals(U2fAuthenticationFailureEvent::getName(), $name);
        $this->assertEquals($this->event->getName(), $name);
    }

    public function testUser()
    {
        $this->assertEquals($this->event->getUser(), $this->user);
    }

    public function testError()
    {
        $this->assertEquals($this->event->getError(), $this->error);
    }

    public function testFailureCounter()
    {
        $this->assertEquals($this->event->getFailureCounter(), $this->counter);
    }
}
