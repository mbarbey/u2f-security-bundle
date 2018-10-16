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

use Doctrine\ORM\Mapping as ORM;
use Mbarbey\U2fSecurityBundle\Model\User\U2fUserInterface;

/**
 * U2F key entity base
 *
 * When you create your entity for the security keys, you can either extends this class or implement the U2fKeyInterface.
 *
 * @author Michael Barbey <michael@barbey-family.ch>
 */
abstract class U2fKey implements U2fKeyInterface
{
    /**
     * @ORM\Column(type="string", length=255)
     */
    public $keyHandle;

    /**
     * @ORM\Column(type="string", length=255)
     */
    public $publicKey;

    /**
     * @ORM\Column(type="text")
     */
    public $certificate;

    /**
     * @ORM\Column(type="integer")
     */
    public $counter;

    /**
     * Override the definition of this variable to create the relationship
     * @var U2fUserInterface
     */
    protected $user;

    /**
     * @return string|NULL
     */
    public function getKeyHandle(): ?string
    {
        return $this->keyHandle;
    }

    /**
     * @param string $keyHandle
     * @return self
     */
    public function setKeyHandle(string $keyHandle): self
    {
        $this->keyHandle = $keyHandle;

        return $this;
    }

    /**
     * @return string|NULL
     */
    public function getPublicKey(): ?string
    {
        return $this->publicKey;
    }

    /**
     * @param string $publicKey
     * @return self
     */
    public function setPublicKey(string $publicKey): self
    {
        $this->publicKey = $publicKey;

        return $this;
    }

    /**
     * @return string|NULL
     */
    public function getCertificate(): ?string
    {
        return $this->certificate;
    }

    /**
     * @param string $certificate
     * @return self
     */
    public function setCertificate(string $certificate): self
    {
        $this->certificate = $certificate;

        return $this;
    }

    /**
     * @return int|NULL
     */
    public function getCounter(): ?int
    {
        return $this->counter;
    }

    /**
     * @param int $counter
     * @return self
     */
    public function setCounter(int $counter): self
    {
        $this->counter = $counter;

        return $this;
    }

    /**
     * @return U2fUserInterface|NULL
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param U2fUserInterface $user
     * @return self
     */
    public function setUser(?U2fUserInterface $user): self
    {
        $this->user = $user;

        return $this;
    }
}
