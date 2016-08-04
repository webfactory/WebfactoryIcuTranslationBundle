<?php

namespace Webfactory\TranslationBundle\Tests\Translator\Formatting;

use Symfony\Component\Debug\BufferingLogger;
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
     * Mocked injected logger.
     *
     * @var \PHPUnit_Framework_MockObject_MockObject|BufferingLogger
     */
    private $logger = null;

    /**
     * Initializes the test environment.
     */
    protected function setUp()
    {
        parent::setUp();
        $this->logger = new BufferingLogger();
        $this->decorator = new MissingParameterWarningDecorator($this->createInnerFormatter(), $this->logger);
    }

    /**
     * Cleans up the test environment.
     */
    protected function tearDown()
    {
        $this->decorator = null;
        $this->logger = null;
        parent::tearDown();
    }

    public function testImplementsFormatterInterface()
    {
        $this->assertInstanceOf(FormatterInterface::class, $this->decorator);
    }

    public function testDoesNotWarnIfAllParametersAreAvailable()
    {
        $message = 'Hello {name}, you are writing a {description} test!';

        $this->decorator->format('en', $message, array('name' => 'Matthias', 'description' => 'great'));

        $this->assertNothingLogged();
    }

    public function testWarnsIfParameterIsMissing()
    {
        $message = 'Hello {name}, you are writing a {description} test!';

        $this->decorator->format('en', $message, array('description' => 'great'));

        $this->assertProblemLogged();
    }

    public function testWarnsIfParametersDifferInCase()
    {
        $message = 'Hello {Name}, you are writing a {description} test!';

        $this->decorator->format('en', $message, array('name' => 'Matthias', 'description' => 'great'));

        $this->assertProblemLogged();
    }

    public function testReturnsMessageFromInnerFormatter()
    {
        $this->assertEquals('inner message', $this->decorator->format('de', 'test', array()));
    }

    /**
     * Asserts that a problem has been detected and was logged.
     */
    private function assertProblemLogged()
    {
        $logs = $this->logger->cleanLogs();
        $this->assertGreaterThan(0, count($logs), 'Expected at least 1 log entry.');
    }

    /**
     * Asserts that nothing has been logged.
     */
    private function assertNothingLogged()
    {
        $logs = $this->logger->cleanLogs();
        $this->assertCount(0, $logs, 'No log entry expected.');
    }

    /**
     * Creates a mocked formatter.
     *
     * @return \PHPUnit_Framework_MockObject_MockObject|FormatterInterface
     */
    private function createInnerFormatter()
    {
        $formatter = $this->getMock(FormatterInterface::class);
        $formatter->expects($this->any())
            ->method('format')
            ->will($this->returnValue('inner message'));
        return $formatter;
    }
}
