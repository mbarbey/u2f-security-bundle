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
use Mbarbey\U2fSecurityBundle\Event\Authentication\U2fPreAuthenticationEvent;

class U2fPreAuthenticationEventTest extends TestCase
{
    private $appId;
    private $event;
    private $user;
    private $reason;

    public function testName()
    {
        $name = 'u2f.authentication.before';

        $this->assertEquals(U2fPreAuthenticationEvent::getName(), $name);

        $this->setUpAllowed();
        $this->assertEquals($this->event->getName(), $name);

        $this->setUpDeniedWithReason();
        $this->assertEquals($this->event->getName(), $name);

        $this->setUpDeniedWithoutReason();
        $this->assertEquals($this->event->getName(), $name);
    }

    public function testAppId()
    {
        $this->setUpAllowed();
        $this->assertEquals($this->event->getAppId(), $this->appId);

        $this->setUpDeniedWithReason();
        $this->assertEquals($this->event->getAppId(), $this->appId);

        $this->setUpDeniedWithoutReason();
        $this->assertEquals($this->event->getAppId(), $this->appId);
    }

    public function testUser()
    {
        $this->setUpAllowed();
        $this->assertEquals($this->event->getUser(), $this->user);

        $this->setUpDeniedWithReason();
        $this->assertEquals($this->event->getUser(), $this->user);

        $this->setUpDeniedWithoutReason();
        $this->assertEquals($this->event->getUser(), $this->user);
    }

    public function testIsAborted()
    {
        $this->setUpAllowed();
        $this->assertFalse($this->event->isAborted());

        $this->setUpDeniedWithReason();
        $this->assertTrue($this->event->isAborted());

        $this->setUpDeniedWithoutReason();
        $this->assertTrue($this->event->isAborted());
    }

    public function testReason()
    {
        $this->setUpAllowed();
        $this->assertNull($this->event->getReason());

        $this->setUpDeniedWithReason();
        $this->assertEquals($this->event->getReason(), $this->reason);

        $this->setUpDeniedWithoutReason();
        $this->assertNull($this->event->getReason());
    }

    private function setUpAllowed()
    {
        $this->user = $this->getMockForAbstractClass(U2fUser::class);
        $this->appId = random_bytes(8);

        $this->event = new U2fPreAuthenticationEvent($this->appId, $this->user);
    }

    private function setUpDeniedWithReason()
    {
        $this->setUpAllowed();

        $this->reason = random_bytes(16);
        $this->event->abort($this->reason);
    }

    private function setUpDeniedWithoutReason()
    {
        $this->setUpAllowed();

        $this->event->abort();
    }
}
