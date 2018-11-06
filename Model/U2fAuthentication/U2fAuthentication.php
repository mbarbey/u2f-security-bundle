<?php

/*
 * This file is part of the U2F Security bundle.
 *
 * (c) Michael Barbey <michael@barbey-family.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mbarbey\U2fSecurityBundle\Model\U2fAuthentication;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * U2F authentication exchange base
 *
 * When you create your entity for the authentication exchange, you can either extends this class or
 * implement the U2fAuthenticationInterface.
 *
 * @author Michael Barbey <michael@barbey-family.ch>
 */
abstract class U2fAuthentication implements U2fAuthenticationInterface
{
    /**
     * @Assert\NotBlank()
     */
    protected $response;

    /**
     * @return string|null
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * @param string|null $response
     */
    public function setResponse(string $response = null)
    {
        $this->response = $response;

        return $this;
    }
}
