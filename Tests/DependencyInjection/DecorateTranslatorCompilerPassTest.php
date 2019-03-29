<?php

namespace Webfactory\IcuTranslationBundle\Tests\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Webfactory\IcuTranslationBundle\DependencyInjection\DecorateTranslatorCompilerPass;
use Webfactory\IcuTranslationBundle\DependencyInjection\WebfactoryIcuTranslationExtension;

/**
 * Tests the compiler pass that decorates the translator.
 */
class DecorateTranslatorCompilerPassTest extends \PHPUnit_Framework_TestCase
{
    /**
     * System under test.
     *
     * @var \Webfactory\IcuTranslationBundle\DependencyInjection\DecorateTranslatorCompilerPass
     */
    protected $compilerPass = null;

    /**
     * The simulated DI container.
     *
     * @var \Symfony\Component\DependencyInjection\ContainerBuilder
     */
    protected $container = null;

    /**
     * Initializes the test environment.
     */
    protected function setUp()
    {
        parent::setUp();
        $this->container = $this->createContainer();
        $this->compilerPass = new DecorateTranslatorCompilerPass();
        $this->container->addCompilerPass($this->compilerPass);
    }

    /**
     * Cleans up the test environment.
     */
    protected function tearDown()
    {
        $this->compilerPass = null;
        $this->container = null;
        parent::tearDown();
    }

    /**
     * Checks if the class implements the necessary CompilerPass interface.
     */
    public function testImplementsInterface()
    {
        $expectedType = 'Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface';
        $this->assertInstanceOf($expectedType, $this->compilerPass);
    }

    /**
     * Ensures that the compiler pass does not fail if no translator exists.
     */
    public function testPassDoesNothingIfTranslatorIsNotAvailable()
    {
        $this->setExpectedException(null);
        $this->container->compile();
    }

    /**
     * Checks if the compiler pass decorates the existing translator.
     */
    public function testPassDecoratesExistingTranslator()
    {
        $translatorClass = $this->getMockClass('Symfony\Component\Translation\TranslatorInterface');
        $translatorDefinition = new Definition($translatorClass);
        $this->container->setDefinition('translator', $translatorDefinition);

        $this->container->compile();

        $translator = $this->container->get('translator');
        $this->assertInstanceOf('Webfactory\IcuTranslationBundle\Translator\FormatterDecorator', $translator);
    }

    /**
     * Ensures that the compiler pass is also able to decorate an aliased translator.
     */
    public function testPassDecoratesAliasedTranslator()
    {
        $translatorClass = $this->getMockClass('Symfony\Component\Translation\TranslatorInterface');
        $translatorDefinition = new Definition($translatorClass);
        $this->container->setDefinition('translator.default', $translatorDefinition);
        $this->container->setAlias('translator', 'translator.default');

        $this->container->compile();

        $translator = $this->container->get('translator');
        $this->assertInstanceOf('Webfactory\IcuTranslationBundle\Translator\FormatterDecorator', $translator);
    }

    /**
     * Creates the container that is used for testing.
     *
     * @return ContainerBuilder
     */
    protected function createContainer()
    {
        $container = new ContainerBuilder();
        // Load the services that are provided by the bundle.
        $extension = new WebfactoryIcuTranslationExtension();
        $extension->load([], $container);

        return $container;
    }
}
