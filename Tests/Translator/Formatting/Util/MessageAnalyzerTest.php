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
