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
    public function getKeyHandle(): ?string;

    /**
     * @param string $keyHandle
     * @return self
     */
    public function setKeyHandle(string $keyHandle);

    /**
     * @return string|NULL
     */
    public function getPublicKey(): ?string;

    /**
     * @param string $publicKey
     * @return self
     */
    public function setPublicKey(string $publicKey);

    /**
     * @return string|NULL
     */
    public function getCertificate(): ?string;

    /**
     * @param string $certificate
     * @return self
     */
    public function setCertificate(string $certificate);

    /**
     * @return int|NULL
     */
    public function getCounter(): ?int;

    /**
     * @param int $counter
     * @return self
     */
    public function setCounter(int $counter);
}
