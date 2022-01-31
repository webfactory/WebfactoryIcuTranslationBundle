<?php

namespace Webfactory\IcuTranslationBundle\Tests\Translator\Formatting\Analysis;

use PHPUnit\Framework\TestCase;
use Webfactory\IcuTranslationBundle\Translator\Formatting\Analysis\MessageAnalyzer;

class MessageAnalyzerTest extends TestCase
{
    /**
     * @test
     */
    public function analyzerDetectsAlphabeticParameter()
    {
        $message = 'Hello {name}!';

        $this->assertParameterDetected($message, 'name');
    }

    /**
     * @test
     */
    public function analyzerDetectsAlphanumericParameter()
    {
        $message = 'Hello {name42}!';

        $this->assertParameterDetected($message, 'name42');
    }

    /**
     * @test
     */
    public function analyzerDetectsNumericParameter()
    {
        $message = 'Hello {0}!';

        $this->assertParameterDetected($message, '0');
    }

    /**
     * @test
     */
    public function analyzerDetectsParameterWithUnderscore()
    {
        $message = 'Hello {your_name}!';

        $this->assertParameterDetected($message, 'your_name');
    }

    /**
     * @test
     */
    public function analyzerReturnsEachParameterOnceEvenIfItIsUsedMultipleTimes()
    {
        $message = 'Hello {name}! Nice to see you, {name}.';

        $parameters = $this->getParametersFrom($message);

        self::assertIsArray($parameters);
        $this->assertCount(1, $parameters);
    }

    /**
     * @test
     */
    public function analyzerReturnsNumericallyIndexedParametersArrayEvenIfMessageContainsParameterMoreThanOnce()
    {
        $message = 'Hello {description} {name}! Nice to see you, {name}, beloved child of {parent}.';

        $parameters = $this->getParametersFrom($message);

        self::assertIsArray($parameters);
        $this->assertArrayHasKey(0, $parameters);
        $this->assertArrayHasKey(1, $parameters);
        $this->assertArrayHasKey(2, $parameters);
    }

    /**
     * @test
     */
    public function analyzerDetectsMultipleParametersInMessage()
    {
        $message = 'Hello {name}! You are assigned to {group}.';

        $this->assertParameterDetected($message, 'name');
        $this->assertParameterDetected($message, 'group');
    }

    /**
     * @test
     */
    public function analyzerDetectsTypedParameter()
    {
        $message = 'We need {tests,number,integer} tests.';

        $this->assertParameterDetected($message, 'tests');
    }

    /**
     * @test
     */
    public function analyzerDetectsParameterThatIsUsedInCondition()
    {
        $message = '{tries, plural,'.\PHP_EOL
                 .'    =0 {First try}'.\PHP_EOL
                 .'    other {Try #}'.\PHP_EOL
                 .'}';

        $this->assertParameterDetected($message, 'tries');
    }

    /**
     * @test
     */
    public function analyzerDetectsNestedParameter()
    {
        $message = '{tries, plural,'.\PHP_EOL
                 .'    =0 {Hello {name}, this is your first try.}'.\PHP_EOL
                 .'    other {Hello {name}, this is your # try.}'.\PHP_EOL
                 .'}';

        $this->assertParameterDetected($message, 'name');
    }

    /**
     * @test
     */
    public function analyzerDoesNotDetectMessageStringInBracesAsParameter()
    {
        $message = '{tries, plural,'.\PHP_EOL
                 .'    =0 {This is a message}'.\PHP_EOL
                 .'    other {This is also a message}'.\PHP_EOL
                 .'}';

        $this->assertParameterNotDetected($message, 'This is a message');
        $this->assertParameterNotDetected($message, 'This is also a message');
        $this->assertParameterNotDetected($message, 'This');
    }

    /**
     * @test
     */
    public function analyzerDoesNotDetectParameterInEscapedBraces()
    {
        $message = "Hello '{'name'}'!";

        $this->assertParameterNotDetected($message, 'name');
    }

    /**
     * @test
     */
    public function analyzerDoesNotRecognizeMessageFromConditionPartAsParameter()
    {
        $message = '{tries, plural,'.\PHP_EOL
                 .'    =0 {This is a message}'.\PHP_EOL
                 .'    other {looks_like_a_parameter}'.\PHP_EOL
                 .'}';

        $this->assertParameterNotDetected($message, 'looks_like_a_parameter');
    }

    /**
     * @test
     */
    public function analyzerDoesNotRecognizeMessageFromNestedConditionPartAsParameter()
    {
        $message = '{tries, plural,'.\PHP_EOL
                 .'    =0 {This is a message}'.\PHP_EOL
                 .'    other {{name, select,'.\PHP_EOL
                 .'        matthias {it is not that bad!}'.\PHP_EOL
                 .'        other {looks_like_a_parameter}'.\PHP_EOL
                 .'    }}'.\PHP_EOL
                 .'}';

        $this->assertParameterNotDetected($message, 'looks_like_a_parameter');
    }

    /**
     * @test
     */
    public function analyzerDetectsParameterIfPreviousConditionBranchContainsAnotherCondition()
    {
        $message = '{tries, plural,'.\PHP_EOL
                 .'    =0 {{name, select,'.\PHP_EOL
                 .'        matthias {Great!}'.\PHP_EOL
                 .'        other {Could have been better!}'.\PHP_EOL
                 .'    }}'.\PHP_EOL
                 .'    other {Nice try, {salutation}!}'.\PHP_EOL
                 .'}';

        $this->assertParameterDetected($message, 'salutation');
    }

    /**
     * Asserts that the analyzer detected the parameter with the name $parameter in $message.
     *
     * @param string $message
     * @param string $parameter
     */
    private function assertParameterDetected($message, $parameter)
    {
        $parameters = $this->getParametersFrom($message);

        self::assertIsArray($parameters);
        self::assertContains($parameter, $parameters);
    }

    /**
     * Asserts that the analyzer does *not* the parameter with the name $parameter in $message.
     *
     * @param string $message
     * @param string $parameter
     */
    private function assertParameterNotDetected($message, $parameter)
    {
        $parameters = $this->getParametersFrom($message);

        self::assertIsArray($parameters);
        $this->assertNotContains($parameter, $parameters);
    }

    /**
     * Returns the names of the parameters that the analyzer finds in the given message.
     *
     * @param string $message
     *
     * @return string[]
     */
    private function getParametersFrom($message)
    {
        return (new MessageAnalyzer($message))->getParameters();
    }
}
