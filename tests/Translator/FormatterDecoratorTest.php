<?php

namespace Webfactory\IcuTranslationBundle\Tests\Translator;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Contracts\Translation\TranslatorInterface;
use Webfactory\IcuTranslationBundle\Translator\FormatterDecorator;
use Webfactory\IcuTranslationBundle\Translator\Formatting\FormatterInterface;
use Webfactory\IcuTranslationBundle\Translator\FormattingException;

/**
 * Tests the formatter decorator for translators.
 */
class FormatterDecoratorTest extends TestCase
{
    /**
     * System under test.
     *
     * @var FormatterDecorator
     */
    protected $decorator = null;

    /**
     * The simulated inner translator.
     *
     * @var MockObject
     */
    protected $translator = null;

    /**
     * The mocked formatter that is used in the tests.
     *
     * @var MockObject
     */
    protected $formatter = null;

    /**
     * Initializes the test environment.
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->translator = $this->getMockBuilder(TranslatorInterface::class)->onlyMethods(['trans'])->addMethods(['getLocale'])->getMock();
        $this->formatter = $this->createMock(FormatterInterface::class);
        $this->decorator = new FormatterDecorator(
            $this->translator,
            $this->formatter
        );
    }

    /**
     * Checks if the decorator implements the Translator interface.
     *
     * @test
     */
    public function implementsTranslatorInterface()
    {
        $this->assertInstanceOf(TranslatorInterface::class, $this->decorator);
    }

    /**
     * Checks if the decorator forwards calls to trans() to the inner translator.
     *
     * @test
     */
    public function decoratorForwardsTransCalls()
    {
        $this->translator->expects($this->once())
                         ->method('trans')
                         ->with('test', ['foo' => 'bar'], 'domain', 'locale')
                         ->willReturn('test');

        $this->formatter->method('format')->willReturn('some string');

        $this->decorator->trans('test', ['foo' => 'bar'], 'domain', 'locale');
    }

//    /**
//     * Checks if the decorator forwards calls to setLocale() to the inner translator.
//     *
//     * @test
//     */
//    public function decoratorForwardsSetLocaleCalls()
//    {
//        $this->translator->expects($this->once())
//                         ->method('setLocale')
//                         ->with('de_DE');
//
//        $this->decorator->setLocale('de_DE');
//    }

    /**
     * Checks if getLocale() returns the locale value from the inner translator.
     *
     * @test
     */
    public function getLocaleReturnsLocaleFromInnerTranslator()
    {
        $this->translator->expects($this->once())
                         ->method('getLocale')
                         ->willReturn('fr');

        $this->assertEquals('fr', $this->decorator->getLocale());
    }

    /**
     * Checks if the decorator passes the result from the inner translator to the formatter.
     *
     * @test
     */
    public function decoratorPassesResultFromTranslatorToFormatter()
    {
        $this->translator->method('getLocale')->willReturn('some_locale');
        $this->translator->expects($this->once())
                         ->method('trans')
                         ->willReturn('test message');
        $this->formatter->expects($this->once())
                        ->method('format')
                        ->with($this->anything(), 'test message')
                        ->willReturn('some string');

        $this->decorator->trans('message_id', ['foo' => 'bar'], 'domain', 'locale');
    }

    /**
     * Checks if the decorator returns the formatted message from
     * the formatter instance.
     *
     * @test
     */
    public function decoratorReturnsResultFromFormatter()
    {
        $this->translator->expects($this->once())
                         ->method('trans')
                         ->willReturn('raw message');
        $this->formatter->expects($this->once())
                        ->method('format')
                        ->willReturn('formatted message');

        $translated = $this->decorator->trans('message_id', ['foo' => 'bar'], 'domain', 'locale');

        $this->assertEquals('formatted message', $translated);
    }

    /**
     * Ensures that the decorator normalizes formatter exceptions.
     *
     * @test
     */
    public function decoratorNormalizesFormatterException()
    {
        $this->formatter->expects($this->once())
                        ->method('format')
                        ->will($this->throwException(new \RuntimeException('Formatter exception.')));
        $this->translator->expects($this->any())
                         ->method('trans')
                         ->willReturn('any');

        self::expectException(FormattingException::class);
        $this->decorator->trans('test', ['test' => 'value'], 'messages', 'en');
    }
}
