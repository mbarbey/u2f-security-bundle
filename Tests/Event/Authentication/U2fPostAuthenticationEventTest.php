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
use Mbarbey\U2fSecurityBundle\Model\Key\U2fKey;
use Mbarbey\U2fSecurityBundle\Event\Authentication\U2fPostAuthenticationEvent;

class U2fPostAuthenticationEventTest extends TestCase
{
    private $event;
    private $user;
    private $key;

    public function testName()
    {
        $name = 'u2f.authentication.after';

        $this->assertEquals(U2fPostAuthenticationEvent::getName(), $name);

        $this->setUpSuccess();
        $this->assertEquals($this->event->getName(), $name);

        $this->setUpFailure();
        $this->assertEquals($this->event->getName(), $name);
    }

    public function testUser()
    {
        $this->setUpSuccess();
        $this->assertEquals($this->event->getUser(), $this->user);

        $this->setUpFailure();
        $this->assertEquals($this->event->getUser(), $this->user);
    }

    public function testKey()
    {
        $this->setUpSuccess();
        $this->assertEquals($this->event->getKey(), $this->key);

        $this->setUpFailure();
        $this->assertNull($this->event->getKey());
    }

    public function testIsSuccess()
    {
        $this->setUpSuccess();
        $this->assertTrue($this->event->isSuccess());

        $this->setUpFailure();
        $this->assertFalse($this->event->isSuccess());
    }

    private function setUpFailure()
    {
        $this->user = $this->getMockForAbstractClass(U2fUser::class);
        $this->key = null;

        $this->event = new U2fPostAuthenticationEvent($this->user);
    }

    private function setUpSuccess()
    {
        $this->user = $this->getMockForAbstractClass(U2fUser::class);
        $this->key = $this->getMockForAbstractClass(U2fKey::class);

        $this->event = new U2fPostAuthenticationEvent($this->user, $this->key);
    }
}
