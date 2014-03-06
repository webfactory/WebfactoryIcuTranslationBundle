<?php

namespace Webfactory\TranslationBundle\Tests\Functional;

use Matthias\SymfonyServiceDefinitionValidator\Error\Printer\SimpleErrorListPrinter;
use Matthias\SymfonyServiceDefinitionValidator\Error\ValidationError;
use Matthias\SymfonyServiceDefinitionValidator\Error\ValidationErrorList;
use Matthias\SymfonyServiceDefinitionValidator\ServiceDefinitionValidatorFactory;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Webfactory\IcuTranslationBundle\DependencyInjection\DecorateTranslatorCompilerPass;
use Webfactory\IcuTranslationBundle\DependencyInjection\WebfactoryIcuTranslationExtension;

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
        $errors    = new ValidationErrorList();
        foreach ($container->getDefinitions() as $serviceId => $definition) {
            try {
                $validator->validate($definition);
            } catch (\Exception $exception) {
                $error = new ValidationError($serviceId, $definition, $exception);
                $errors->add($error);
            }
        }
        $printer = new SimpleErrorListPrinter();
        $this->assertCount(0, $errors, $printer->printErrorList($errors));
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
 