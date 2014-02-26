<?php

namespace Webfactory\WebsiteBundle\Tests\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Webfactory\TranslatorBundle\DependencyInjection\DecorateTranslatorCompilerPass;

/**
 * Tests the compiler pass that decorates the translator.
 */
class DecorateTranslatorCompilerPassTest extends \PHPUnit_Framework_TestCase
{

    /**
     * System under test.
     *
     * @var \Webfactory\TranslatorBundle\DependencyInjection\DecorateTranslatorCompilerPass
     */
    protected $compilerPass = null;

    /**
     * The simulated DI container.
     *
     * @var \Symfony\Component\DependencyInjection\ContainerBuilder
     */
    protected $container = null;

    protected function setUp()
    {
        parent::setUp();
        $this->container    = new ContainerBuilder();
        $this->compilerPass = new DecorateTranslatorCompilerPass();
    }

    protected function tearDown()
    {
        $this->compilerPass = null;
        $this->container    = null;
        parent::tearDown();
    }


    /**
     * Checks if the class implements the necessary CompilerPass interface.
     */
    public function testImplementsInterface()
    {
        $this->assertInstanceOf('Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface', $this->compilerPass);
    }

    /**
     * Ensures that the compiler pass does not fail if no translator exists.
     */
    public function testPassDoesNothingIfTranslatorIsNotAvailable()
    {
        $this->setExpectedException(null);
        $this->compilerPass->process($this->container);
    }

    /**
     * Checks if the compiler pass decorates the existing translator.
     */
    public function testPassDecoratesExistingTranslator()
    {
        $translatorClass      = $this->getMockClass('Symfony\Component\Translation\TranslatorInterface');
        $translatorDefinition = new Definition($translatorClass);
        $this->container->setDefinition('translator', $translatorDefinition);

        $this->compilerPass->process($this->container);

        $translator = $this->container->get('translator');
        $this->assertInstanceOf('Webfactory\TranslatorBundle\Translator\FormatterDecorator', $translator);
    }

    /**
     * Ensures that the compiler pass is also able to decorate an aliased translator.
     */
    public function testPassDecoratesAliasedTranslator()
    {
        $translatorClass      = $this->getMockClass('Symfony\Component\Translation\TranslatorInterface');
        $translatorDefinition = new Definition($translatorClass);
        $this->container->setDefinition('translator.default', $translatorDefinition);
        $this->container->setAlias('translator', 'translator.default');

        $this->compilerPass->process($this->container);

        $translator = $this->container->get('translator');
        $this->assertInstanceOf('Webfactory\TranslatorBundle\Translator\FormatterDecorator', $translator);
    }

}
