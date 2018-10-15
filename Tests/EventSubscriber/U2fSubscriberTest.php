<?php

/*
 * This file is part of the U2F Security bundle.
 *
 * (c) Michael Barbey <michael@barbey-family.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mbarbey\U2fSecurityBundle\Tests\EventSubscriber;

use Symfony\Bundle\FrameworkBundle\Tests\TestCase;
use Mbarbey\U2fSecurityBundle\EventSubscriber\U2fSubscriber;
use Symfony\Component\Security\Http\SecurityEvents;
use Symfony\Component\HttpKernel\KernelEvents;

class U2fSubscriberTest extends TestCase
{
    public function testSubscribedEvents()
    {
        $events = U2fSubscriber::getSubscribedEvents();

        $this->assertEquals(count($events), 2);
        $this->assertArrayHasKey(SecurityEvents::INTERACTIVE_LOGIN, $events);
        $this->assertArrayHasKey(KernelEvents::REQUEST, $events);

        $this->assertTrue(method_exists(U2fSubscriber::class, $events[SecurityEvents::INTERACTIVE_LOGIN]));
        $this->assertTrue(method_exists(U2fSubscriber::class, $events[KernelEvents::REQUEST]));
    }
}
