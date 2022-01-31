<?php

namespace Webfactory\IcuTranslationBundle\Tests\DependencyInjection;

use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Translation\TranslatorInterface;
use Webfactory\IcuTranslationBundle\DependencyInjection\WebfactoryIcuTranslationExtension;

/**
 * Tests the bundle extension.
 */
class WebfactoryIcuTranslationExtensionTest extends TestCase
{
    /**
     * System under test.
     *
     * @var \Webfactory\IcuTranslationBundle\DependencyInjection\WebfactoryIcuTranslationExtension
     */
    protected $extension = null;

    /**
     * Initializes the test environment.
     */
    protected function setUp()
    {
        parent::setUp();
        $this->extension = new WebfactoryIcuTranslationExtension();
    }

    /**
     * Cleans up the test environment.
     */
    protected function tearDown()
    {
        $this->extension = null;
        parent::tearDown();
    }

    /**
     * Checks if the dependency injection container can be compiled after modification
     * by the extension.
     *
     * @test
     * @doesNotPerformAssertions
     */
    public function configuredContainerIsCompilable()
    {
        $builder = new ContainerBuilder();
        $builder->register('translator', TranslatorInterface::class);
        $this->extension->load([], $builder);

        $builder->compile();
    }
}
