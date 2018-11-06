<?php

/*
 * This file is part of the U2F Security bundle.
 *
 * (c) Michael Barbey <michael@barbey-family.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mbarbey\U2fSecurityBundle\Model\Key;

use Mbarbey\U2fSecurityBundle\Model\User\U2fUserInterface;

/**
 * U2F key entity interface
 *
 * When you create your entity for the security keys, you can either implement this interface or extends the U2fKey class.
 *
 * @author Michael Barbey <michael@barbey-family.ch>
 */
interface U2fKeyInterface
{
    /**
     * @return string|NULL
     */
    public function getKeyHandle();

    /**
     * @param string $keyHandle
     * @return self
     */
    public function setKeyHandle($keyHandle);

    /**
     * @return string|NULL
     */
    public function getPublicKey();

    /**
     * @param string $publicKey
     * @return self
     */
    public function setPublicKey($publicKey);

    /**
     * @return string|NULL
     */
    public function getCertificate();

    /**
     * @param string $certificate
     * @return self
     */
    public function setCertificate($certificate);

    /**
     * @return int|NULL
     */
    public function getCounter();

    /**
     * @param int $counter
     * @return self
     */
    public function setCounter($counter);

    /**
     * @return U2fUserInterface|NULL
     */
    public function getUser();

    /**
     * @param U2fUserInterface $user
     */
    public function setUser(U2fUserInterface $user = null);
}
