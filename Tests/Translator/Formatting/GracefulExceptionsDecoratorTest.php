<?php

namespace Webfactory\TranslationBundle\Tests\Translator\Formatting;

use Psr\Log\LoggerInterface;
use Webfactory\IcuTranslationBundle\Translator\Formatting\GracefulExceptionsDecorator;
use Webfactory\IcuTranslationBundle\Translator\Formatting\FormatterInterface;
use Webfactory\IcuTranslationBundle\Translator\Formatting\Exception\FormattingException;

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
        $this->innerFormatter = $this->getMock('\Webfactory\IcuTranslationBundle\Translator\Formatting\FormatterInterface');
        $this->logger         = $this->getMock('\Psr\Log\LoggerInterface');
        $this->decorator      = new GracefulExceptionsDecorator($this->innerFormatter, $this->logger);
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
                             ->will($this->returnValue($innerResponse));
        $this->assertSame($innerResponse, $this->decorator->format('', '', array()));
    }

    /**
     * @test
     * Checks the decorator logs a formatting exception thrown in the inner formatter.
     */
    public function logsFormattingException()
    {
        $innerException = new FormattingException();
        $this->innerFormatter->expects($this->once())
                             ->method('format')
                             ->will($this->throwException($innerException));
        $this->logger->expects($this->once())
                     ->method('error')
                     ->with(
                         $this->anything(),
                         $this->contains($innerException)
                     );

        $this->decorator->format('', '', array());
    }

    /**
     * Checks if none-formatting exceptions are logged.
     *
     * @test
     * @see https://github.com/webfactory/icu-translation-bundle/issues/4
     */
    public function logsOtherExceptionTypes()
    {
        $innerException = new \RuntimeException('Unexpected exception.');
        $this->innerFormatter->expects($this->once())
            ->method('format')
            ->will($this->throwException($innerException));
        $this->logger->expects($this->once())
            ->method('error')
            ->with(
                $this->anything(),
                $this->contains($innerException)
            );

        $this->decorator->format('', '', array());
    }

    /**
     * @test
     * Checks the decorator gracefully returns a string if an exception is thrown in the inner formatter.
     */
    public function returnStringInCaseOfException()
    {
        $this->innerFormatter->expects($this->once())
                             ->method('format')
                             ->will($this->throwException(new FormattingException()));

        $this->assertInternalType('string', $this->decorator->format('', '', array()));
    }

    /**
     * @test
     * Checks the constructor parameter for the logger is optional, i.e. nothing breaks if it was not set and the inner
     * formatter throws an exception.
     */
    public function loggerIsOptional()
    {
        $decorator = new GracefulExceptionsDecorator($this->innerFormatter);
        $this->innerFormatter->expects($this->once())
                             ->method('format')
                             ->will($this->throwException(new FormattingException()));

        $this->setExpectedException(null);

        $decorator->format('', '', array());
    }
}
