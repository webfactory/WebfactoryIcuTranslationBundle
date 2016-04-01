<?php

namespace Webfactory\TranslationBundle\Tests\Translator\Formatting;

use Webfactory\IcuTranslationBundle\Translator\Formatting\DefaultParameterDecorator;
use Webfactory\IcuTranslationBundle\Translator\Formatting\FormatterInterface;

class DefaultParameterDecoratorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * System under test.
     *
     * @var DefaultParameterDecorator
     */
    protected $decorator = null;

    /**
     * @var FormatterInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $innerFormatter = null;

    /**
     * Initializes the test environment.
     */
    protected function setUp()
    {
        parent::setUp();
        $this->innerFormatter = $this->getMock(FormatterInterface::class);
        $this->decorator = new DefaultParameterDecorator($this->innerFormatter);
    }

    /**
     * Cleans up the test environment.
     */
    protected function tearDown()
    {
        $this->decorator = null;
        $this->innerFormatter = null;
        parent::tearDown();
    }

    public function testInjectsDefaultForAlphabeticPlaceholder()
    {
        $message = 'Hello {name}!';

        $this->assertExpectedParameters(array('name' => null));
        $this->innerFormatter->format('en', $message, array());
    }

    public function testInjectsDefaultForAlphanumericPlaceholder()
    {
        $message = 'Hello {name42}!';

        $this->assertExpectedParameters(array('name42' => null));
        $this->innerFormatter->format('en', $message, array());
    }

    public function testInjectsDefaultForNumericPlaceholder()
    {
        $message = 'Hello {0}!';

        $this->assertExpectedParameters(array('0' => null));
        $this->innerFormatter->format('en', $message, array());
    }

    public function testInjectsDefaultIfPlaceholderIsUsedMultipleTimes()
    {
        $message = 'Hello {name}! Nice to see you, {name}.';

        $this->assertExpectedParameters(array('name' => null));
        $this->innerFormatter->format('en', $message, array());
    }

    public function testInjectsDefaultsForMultiplePlaceholders()
    {
        $message = 'Hello {name}! You are assigned to {group}.';

        $this->assertExpectedParameters(array('name' => null, 'group' => null));
        $this->innerFormatter->format('en', $message, array());
    }

    public function testWorksForTypedPlaceholder()
    {

    }

    public function testWorksForPlaceholderThatIsUsedInCondition()
    {

    }

    public function testWorksForNestedPlaceholder()
    {

    }

    public function testDoesNotSetDefaultIfParameterForAlphabeticPlaceholderIsPassed()
    {
        $message = 'Hello {name}!';

        $this->assertExpectedParameters(array('name' => 'Matthias'));
        $this->innerFormatter->format('en', $message, array('name' => 'Matthias'));
    }

    public function testDoesNotSetDefaultIfParameterForNumericPlaceholderIsPassed()
    {
        $message = 'Hello {0}!';

        $this->assertExpectedParameters(array('0' => 'Matthias'));
        $this->innerFormatter->format('en', $message, array('0' => 'Matthias'));
    }

    /**
     * Asserts that the given parameters are passed to the inner formatter.
     *
     * It is only asserted that the given parameters with the provided values exists,
     * it is not guaranteed that no other parameters are passed.
     *
     * @param array<string, mixed> $parameters
     */
    protected function assertExpectedParameters(array $parameterSubSet)
    {
        $checkParameters = function (array $parameters) use ($parameterSubSet) {
            foreach ($parameterSubSet as $name => $value) {
                $this->assertArrayHasKey(
                    $name,
                    $parameters,
                    sprintf('Missing parameter. Available parameters: %s', implode(',', array_keys($parameters)))
                );
                $this->assertEquals(
                    $parameters[$name],
                    $value,
                    sprintf('Parameter "%s" does not have the expected value.', $name)
                );
            }
            return true;
        };
        $this->innerFormatter->expects($this->atLeastOnce())
            ->method('format')
            ->with($this->anything(), $this->anything(), $this->callback($checkParameters))
            ->will($this->returnValue('formatted message'));
    }
}
