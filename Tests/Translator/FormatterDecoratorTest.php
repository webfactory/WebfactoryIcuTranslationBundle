<?php

namespace Webfactory\IcuTranslationBundle\Tests\Translator;

use Webfactory\IcuTranslationBundle\Translator\FormatterDecorator;

/**
 * Tests the formatter decorator for translators.
 */
class FormatterDecoratorTest extends \PHPUnit_Framework_TestCase
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
    protected function setUp()
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
    protected function tearDown()
    {
        $this->decorator = null;
        $this->formatter = null;
        $this->translator = null;
        parent::tearDown();
    }

    /**
     * Checks if the decorator implements the Translator interface.
     */
    public function testImplementsTranslatorInterface()
    {
        $this->assertInstanceOf('Symfony\Component\Translation\TranslatorInterface', $this->decorator);
    }

    /**
     * Checks if the decorator forwards calls to trans() to the inner translator.
     */
    public function testDecoratorForwardsTransCalls()
    {
        $this->translator->expects($this->once())
                         ->method('trans')
                         ->will($this->returnValue('test'));

        $this->decorator->trans('test');
    }

    /**
     * Checks if the decorator forwards calls to transChoice() to the inner translator.
     */
    public function testDecoratorForwardsTransChoiceCalls()
    {
        $this->translator->expects($this->once())
                         ->method('transChoice')
                         ->will($this->returnValue('test'));

        $this->decorator->transChoice('test', 42);
    }

    /**
     * Checks if the decorator forwards calls to setLocale() to the inner translator.
     */
    public function testDecoratorForwardsSetLocaleCalls()
    {
        $this->translator->expects($this->once())
                         ->method('setLocale')
                         ->with('de_DE');

        $this->decorator->setLocale('de_DE');
    }

    /**
     * Checks if getLocale() returns the locale value from the inner translator.
     */
    public function testGetLocaleReturnsLocaleFromInnerTranslator()
    {
        $this->translator->expects($this->once())
                         ->method('getLocale')
                         ->will($this->returnValue('fr'));

        $this->assertEquals('fr', $this->decorator->getLocale());
    }

    /**
     * Checks if the decorator passes the result from the inner translator to the formatter.
     */
    public function testDecoratorPassesResultFromTranslatorToFormatter()
    {
        $this->translator->expects($this->once())
                         ->method('trans')
                         ->will($this->returnValue('test message'));
        $this->formatter->expects($this->once())
                        ->method('format')
                        ->with($this->anything(), 'test message');

        $this->decorator->trans('message_id');
    }

    /**
     * Checks if the decorator returns the formatted message from
     * the formatter instance.
     */
    public function testDecoratorReturnsResultFromFormatter()
    {
        $this->translator->expects($this->once())
                         ->method('trans')
                         ->will($this->returnValue('raw message'));
        $this->formatter->expects($this->once())
                        ->method('format')
                        ->will($this->returnValue('formatted message'));

        $translated = $this->decorator->trans('message_id');

        $this->assertEquals('formatted message', $translated);
    }

    /**
     * Ensures that the decorator normalizes formatter exceptions.
     */
    public function testDecoratorNormalizesFormatterException()
    {
        $this->formatter->expects($this->once())
                        ->method('format')
                        ->will($this->throwException(new \RuntimeException('Formatter exception.')));
        $this->translator->expects($this->any())
                         ->method('trans')
                         ->will($this->returnValue('any'));

        $this->setExpectedException('Webfactory\IcuTranslationBundle\Translator\FormattingException');
        $this->decorator->trans('test', ['test' => 'value'], 'messages', 'en');
    }
}
