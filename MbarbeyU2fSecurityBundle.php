<?php

namespace Mbarbey\U2fSecurityBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Mbarbey\U2fSecurityBundle\DependencyInjection\MbarbeyU2fSecurityExtension;

class MbarbeyU2fSecurityBundle extends Bundle
{
    public function getContainerExtension()
    {
        return new MbarbeyU2fSecurityExtension();
    }
}
