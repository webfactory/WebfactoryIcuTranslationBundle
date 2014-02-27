<?php

namespace Webfactory\WebsiteBundle\Tests\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Webfactory\TranslationBundle\DependencyInjection\WebfactoryTranslationExtension;

/**
 * Tests the bundle extension.
 */
class WebfactoryTranslationExtensionTest extends \PHPUnit_Framework_TestCase
{

    /**
     * System under test.
     *
     * @var \Webfactory\TranslationBundle\DependencyInjection\WebfactoryTranslationExtension
     */
    protected $extension = null;

    /**
     * Initializes the test environment.
     */
    protected function setUp()
    {
        parent::setUp();
        $this->extension = new WebfactoryTranslationExtension();
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
     */
    public function testConfiguredContainerIsCompilable()
    {
        $builder = new ContainerBuilder();
        $this->extension->load(array(), $builder);

        $this->setExpectedException(null);
        $builder->compile();
    }

}
 