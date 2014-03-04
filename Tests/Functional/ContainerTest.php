<?php

namespace Webfactory\TranslationBundle\Tests\Functional;

use Matthias\SymfonyServiceDefinitionValidator\Error\ValidationErrorInterface;
use Matthias\SymfonyServiceDefinitionValidator\ServiceDefinitionValidatorFactory;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Webfactory\IcuTranslationBundle\DependencyInjection\DecorateTranslatorCompilerPass;
use Webfactory\IcuTranslationBundle\DependencyInjection\WebfactoryIcuTranslationExtension;
use Matthias\SymfonyServiceDefinitionValidator\BatchServiceDefinitionValidator;
use Matthias\SymfonyServiceDefinitionValidator\Error\ValidationErrorFactory;

/**
 * Tests the service container configuration.
 */
class ContainerTest extends \PHPUnit_Framework_TestCase
{

    /**
     * System under test.
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
    }

    /**
     * Cleans up the test environment.
     */
    protected function tearDown()
    {
        $this->container = null;
        parent::tearDown();
    }

    /**
     * Ensures that the container is configured correctly if the application does not
     * provide a translator.
     */
    public function testContainerConfigurationInApplicationWithoutTranslatorIsValid()
    {
        $this->assertValidConfiguration($this->container);
    }

    /**
     * Ensures that the container is configured correctly if the application defines
     * a translator.
     */
    public function testContainerConfigurationInApplicationWithTranslatorIsValid()
    {
        $this->container->register('translator', '\Symfony\Component\Translation\Translator')->addArgument('en');
        $this->assertValidConfiguration($this->container);
    }

    /**
     * Asserts that the provided container is configured correctly.
     *
     * @param ContainerBuilder $container
     */
    protected function assertValidConfiguration(ContainerBuilder $container)
    {
        $container->compile();

        $validatorFactory = new ServiceDefinitionValidatorFactory();
        $validator = $validatorFactory->create($container);
        $batchValidator = new BatchServiceDefinitionValidator(
            $validator,
            new ValidationErrorFactory()
        );
        /* @var $errors \Matthias\SymfonyServiceDefinitionValidator\Error\ValidationErrorListInterface */
        $errors = $batchValidator->validate($container->getDefinitions());
        $failures = array_map(function (ValidationErrorInterface $error) {
            return '- ' . $error->getServiceId() . ': ' . $error->getException();
        }, iterator_to_array($errors));
        $message = 'Container configuration errors detected: ' . PHP_EOL . implode(PHP_EOL, $failures);
        $this->assertCount(0, $errors, $message);
    }

    /**
     * Creates a container that is configured by this bundle.
     *
     * @return ContainerBuilder
     */
    protected function createContainer()
    {
        $builder = new ContainerBuilder();
        $extension = new WebfactoryIcuTranslationExtension();
        $extension->load(array(), $builder);
        $builder->addCompilerPass(new DecorateTranslatorCompilerPass());
        return $builder;
    }

}
 