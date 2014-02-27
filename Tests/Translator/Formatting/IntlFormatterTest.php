<?php

namespace Webfactory\TranslationBundle\Tests\Translator\Formatting;

use Webfactory\TranslationBundle\Translator\Formatting\IntlFormatter;

/**
 * Tests the Intl formatter.
 */
class IntlFormatterTest extends \PHPUnit_Framework_TestCase
{

    /**
     * System under test.
     *
     * @var \Webfactory\TranslationBundle\Translator\Formatting\IntlFormatter
     */
    protected $formatter = null;

    /**
     * Initializes the test environment.
     */
    protected function setUp()
    {
        parent::setUp();
        $this->formatter = new IntlFormatter();
    }

    /**
     * Cleans up the test environment.
     */
    protected function tearDown()
    {
        $this->formatter = null;
        parent::tearDown();
    }

    /**
     * Ensures that the formatter throws an exception if the syntax of the provided
     * message is not correct.
     */
    public function testFormatterThrowsExceptionIfMessageSyntaxIsNotValid()
    {
        $invalidMessage = 'Hello {name, number,';

        $expected = '\Webfactory\TranslationBundle\Translator\Formatting\Exception\CannotInstantiateFormatterException';
        $this->setExpectedException($expected);
        $this->formatter->format('en', $invalidMessage, array());
    }

    /**
     * Checks if the formatter can handle an empty string as message argument.
     */
    public function testFormatterCanHandleEmptyString()
    {
        $formatted = $this->formatter->format('en', '', array());

        $this->assertInternalType('string', $formatted);
        $this->assertEquals('', $formatted);
    }

    /**
     * Checks if the formatter substitutes simple placeholders without additional
     * instructions.
     */
    public function testFormatterSubstitutesPlaceholders()
    {
        $formatted = $this->formatter->format('en', 'Hello {0}!', array(0 => 'Matthias'));

        $this->assertEquals('Hello Matthias!', $formatted);
    }

}
 