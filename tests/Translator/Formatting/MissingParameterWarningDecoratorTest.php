<?php

namespace Webfactory\TranslationBundle\Tests\Translator\Formatting;

use Psr\Log\AbstractLogger;
use Psr\Log\LoggerInterface;
use Webfactory\IcuTranslationBundle\Translator\Formatting\FormatterInterface;
use Webfactory\IcuTranslationBundle\Translator\Formatting\MissingParameterWarningDecorator;

class MissingParameterWarningDecoratorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * System under test.
     *
     * @var MissingParameterWarningDecorator
     */
    private $decorator = null;

    /**
     * @var array<array<string=>mixed>>
     */
    private $logEntries = null;

    /**
     * Initializes the test environment.
     */
    protected function setUp()
    {
        parent::setUp();
        $this->logEntries = [];
        $this->decorator = new MissingParameterWarningDecorator($this->createInnerFormatter(), $this->createLogger());
    }

    /**
     * Cleans up the test environment.
     */
    protected function tearDown()
    {
        $this->decorator = null;
        $this->logEntries = null;
        parent::tearDown();
    }

    /**
     * @test
     */
    public function implementsFormatterInterface()
    {
        $this->assertInstanceOf(FormatterInterface::class, $this->decorator);
    }

    /**
     * @test
     */
    public function doesNotWarnIfAllParametersAreAvailable()
    {
        $message = 'Hello {name}, you are writing a {description} test!';

        $this->decorator->format('en', $message, ['name' => 'Matthias', 'description' => 'great']);

        $this->assertNothingLogged();
    }

    /**
     * @test
     */
    public function warnsIfParameterIsMissing()
    {
        $message = 'Hello {name}, you are writing a {description} test!';

        $this->decorator->format('en', $message, ['description' => 'great']);

        $this->assertProblemLogged();
    }

    /**
     * @test
     */
    public function warnsIfParametersDifferInCase()
    {
        $message = 'Hello {Name}, you are writing a {description} test!';

        $this->decorator->format('en', $message, ['name' => 'Matthias', 'description' => 'great']);

        $this->assertProblemLogged();
    }

    /**
     * @test
     */
    public function returnsMessageFromInnerFormatter()
    {
        $this->assertEquals('inner message', $this->decorator->format('de', 'test', []));
    }

    /**
     * Asserts that a problem has been detected and was logged.
     */
    private function assertProblemLogged()
    {
        $this->assertGreaterThan(0, \count($this->logEntries), 'Expected at least 1 log entry.');
    }

    /**
     * Asserts that nothing has been logged.
     */
    private function assertNothingLogged()
    {
        $this->assertCount(0, $this->logEntries, 'No log entry expected.');
    }

    /**
     * Creates a mocked formatter.
     *
     * @return \PHPUnit_Framework_MockObject_MockObject|FormatterInterface
     */
    private function createInnerFormatter()
    {
        $formatter = $this->createMock(FormatterInterface::class);
        $formatter->expects($this->any())
            ->method('format')
            ->willReturn('inner message');

        return $formatter;
    }

    /**
     * Creates a mocked logger that stores received log entries in the $logEntries attribute.
     *
     * @return LoggerInterface
     */
    private function createLogger()
    {
        $storeLogEntry = function ($level, $message) {
            $this->logEntries[] = [
                'level' => $level,
                'message' => $message,
            ];
        };
        $logger = $this->getMockForAbstractClass(AbstractLogger::class);
        $logger->expects($this->any())
            ->method('log')
            ->willReturnCallback($storeLogEntry);

        return $logger;
    }
}
