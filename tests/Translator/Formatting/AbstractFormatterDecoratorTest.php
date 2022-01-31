<?php

namespace Webfactory\IcuTranslationBundle\Tests\Translator\Formatting;

use PHPUnit\Framework\TestCase;

/**
 * Tests the abstract formatter decorator.
 */
class AbstractFormatterDecoratorTest extends TestCase
{
    /**
     * System under test.
     *
     * @var \Webfactory\IcuTranslationBundle\Translator\Formatting\AbstractFormatterDecorator
     */
    protected $decorator = null;

    /**
     * The simulated inner formatter.
     *
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $innerFormatter = null;

    /**
     * Initializes the test environment.
     */
    protected function setUp()
    {
        parent::setUp();
        $formatterInterface = 'Webfactory\IcuTranslationBundle\Translator\Formatting\FormatterInterface';
        $this->innerFormatter = $this->createMock($formatterInterface);
        $decoratorClass = 'Webfactory\IcuTranslationBundle\Translator\Formatting\AbstractFormatterDecorator';
        $this->decorator = $this->getMockForAbstractClass($decoratorClass, [$this->innerFormatter]);
    }

    /**
     * Cleans up the test environment.
     */
    protected function tearDown()
    {
        $this->decorator = null;
        $this->innerFormatter = null;
        parent::tearDown();
    }

    /**
     * Checks if the decorator implements the formatter interface.
     *
     * @test
     */
    public function implementsInterface()
    {
        $formatterInterface = 'Webfactory\IcuTranslationBundle\Translator\Formatting\FormatterInterface';
        $this->assertInstanceOf($formatterInterface, $this->decorator);
    }

    /**
     * Checks if the decorator delegates format() calls to the inner formatter.
     *
     * @test
     */
    public function formatDelegatesToInnerFormatter()
    {
        $this->innerFormatter->expects($this->once())->method('format');

        $this->decorator->format('de_DE', 'test message', []);
    }
}
