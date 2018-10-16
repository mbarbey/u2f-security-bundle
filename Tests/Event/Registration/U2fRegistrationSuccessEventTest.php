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
use Mbarbey\U2fSecurityBundle\Model\Key\U2fKey;
use Mbarbey\U2fSecurityBundle\Event\Registration\U2fRegistrationSuccessEvent;

class U2fRegistrationSuccessEventTest extends TestCase
{
    private $event;
    private $user;
    private $key;

    public function setUp()
    {
        $this->user = $this->getMockForAbstractClass(U2fUser::class);
        $this->key = $this->getMockForAbstractClass(U2fKey::class);

        $this->event = new U2fRegistrationSuccessEvent($this->user, $this->key);
    }

    public function testName()
    {
        $name = 'u2f.registration.success';

        $this->assertEquals(U2fRegistrationSuccessEvent::getName(), $name);
        $this->assertEquals($this->event->getName(), $name);
    }

    public function testUser()
    {
        $this->assertEquals($this->event->getUser(), $this->user);
    }

    public function testKey()
    {
        $this->assertEquals($this->event->getKey(), $this->key);
    }
}
