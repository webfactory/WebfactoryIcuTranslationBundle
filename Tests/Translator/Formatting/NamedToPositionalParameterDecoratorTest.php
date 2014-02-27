<?php

namespace Webfactory\TranslationBundle\Tests\Translator\Formatting;

use Webfactory\TranslationBundle\Translator\Formatting\MessageLexer;
use Webfactory\TranslationBundle\Translator\Formatting\MessageParser;
use Webfactory\TranslationBundle\Translator\Formatting\NamedToPositionalParameterDecorator;

/**
 * Tests the decorator that converts named into positional parameters.
 */
class NamedToPositionalParameterDecoratorTest extends \PHPUnit_Framework_TestCase
{

    /**
     * System under test.
     *
     * @var \Webfactory\TranslationBundle\Translator\Formatting\NamedToPositionalParameterDecorator
     */
    protected $decorator = null;

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
        $this->decorator      = new NamedToPositionalParameterDecorator(
            $this->innerFormatter,
            new MessageParser(new MessageLexer())
        );
    }

    /**
     * Cleans up the test environment.
     */
    protected function tearDown()
    {
        $this->decorator      = null;
        $this->innerFormatter = null;
        parent::tearDown();
    }

    /**
     * Checks if the decorator implements the formatter interface.
     */
    public function testImplementsFormatterInterface()
    {
        $expectedType = '\Webfactory\TranslationBundle\Translator\Formatting\FormatterInterface';
        $this->assertInstanceOf($expectedType, $this->decorator);
    }

    /**
     * Checks if the decorator can handle translation messages that do not contain
     * any parameter.
     */
    public function testDecoratorCanHandleMessagesWithoutParameters()
    {
        $this->assertFormatterReceives('test', array());

        $this->decorator->format('de', 'test', array());
    }

    /**
     * Checks if the decorator converts named parameters in the provided message.
     */
    public function testDecoratorConvertsNamedToPositionalParameters()
    {
        $this->assertFormatterReceives('Hello {0} from {1}!', $this->anything());

        $params = array('name' => 'Matthias', 'location' => 'Bonn');
        $this->decorator->format('de', 'Hello {name} from {location}!', $params);
    }

    /**
     * Checks if the decorator assigns the same position to each occurrence of a named
     * parameter.
     */
    public function testDecoratorConvertsParametersThatOccurMultipleTimesCorrectly()
    {
        $this->assertFormatterReceives('{0}/{0}', $this->anything());

        $this->decorator->format('de', '{name}/{name}', array('name' => 'Matthias'));
    }

    /**
     * Checks if the decorator can handle nested formatting instructions.
     */
    public function testDecoratorWorksWithNestedFormattingConstructs()
    {
        $message = '{number_of_tries, plural,'                      . PHP_EOL
                 . '    =0 {Hello {name}, this is your first try.}' . PHP_EOL
                 . '    other {Hello {name}, this is your # try.}'  . PHP_EOL
                 . '}';

        $expected = strtr($message, array('number_of_tries' => '0', 'name' => '1'));
        $this->assertFormatterReceives($expected, $this->anything());

        $this->decorator->format('de', $message, array('name' => 'Matthias'));
    }

    /**
     * Checks if the decorator changes the parameters to match the new (positional) identifiers
     * in the translation message.
     */
    public function testDecoratorChangesParametersToMatchPositions()
    {
        $params = array('name' => 'Matthias', 'location' => 'Bonn');

        $this->assertFormatterReceives($this->anything(), array_values($params));

        $this->decorator->format('de', 'Hello {name} from {location}!', $params);
    }

    /**
     * Checks if the decorator returns the result from the inner formatter.
     */
    public function testDecoratorReturnsResultFromInnerFormatter()
    {
        $this->innerFormatter->expects($this->once())
                             ->method('format')
                             ->will($this->returnValue('test'));

        $this->assertEquals('test', $this->decorator->format('de', 'hello', array()));
    }

    /**
     * Asserts that the inner formatter receives the provided message and the
     * given parameters.
     *
     * @param string|\PHPUnit_Framework_Constraint $message
     * @param array(mixed)|\PHPUnit_Framework_Constraint $parameters
     */
    protected function assertFormatterReceives($message, $parameters)
    {
        $this->innerFormatter->expects($this->once())
                             ->method('format')
                             ->with($this->anything(), $message, $parameters);
    }

}
 