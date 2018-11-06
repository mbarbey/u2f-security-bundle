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

/**
 * U2F registration exchange base
 *
 * When you create your entity for the registration exchange, you can either implements this interface or
 * extends the U2fAuthentication class.
 *
 * @author Michael Barbey <michael@barbey-family.ch>
 */
interface U2fRegistrationInterface
{
    /**
     * @return string|null
     */
    public function getResponse();

    /**
     * @param string|null $response
     */
    public function setResponse(string $response = null);
}
