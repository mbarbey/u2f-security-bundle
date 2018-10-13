<?php


namespace Mbarbey\U2fSecurityBundle\Model\U2fAuthentication;

use Symfony\Component\Validator\Constraints as Assert;

class U2fAuthentication implements U2fAuthenticationInterface
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
