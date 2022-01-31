<?php

namespace Webfactory\TranslationBundle\Tests\Functional;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Translation\TranslatorInterface;
use Webfactory\IcuTranslationBundle\Translator\FormatterDecorator;

class SymfonyIntegrationTest extends KernelTestCase
{
    /**
     * Checks if it is possible to load the translator from the container
     * of a booted application.
     *
     * @see https://github.com/webfactory/icu-translation-bundle/issues/3
     *
     * @test
     */
    public function translatorCanBeRetrievedFromContainer()
    {
        $kernel = static::bootKernel();
        $container = $kernel->getContainer();
        $translator = $container->get('translator');

        self::assertInstanceOf(TranslatorInterface::class, $translator);
        self::assertInstanceOf(FormatterDecorator::class, $translator);
    }
}
