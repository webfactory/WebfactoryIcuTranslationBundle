<?php

namespace Webfactory\TranslationBundle\Tests\Translator\Formatting;

use Psr\Log\LoggerInterface;
use Webfactory\IcuTranslationBundle\Translator\Formatting\Exception\FormattingException;
use Webfactory\IcuTranslationBundle\Translator\Formatting\FormatterInterface;
use Webfactory\IcuTranslationBundle\Translator\Formatting\GracefulExceptionsDecorator;

/**
 * Tests for the decorator that deals gracefully with exceptions.
 */
final class GracefulExceptionsDecoratorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * System under test.
     *
     * @var GracefulExceptionsDecorator
     */
    protected $decorator = null;

    /**
     * Mocked inner formatter.
     *
     * @var \PHPUnit_Framework_MockObject_MockObject|FormatterInterface
     */
    protected $innerFormatter = null;

    /**
     * Mocked injected logger.
     *
     * @var \PHPUnit_Framework_MockObject_MockObject|LoggerInterface
     */
    protected $logger = null;

    /**
     * Initializes the test environment.
     */
    protected function setUp()
    {
        parent::setUp();
        $this->innerFormatter = $this->createMock('\Webfactory\IcuTranslationBundle\Translator\Formatting\FormatterInterface');
        $this->logger = $this->createMock('\Psr\Log\LoggerInterface');
        $this->decorator = new GracefulExceptionsDecorator($this->innerFormatter, $this->logger);
    }

    /**
     * @test
     * Checks if the decorator implements the formatter interface.
     */
    public function implementsFormatterInterface()
    {
        $expectedType = '\Webfactory\IcuTranslationBundle\Translator\Formatting\FormatterInterface';
        $this->assertInstanceOf($expectedType, $this->decorator);
    }

    /**
     * @test
     * Checks the decorator returns the inner response if no exception got thrown.
     */
    public function returnInnerResponseNoExceptionGotThrown()
    {
        $innerResponse = 'inner response';
        $this->innerFormatter->expects($this->once())
                             ->method('format')
                             ->willReturn($innerResponse);
        $this->assertSame($innerResponse, $this->decorator->format('', '', []));
    }

    /**
     * @test
     * Checks the decorator logs a formatting exception thrown in the inner formatter.
     */
    public function logsFormattingException()
    {
        $innerException = new FormattingException();
        $this->simulateFormatterException($innerException);
        $this->assertWillLogException($innerException);

        $this->decorator->format('', '', []);
    }

    /**
     * Checks if none-formatting exceptions are logged.
     *
     * @test
     *
     * @see https://github.com/webfactory/icu-translation-bundle/issues/4
     */
    public function logsOtherExceptionTypes()
    {
        $innerException = new \RuntimeException('Unexpected exception.');
        $this->simulateFormatterException($innerException);
        $this->assertWillLogException($innerException);

        $this->decorator->format('', '', []);
    }

    /**
     * @test
     * Checks the decorator gracefully returns a string if an exception is thrown in the inner formatter.
     */
    public function returnStringInCaseOfException()
    {
        $this->simulateFormatterException(new FormattingException());

        $this->assertInternalType('string', $this->decorator->format('', '', []));
    }

    /**
     * @test
     * Checks the constructor parameter for the logger is optional, i.e. nothing breaks if it was not set and the inner
     * formatter throws an exception.
     */
    public function loggerIsOptional()
    {
        $decorator = new GracefulExceptionsDecorator($this->innerFormatter);
        $this->simulateFormatterException(new FormattingException());

        $this->setExpectedException(null);

        $decorator->format('', '', []);
    }

    /**
     * Ensures that the inner formatter throws the given exception.
     */
    private function simulateFormatterException(\Exception $exception)
    {
        $this->innerFormatter->expects($this->once())
            ->method('format')
            ->will($this->throwException($exception));
    }

    /**
     * Asserts that the given exception will be logged.
     */
    private function assertWillLogException(\Exception $exception)
    {
        $this->logger->expects($this->once())
            ->method('error')
            ->with(
                $this->anything(),
                $this->contains($exception)
            );
    }
}
