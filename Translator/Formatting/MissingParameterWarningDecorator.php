<?php

namespace Webfactory\IcuTranslationBundle\Translator\Formatting;

use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

/**
 * Decorator that generates a warning log entry whenever a parameter for a formatted message seems to be missing.
 *
 * As missing parameters are simply ignored, these kind of mistakes can lead to serious debugging effort.
 * This formatter decorator tries to find these error spots early.
 */
class MissingParameterWarningDecorator extends AbstractFormatterDecorator
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * Creates a decorator for the provided formatter.
     *
     * @param \Webfactory\IcuTranslationBundle\Translator\Formatting\FormatterInterface $innerFormatter
     * @param LoggerInterface $logger
     */
    public function __construct(FormatterInterface $innerFormatter, LoggerInterface $logger = null)
    {
        parent::__construct($innerFormatter);
        $this->logger = ($logger !== null) ? $logger : new NullLogger();
    }
}
