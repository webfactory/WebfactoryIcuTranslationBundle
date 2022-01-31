<?php

namespace Webfactory\TranslationBundle\Tests\Translator\Formatting;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Webfactory\IcuTranslationBundle\Translator\Formatting\Exception\FormattingException;
use Webfactory\IcuTranslationBundle\Translator\Formatting\FormatterInterface;
use Webfactory\IcuTranslationBundle\Translator\Formatting\GracefulExceptionsDecorator;

/**
 * Tests for the decorator that deals gracefully with exceptions.
 */
final class GracefulExceptionsDecoratorTest extends TestCase
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
     * @var MockObject|FormatterInterface
     */
    protected $innerFormatter = null;

    /**
     * Mocked injected logger.
     *
     * @var MockObject|LoggerInterface
     */
    protected $logger = null;

    /**
     * Initializes the test environment.
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->innerFormatter = $this->createMock(FormatterInterface::class);
        $this->logger = $this->createMock(LoggerInterface::class);
        $this->decorator = new GracefulExceptionsDecorator($this->innerFormatter, $this->logger);
    }

    /**
     * @test
     * Checks if the decorator implements the formatter interface.
     */
    public function implementsFormatterInterface()
    {
        $this->assertInstanceOf(FormatterInterface::class, $this->decorator);
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

        self::assertIsString($this->decorator->format('', '', []));
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
