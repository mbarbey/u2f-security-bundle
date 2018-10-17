<?php

/*
 * This file is part of the U2F Security bundle.
 *
 * (c) Michael Barbey <michael@barbey-family.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mbarbey\U2fSecurityBundle\Tests\Entity\User;

use Symfony\Bundle\FrameworkBundle\Tests\TestCase;
use Mbarbey\U2fSecurityBundle\Model\User\U2fUser;
use Mbarbey\U2fSecurityBundle\Model\Key\U2fKey;

class U2fUserTest extends TestCase
{
    public function testKeys()
    {
        $user = $this->getUser();
        $this->assertEquals($user->getU2fKeys()->count(), 0);

        $key1 = $this->getMockForAbstractClass(U2fKey::class);
        $user->addU2fKey($key1);
        $this->assertEquals($user->getU2fKeys()->count(), 1);
        $this->assertEquals($user->getU2fKeys()->first(), $key1);
        $this->assertEquals($key1->getUser(), $user);

        $key2 = $this->getMockForAbstractClass(U2fKey::class);
        $user->addU2fKey($key2);
        $this->assertEquals($user->getU2fKeys()->count(), 2);
        $this->assertEquals($user->getU2fKeys()->get(0), $key1);
        $this->assertEquals($user->getU2fKeys()->get(1), $key2);
        $this->assertEquals($key2->getUser(), $user);

        $user->removeU2fKey($key1);
        $this->assertNull($key1->getUser());
        $this->assertEquals($user->getU2fKeys()->count(), 1);
        $this->assertEquals($user->getU2fKeys()->first(), $key2);

        $user->removeU2fKey($key2);
        $this->assertNull($key2->getUser());
        $this->assertEquals($user->getU2fKeys()->count(), 0);
    }

    protected function getUser()
    {
        return $this->getMockForAbstractClass(U2fUser::class);
    }
}
