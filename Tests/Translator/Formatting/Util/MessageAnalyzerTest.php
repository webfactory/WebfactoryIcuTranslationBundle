<?php

namespace Webfactory\IcuTranslationBundle\Tests\Translator\Formatting\Util;

use Webfactory\IcuTranslationBundle\Translator\Formatting\Util\MessageAnalyzer;

class MessageAnalyzerTest extends \PHPUnit_Framework_TestCase
{
    public function testAnalyzerDetectsAlphabeticParameter()
    {
        $message = 'Hello {name}!';

        $this->assertParameterDetected($message, 'name');
    }

    public function testAnalyzerDetectsAlphanumericParameter()
    {
        $message = 'Hello {name42}!';

        $this->assertParameterDetected($message, 'name42');
    }

    public function testAnalyzerDetectsNumericParameter()
    {
        $message = 'Hello {0}!';

        $this->assertParameterDetected($message, '0');
    }

    public function testAnalyzerDetectsParameterWithUnderscore()
    {
        $message = 'Hello {your_name}!';

        $this->assertParameterDetected($message, 'your_name');
    }

    public function testAnalyzerReturnsEachParameterOnceEvenIfItIsUsedMultipleTimes()
    {
        $message = 'Hello {name}! Nice to see you, {name}.';

        $parameters = $this->getParametersFrom($message);

        $this->assertInternalType('array', $parameters);
        $this->assertCount(1, $parameters);
    }

    public function testAnalyzerDetectsMultipleParametersInMessage()
    {
        $message = 'Hello {name}! You are assigned to {group}.';

        $this->assertParameterDetected($message, 'name');
        $this->assertParameterDetected($message, 'group');
    }

    public function testAnalyzerDetectsTypedParameter()
    {
        $message = 'We need {tests,number,integer} tests.';

        $this->assertParameterDetected($message, 'tests');
    }

    public function testAnalyzerDetectsParameterThatIsUsedInCondition()
    {
        $message = '{tries, plural,' . PHP_EOL
                 . '    =0 {First try}' . PHP_EOL
                 . '    other {Try #}' . PHP_EOL
                 . '}';

        $this->assertParameterDetected($message, 'tries');
    }

    public function testAnalyzerDetectsNestedParameter()
    {
        $message = '{tries, plural,' . PHP_EOL
                 . '    =0 {Hello {name}, this is your first try.}' . PHP_EOL
                 . '    other {Hello {name}, this is your # try.}' . PHP_EOL
                 . '}';

        $this->assertParameterDetected($message, 'name');
    }

    public function testAnalyzerDoesNotDetectMessageStringInBracesAsParameter()
    {
        $message = '{tries, plural,' . PHP_EOL
                 . '    =0 {This is a message}' . PHP_EOL
                 . '    other {This is also a message}' . PHP_EOL
                 . '}';

        $this->assertParameterNotDetected($message, 'This is a message');
        $this->assertParameterNotDetected($message, 'This is also a message');
        $this->assertParameterNotDetected($message, 'This');
    }

    public function testAnalyzerDoesNotDetectParameterInEscapedBraces()
    {
        $message = "Hello '{'name'}'!";

        $this->assertParameterNotDetected($message, 'name');
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

        $this->assertInternalType('array', $parameters);
        $this->assertContains($parameter, $parameters);
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

        $this->assertInternalType('array', $parameters);
        $this->assertNotContains($parameter, $parameters);
    }

    /**
     * Returns the names of the parameters that the analyzer finds in the given message.
     *
     * @param string $message
     * @return string[]
     */
    private function getParametersFrom($message)
    {
        return (new MessageAnalyzer($message))->getParameters();
    }
}
