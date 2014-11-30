<?php

namespace Webfactory\TranslationBundle\Tests\Functional;

use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Bundle\MonologBundle\MonologBundle;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\HttpKernel\KernelInterface;
use Webfactory\IcuTranslationBundle\WebfactoryIcuTranslationBundle;

/**
 * Tests if the bundle is usable with the Symfony application stack.
 */
class SymfonyIntegrationTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Checks if it is possible to load the translator from the container
     * of a booted application.
     *
     * @see https://github.com/webfactory/icu-translation-bundle/issues/3
     */
    public function testTranslatorCanBeRetrievedFromContainer()
    {
        $kernel = $this->createKernel();
        $kernel->boot();

        $container = $kernel->getContainer();
        $translator = $container->get('translator');
        $this->assertInstanceOf('Symfony\Component\Translation\TranslatorInterface', $translator);
    }

    /**
     * Creates an application kernel for testing.
     *
     * @return KernelInterface
     */
    protected function createKernel()
    {
        $mockedMethods = array('registerBundles', 'registerContainerConfiguration');
        $kernel = $this->getMock('Symfony\Component\HttpKernel\Kernel', $mockedMethods, array('test', true));
        $activeBundles = array(
            new FrameworkBundle(),
            new MonologBundle(),
            new WebfactoryIcuTranslationBundle()
        );
        $kernel->expects($this->any())
            ->method('registerBundles')
            ->will($this->returnValue($activeBundles));
        $loadConfiguration = function (LoaderInterface $loader) {
            $loader->load(__DIR__ . '/_files/SymfonyIntegration/config.yml');
        };
        $kernel->expects($this->any())
            ->method('registerContainerConfiguration')
            ->will($this->returnCallback($loadConfiguration));
        return $kernel;
    }
}
