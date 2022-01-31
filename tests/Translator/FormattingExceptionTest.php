<?php

namespace Webfactory\IcuTranslationBundle\Tests\Translator;

use Webfactory\IcuTranslationBundle\Translator\FormattingException;

/**
 * Tests the formatting exception.
 */
class FormattingExceptionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * System under test.
     *
     * @var \Webfactory\IcuTranslationBundle\Translator\FormattingException
     */
    protected $exception = null;

    /**
     * The inner exception.
     *
     * @var \Exception
     */
    protected $innerException = null;

    /**
     * Initializes the test environment.
     */
    protected function setUp()
    {
        parent::setUp();
        $this->innerException = new \RuntimeException('Inner exception.', 42);
        $this->exception = new FormattingException(
            'en_US',
            'translation_id',
            'Hello {test}!',
            [
                'test' => 'Albert',
            ],
            $this->innerException
        );
    }

    /**
     * Cleans up the test environment.
     */
    protected function tearDown()
    {
        $this->exception = null;
        $this->innerException = null;
        parent::tearDown();
    }

    /**
     * Ensures that getCode() returns the code of the inner exception.
     *
     * @test
     */
    public function getCodeReturnsCodeFromInnerException()
    {
        $this->assertEquals($this->innerException->getCode(), $this->exception->getCode());
    }

    /**
     * Checks if the exception message contains the translation message ID.
     *
     * @test
     */
    public function messageContainsMessageId()
    {
        $this->assertContains('translation_id', $this->exception->getMessage());
    }

    /**
     * Checks if the exception message contains the translation message pattern.
     *
     * @test
     */
    public function messageContainsMessagePattern()
    {
        $this->assertContains('Hello {test}!', $this->exception->getMessage());
    }

    /**
     * Checks if the exception message contains the translation locale.
     *
     * @test
     */
    public function messageContainsLocale()
    {
        $this->assertContains('en_US', $this->exception->getMessage());
    }

    /**
     * Ensures that the exception message contains the translation parameters.
     *
     * @test
     */
    public function messageContainsParameters()
    {
        $this->assertContains('Albert', $this->exception->getMessage());
    }

    /**
     * Checks if getMessageId() returns the correct value.
     *
     * @test
     */
    public function getMessageIdReturnsCorrectValue()
    {
        $this->assertEquals('translation_id', $this->exception->getMessageId());
    }

    /**
     * Checks if getMessagePattern() returns the correct value.
     *
     * @test
     */
    public function getMessagePatternReturnsCorrectValue()
    {
        $this->assertEquals('Hello {test}!', $this->exception->getMessagePattern());
    }

    /**
     * Checks if getLocale() returns the correct value.
     *
     * @test
     */
    public function getLocaleReturnsCorrectValue()
    {
        $this->assertEquals('en_US', $this->exception->getLocale());
    }

    /**
     * Checks if getParameters() returns the translation parameters.
     *
     * @test
     */
    public function getParametersReturnsCorrectValue()
    {
        $expected = ['test' => 'Albert'];
        $this->assertEquals($expected, $this->exception->getParameters());
    }

    /**
     * Ensures that getPrevious() returns the inner exception.
     *
     * @test
     */
    public function getPreviousReturnsInnerException()
    {
        $this->assertSame($this->innerException, $this->exception->getPrevious());
    }
}
