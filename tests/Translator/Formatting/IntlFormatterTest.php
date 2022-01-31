<?php

namespace Webfactory\TranslationBundle\Tests\Translator\Formatting;

use PHPUnit\Framework\TestCase;
use Webfactory\IcuTranslationBundle\Translator\Formatting\IntlFormatter;

/**
 * Tests the Intl formatter.
 */
class IntlFormatterTest extends TestCase
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
    protected function setUp(): void
    {
        parent::setUp();
        $this->formatter = new IntlFormatter();
    }

    /**
     * Cleans up the test environment.
     */
    protected function tearDown(): void
    {
        $this->formatter = null;
        parent::tearDown();
    }

    /**
     * Ensures that the formatter throws an exception if the syntax of the provided
     * message is not correct.
     *
     * @test
     */
    public function formatterThrowsExceptionIfMessageSyntaxIsNotValid()
    {
        $invalidMessage = 'Hello {name, number,';

        $expected = '\Webfactory\IcuTranslationBundle\Translator\Formatting\Exception\CannotInstantiateFormatterException';
        self::expectException($expected);
        $this->formatter->format('en', $invalidMessage, []);
    }

    /**
     * Ensures that the formatter does not substitute missing parameters (PHP >= 5.5).
     *
     * @test
     */
    public function formatterDoesNotSubstituteMissingParameters()
    {
        // The required parameter is missing.
        $message = $this->formatter->format('en', 'Hello {0}!', []);
        $this->assertEquals('Hello {0}!', $message);
    }

    /**
     * Checks if the formatter is able to substitute named parameters (PHP >= 5.5).
     *
     * @test
     */
    public function formatterSubstitutesNamedParameters()
    {
        $message = $this->formatter->format('en', 'Hello {name}!', ['name' => 'Matthias']);
        $this->assertEquals('Hello Matthias!', $message);
    }

    /**
     * Checks if the formatter can handle an empty string as message argument.
     *
     * @test
     */
    public function formatterCanHandleEmptyString()
    {
        $formatted = $this->formatter->format('en', '', []);

        self::assertIsString($formatted);
        $this->assertEquals('', $formatted);
    }

    /**
     * Checks if the formatter substitutes simple placeholders without additional
     * instructions.
     *
     * @test
     */
    public function formatterSubstitutesPlaceholders()
    {
        $formatted = $this->formatter->format('en', 'Hello {0}!', [0 => 'Matthias']);

        $this->assertEquals('Hello Matthias!', $formatted);
    }
}
