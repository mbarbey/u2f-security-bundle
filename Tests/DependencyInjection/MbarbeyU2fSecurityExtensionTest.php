<?php

/*
 * This file is part of the U2F Security bundle.
 *
 * (c) Michael Barbey <michael@barbey-family.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mbarbey\U2fSecurityBundle\Tests\DependencyInjection;

use Symfony\Bundle\FrameworkBundle\Tests\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Mbarbey\U2fSecurityBundle\DependencyInjection\MbarbeyU2fSecurityExtension;
use Symfony\Component\Yaml\Parser;

class MbarbeyU2fSecurityExtensionTest extends TestCase
{
    /** @var ContainerBuilder */
    protected $configuration;

    protected function tearDown()
    {
        $this->configuration = null;
    }

    /**
     * @expectedException \Symfony\Component\Config\Definition\Exception\InvalidConfigurationException
     */
    public function testConfigLoadThrowsExceptionWhenNoAuthenticationRoute()
    {
        $loader = new MbarbeyU2fSecurityExtension();
        $config = $this->getEmptyConfig();
        $loader->load(array($config), new ContainerBuilder());
    }

    public function testConfigLoadWithoutWhitelist()
    {
        $this->configuration = new ContainerBuilder();
        $loader = new MbarbeyU2fSecurityExtension();
        $config = $this->getOnlyAuthenticationRoute();
        $loader->load(array($config), $this->configuration);

        $this->assertHasDefinition('mbarbey_u2f_security.subscriber');
        $this->assertEquals($this->getDefinitionArgument('mbarbey_u2f_security.subscriber', 0), 'test');
    }

    public function testFullConfig()
    {
        $this->configuration = new ContainerBuilder();
        $loader = new MbarbeyU2fSecurityExtension();
        $config = $this->getFullConfig();
        $loader->load(array($config), $this->configuration);

        $this->assertHasDefinition('mbarbey_u2f_security.subscriber');
        $this->assertEquals($this->getDefinitionArgument('mbarbey_u2f_security.subscriber', 0), 'test2');
        $this->assertEquals($this->getDefinitionArgument('mbarbey_u2f_security.subscriber', 1), ['route1', 'route2']);
    }

    protected function getEmptyConfig()
    {
        $yaml = <<<EOF
authentication_route: ~
whitelist_routes: ~
EOF;
        $parser = new Parser();
        return $parser->parse($yaml);
    }

    protected function getOnlyAuthenticationRoute()
    {
        $yaml = <<<EOF
authentication_route: 'test'
EOF;
        $parser = new Parser();
        return $parser->parse($yaml);
    }

    protected function getFullConfig()
    {
        $yaml = <<<EOF
authentication_route: test2
whitelist_routes:
    - route1
    - route2
EOF;
        $parser = new Parser();
        return $parser->parse($yaml);
    }

    private function assertHasDefinition(string $id)
    {
        $this->assertTrue($this->configuration->hasDefinition($id));
    }

    private function getDefinitionArgument(string $id, int $index)
    {
        return $this->configuration->getDefinition($id)->getArgument($index);
    }
}
