<?php

namespace Webfactory\IcuTranslationBundle\Tests\Translator;

use PHPUnit\Framework\TestCase;
use Webfactory\IcuTranslationBundle\Translator\FormatterDecorator;
use Webfactory\IcuTranslationBundle\Translator\FormattingException;

/**
 * Tests the formatter decorator for translators.
 */
class FormatterDecoratorTest extends TestCase
{
    /**
     * System under test.
     *
     * @var \Webfactory\IcuTranslationBundle\Translator\FormatterDecorator
     */
    protected $decorator = null;

    /**
     * The simulated inner translator.
     *
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $translator = null;

    /**
     * The mocked formatter that is used in the tests.
     *
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $formatter = null;

    /**
     * Initializes the test environment.
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->translator = $this->createMock('Symfony\Component\Translation\TranslatorInterface');
        $this->formatter = $this->createMock('Webfactory\IcuTranslationBundle\Translator\Formatting\FormatterInterface');
        $this->decorator = new FormatterDecorator(
            $this->translator,
            $this->formatter
        );
    }

    /**
     * Cleans up the test environment.
     */
    protected function tearDown(): void
    {
        $this->decorator = null;
        $this->formatter = null;
        $this->translator = null;
        parent::tearDown();
    }

    /**
     * Checks if the decorator implements the Translator interface.
     *
     * @test
     */
    public function implementsTranslatorInterface()
    {
        $this->assertInstanceOf('Symfony\Component\Translation\TranslatorInterface', $this->decorator);
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
                         ->willReturn('test');

        $this->decorator->trans('test');
    }

    /**
     * Checks if the decorator forwards calls to transChoice() to the inner translator.
     *
     * @test
     */
    public function decoratorForwardsTransChoiceCalls()
    {
        $this->translator->expects($this->once())
                         ->method('transChoice')
                         ->willReturn('test');

        $this->decorator->transChoice('test', 42);
    }

    /**
     * Checks if the decorator forwards calls to setLocale() to the inner translator.
     *
     * @test
     */
    public function decoratorForwardsSetLocaleCalls()
    {
        $this->translator->expects($this->once())
                         ->method('setLocale')
                         ->with('de_DE');

        $this->decorator->setLocale('de_DE');
    }

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
        $this->translator->expects($this->once())
                         ->method('trans')
                         ->willReturn('test message');
        $this->formatter->expects($this->once())
                        ->method('format')
                        ->with($this->anything(), 'test message');

        $this->decorator->trans('message_id');
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

        $translated = $this->decorator->trans('message_id');

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
