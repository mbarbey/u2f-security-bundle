<?php

namespace Mbarbey\U2fSecurityBundle\Model\U2fRegistration;

interface U2fRegistrationInterface
{
    /**
     * @return string
     */
    public function getResponse();

    /**
     * @param string|null $response
     */
    public function setResponse(string $response = null);
}
