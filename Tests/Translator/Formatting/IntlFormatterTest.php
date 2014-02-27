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
     * Checks if the formatter substitutes simple placeholders without additional
     * instructions.
     */
    public function testFormatterSubstitutesPlaceholders()
    {
        $formatted = $this->formatter->format('en', 'Hello {0}!', array(0 => 'Matthias'));

        $this->assertEquals('Hello Matthias!', $formatted);
    }

}
 