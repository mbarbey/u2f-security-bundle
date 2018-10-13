<?php


namespace Mbarbey\U2fSecurityBundle\Model\U2fRegistration;

use Symfony\Component\Validator\Constraints as Assert;

class U2fRegistration implements U2fRegistrationInterface
{
    /**
     * @Assert\NotBlank()
     */
    private $response;

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
