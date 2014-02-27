<?php

namespace Webfactory\TranslationBundle\Tests\Translator\Formatting;

use Webfactory\TranslationBundle\Translator\Formatting\TwigParameterNormalizer;

/**
 * Tests the Twig parameter normalizer.
 */
class TwigParameterNormalizerTest extends \PHPUnit_Framework_TestCase
{

    /**
     * System under test.
     *
     * @var \Webfactory\TranslationBundle\Translator\Formatting\TwigParameterNormalizer
     */
    protected $formatter = null;

    /**
     * The mocked inner formatter.
     *
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $innerFormatter = null;

    /**
     * Initializes the test environment.
     */
    protected function setUp()
    {
        parent::setUp();
        $this->innerFormatter = $this->getMock('\Webfactory\TranslationBundle\Translator\Formatting\FormatterInterface');
        $this->formatter      = new TwigParameterNormalizer($this->innerFormatter);
    }

    /**
     * Cleans up the test environment.
     */
    protected function tearDown()
    {
        $this->formatter      = null;
        $this->innerFormatter = null;
        parent::tearDown();
    }

    /**
     * Checks if the formatter works with an empty parameter list.
     */
    public function testFormatterWorksIfParameterListIsEmpty()
    {
        $this->setExpectedException(null);
        $this->formatter->format('de', 'test', array());
    }

    /**
     * Ensures that parameter names without percentage delimiters are not modified.
     */
    public function testFormatterDoesNotModifyParameterNamesWithoutDelimiters()
    {
        $this->assertPassedParameters(array('user' => 'Matthias'));

        $this->formatter->format('de', 'test', array('user' => 'Matthias'));
    }

    /**
     * Ensures that percentage delimiters are removed from parameter names.
     */
    public function testFormatterRemovesDelimitersFromParameterNames()
    {
        $this->assertPassedParameters(array('user' => 'Matthias'));

        $this->formatter->format('de', 'test', array('%user%' => 'Matthias'));
    }

    /**
     * Ensures that the formatter keeps the existing parameter if removing the delimiters
     * from another one leads to a naming conflict.
     */
    public function testFormatterKeepsExistingParameterIfRemovingDelimitersLeadsToConflict()
    {
        $this->assertPassedParameters(array('user' => 'Matthias'));

        $this->formatter->format('de', 'test', array('user' => 'Matthias', '%user%' => 'Malte'));
    }

    /**
     * Ensures that the decorator does not change integer keys.
     */
    public function testFormatterDoesNotChangeIntegerKeys()
    {
        $this->assertPassedParameters(array(0 => 'Matthias'));

        $this->formatter->format('de', 'test', array(0 => 'Matthias'));
    }

    /**
     * Asserts that the provided parameters are passed to the inner formatter.
     *
     * @param array(string=>mixed) $parameters
     */
    protected function assertPassedParameters(array $parameters)
    {
        $this->innerFormatter->expects($this->once())
                             ->method('format')
                             ->with($this->anything(), $this->anything(), $parameters);
    }

}
