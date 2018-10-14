<?php

/*
 * This file is part of the U2F Security bundle.
 *
 * (c) Michael Barbey <michael@barbey-family.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mbarbey\U2fSecurityBundle\Model\User;

use Doctrine\Common\Collections\Collection;
use Mbarbey\U2fSecurityBundle\Model\Key\U2fKeyInterface;

/**
 * U2F user entity base
 *
 * When you edit your own user entity, you can either implements this interface or extends the U2fUser class.
 *
 * @author Michael Barbey <michael@barbey-family.ch>
 */
interface U2fUserInterface
{
    /**
     * @return Collection|U2fKeyInterface[]
     */
    public function getU2fKeys(): Collection;

    /**
     * @param U2fKeyInterface $u2fKey
     * @return self
     */
    public function addU2fKey(U2fKeyInterface $u2fKey);

    /**
     * @param U2fKeyInterface $u2fKey
     * @return self
     */
    public function removeU2fKey(U2fKeyInterface $u2fKey);
}
