<?php

/*
 * This file is part of the U2F Security bundle.
 *
 * (c) Michael Barbey <michael@barbey-family.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mbarbey\U2fSecurityBundle\Tests\Event\Registration;

use Symfony\Bundle\FrameworkBundle\Tests\TestCase;
use Mbarbey\U2fSecurityBundle\Model\User\U2fUser;
use Mbarbey\U2fSecurityBundle\Event\Registration\U2fRegistrationFailureEvent;

class U2fRegistrationFailureEventTest extends TestCase
{
    private $event;
    private $user;
    private $error;

    public function setUp()
    {
        $this->user = $this->getMockForAbstractClass(U2fUser::class);
        $this->error = new \Exception();

        $this->event = new U2fRegistrationFailureEvent($this->user, $this->error);
    }

    public function testName()
    {
        $name = 'u2f.registration.failure';

        $this->assertEquals(U2fRegistrationFailureEvent::getName(), $name);
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
}
