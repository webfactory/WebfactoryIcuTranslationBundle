<?php

namespace Webfactory\IcuTranslationBundle\Tests\Translator\Formatting\Util;

use Webfactory\IcuTranslationBundle\Translator\Formatting\Util\MessageAnalyzer;

class MessageAnalyzerTest extends \PHPUnit_Framework_TestCase
{
    public function testAnalyzerDetectsAlphabeticParameter()
    {
        $message = 'Hello {name}!';

        $parameters = $this->getParametersFrom($message);

        $this->assertInternalType('array', $parameters);
        $this->assertContains('name', $parameters);
    }

    public function testAnalyzerDetectsAlphanumericParameter()
    {
        $message = 'Hello {name42}!';

        $parameters = $this->getParametersFrom($message);

        $this->assertInternalType('array', $parameters);
        $this->assertContains('name42', $parameters);
    }

    public function testAnalyzerDetectsNumericParameter()
    {
        $message = 'Hello {0}!';

        $parameters = $this->getParametersFrom($message);

        $this->assertInternalType('array', $parameters);
        $this->assertContains('0', $parameters);
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
