<?php

namespace Mbarbey\U2fSecurityBundle\Model\Key;

interface U2fKeyInterface{
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
