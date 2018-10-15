<?php

/*
 * This file is part of the U2F Security bundle.
 *
 * (c) Michael Barbey <michael@barbey-family.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mbarbey\U2fSecurityBundle\Tests\Entity\U2fRegistration;

use Symfony\Bundle\FrameworkBundle\Tests\TestCase;

class U2fRegistrationTest extends TestCase
{
    public function testResponse()
    {
        $registration = $this->getRegistration();
        $this->assertNull($registration->getResponse());

        $registration->setResponse('test');
        $this->assertEquals($registration->getResponse(), 'test');
    }

    protected function getRegistration()
    {
        return $this->getMockForAbstractClass('Mbarbey\U2fSecurityBundle\Model\U2fRegistration\U2fRegistration');
    }
}
