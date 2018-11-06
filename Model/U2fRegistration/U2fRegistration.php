<?php

/*
 * This file is part of the U2F Security bundle.
 *
 * (c) Michael Barbey <michael@barbey-family.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mbarbey\U2fSecurityBundle\Model\U2fRegistration;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * U2F registration exchange base
 *
 * When you create your entity for the registration exchange, you can either extends this class or
 * implement the U2fRegistrationInterface.
 *
 * @author Michael Barbey <michael@barbey-family.ch>
 */
class U2fRegistration implements U2fRegistrationInterface
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
    public function setResponse($response = null)
    {
        $this->response = $response;

        return $this;
    }
}
