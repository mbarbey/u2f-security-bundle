<?php

/*
 * This file is part of the U2F Security bundle.
 *
 * (c) Michael Barbey <michael@barbey-family.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mbarbey\U2fSecurityBundle\Tests\Entity\Key;

use Symfony\Bundle\FrameworkBundle\Tests\TestCase;
use Mbarbey\U2fSecurityBundle\Model\Key\U2fKey;
use Mbarbey\U2fSecurityBundle\Model\User\U2fUser;

class U2fKeyTest extends TestCase
{
    public function testKeyHandle()
    {
        $key = $this->getKey();
        $this->assertNull($key->keyHandle);

        $key->keyHandle = 'test1';
        $this->assertEquals($key->keyHandle, 'test1');
        $this->assertEquals($key->getKeyHandle(), 'test1');

        $key->setKeyHandle('test2');
        $this->assertEquals($key->keyHandle, 'test2');
        $this->assertEquals($key->getKeyHandle(), 'test2');
    }

    public function testPublicKey()
    {
        $key = $this->getKey();
        $this->assertNull($key->publicKey);

        $key->publicKey = 'test1';
        $this->assertEquals($key->publicKey, 'test1');
        $this->assertEquals($key->getPublicKey(), 'test1');

        $key->setPublicKey('test2');
        $this->assertEquals($key->publicKey, 'test2');
        $this->assertEquals($key->getPublicKey(), 'test2');
    }

    public function testCertificate()
    {
        $key = $this->getKey();
        $this->assertNull($key->certificate);

        $key->certificate = 'test1';
        $this->assertEquals($key->certificate, 'test1');
        $this->assertEquals($key->getCertificate(), 'test1');

        $key->setCertificate('test2');
        $this->assertEquals($key->certificate, 'test2');
        $this->assertEquals($key->getCertificate(), 'test2');
    }

    public function testCounter()
    {
        $key = $this->getKey();
        $this->assertNull($key->counter);

        $key->counter = 100;
        $this->assertEquals($key->counter, 100);
        $this->assertEquals($key->getCounter(), 100);

        $key->setCounter(200);
        $this->assertEquals($key->counter, 200);
        $this->assertEquals($key->getCounter(), 200);
    }

    public function testUser()
    {
        $key = $this->getKey();
        $this->assertNull($key->getUser());

        $user = $this->getMockForAbstractClass(U2fUser::class);
        $key->setUser($user);
        $this->assertEquals($key->getUser(), $user);
    }

    protected function getKey()
    {
        return $this->getMockForAbstractClass(U2fKey::class);
    }
}
