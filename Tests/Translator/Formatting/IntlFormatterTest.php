<?php

namespace Webfactory\TranslationBundle\Tests\Translator\Formatting;

use Webfactory\IcuTranslationBundle\Translator\Formatting\IntlFormatter;

/**
 * Tests the Intl formatter.
 */
class IntlFormatterTest extends \PHPUnit_Framework_TestCase
{

    /**
     * System under test.
     *
     * @var \Webfactory\IcuTranslationBundle\Translator\Formatting\IntlFormatter
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

        $expected = '\Webfactory\IcuTranslationBundle\Translator\Formatting\Exception\CannotInstantiateFormatterException';
        $this->setExpectedException($expected);
        $this->formatter->format('en', $invalidMessage, array());
    }

    /**
     * Ensures that the formatter throws an exception if formatting is not possible
     * with the provided information.
     */
    public function testFormatterThrowsExceptionIfParametersAreNotValid()
    {
        if (version_compare(PHP_VERSION, '5.5', '<')) {
            // TODO PHP 5.5 does not substitute missing parameters. We should decide how to handle
            // this case for prior versions. It might be useful to simulate 5.5 behavior.
            $expected = '\Webfactory\IcuTranslationBundle\Translator\Formatting\Exception\CannotFormatException';
            $this->setExpectedException($expected);
        }
        // The required parameter is missing.
        $this->formatter->format('en', 'Hello {0}!', array());
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
 