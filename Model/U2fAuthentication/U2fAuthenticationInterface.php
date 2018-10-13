<?php

namespace Mbarbey\U2fSecurityBundle\Model\U2fAuthentication;

interface U2fAuthenticationInterface
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
