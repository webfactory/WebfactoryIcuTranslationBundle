<?php

namespace Webfactory\TranslationBundle\Tests\Functional;

use Matthias\SymfonyServiceDefinitionValidator\Error\Printer\SimpleErrorListPrinter;
use Matthias\SymfonyServiceDefinitionValidator\Error\ValidationError;
use Matthias\SymfonyServiceDefinitionValidator\Error\ValidationErrorList;
use Matthias\SymfonyServiceDefinitionValidator\ServiceDefinitionValidatorFactory;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Translation\TranslatorInterface;
use Webfactory\IcuTranslationBundle\DependencyInjection\WebfactoryIcuTranslationExtension;

/**
 * Tests the service container configuration.
 */
class ContainerTest extends TestCase
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
    protected function setUp(): void
    {
        parent::setUp();
        $this->container = $this->createContainer();
    }

    /**
     * Cleans up the test environment.
     */
    protected function tearDown(): void
    {
        $this->container = null;
        parent::tearDown();
    }

    /**
     * Ensures that the container is configured correctly if the application does not
     * provide a translator.
     *
     * @test
     */
    public function containerConfigurationInApplicationWithoutTranslatorIsValid()
    {
        $this->assertValidConfiguration($this->container);
    }

    /**
     * Ensures that the container is configured correctly if the application defines
     * a translator.
     *
     * @test
     */
    public function containerConfigurationInApplicationWithTranslatorIsValid()
    {
        $this->container->register('translator', '\Symfony\Component\Translation\Translator')->addArgument('en');
        $this->assertValidConfiguration($this->container);
    }

    /**
     * Asserts that the provided container is configured correctly.
     */
    protected function assertValidConfiguration(ContainerBuilder $container)
    {
        $container->compile();

        $errors = $this->validateContainer($container);
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
        $builder->register('translator', TranslatorInterface::class);
        $extension = new WebfactoryIcuTranslationExtension();
        $extension->load([], $builder);

        return $builder;
    }

    /**
     * Validates the definitions in the provided container and returns a list of errors.
     *
     * @return ValidationErrorList
     */
    protected function validateContainer(ContainerBuilder $container)
    {
        $validatorFactory = new ServiceDefinitionValidatorFactory();
        $validator = $validatorFactory->create($container);
        $errors = new ValidationErrorList();
        foreach ($container->getDefinitions() as $serviceId => $definition) {
            try {
                $validator->validate($definition);

                return $errors;
            } catch (\Exception $exception) {
                $error = new ValidationError($serviceId, $definition, $exception);
                $errors->add($error);
            }
        }

        return $errors;
    }
}
