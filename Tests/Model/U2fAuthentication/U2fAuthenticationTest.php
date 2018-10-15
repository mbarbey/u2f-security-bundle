<?php

/*
 * This file is part of the U2F Security bundle.
 *
 * (c) Michael Barbey <michael@barbey-family.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mbarbey\U2fSecurityBundle\Tests\Entity\U2fAuthentication;

use Symfony\Bundle\FrameworkBundle\Tests\TestCase;

class U2fAuthenticationTest extends TestCase
{
    public function testResponse()
    {
        $authentication = $this->getAuthentication();
        $this->assertNull($authentication->getResponse());

        $authentication->setResponse('test');
        $this->assertEquals($authentication->getResponse(), 'test');
    }

    protected function getAuthentication()
    {
        return $this->getMockForAbstractClass('Mbarbey\U2fSecurityBundle\Model\U2fAuthentication\U2fAuthentication');
    }
}
