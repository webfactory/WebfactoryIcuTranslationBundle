<?php

namespace Webfactory\IcuTranslationBundle\Translator\Formatting;

use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Webfactory\IcuTranslationBundle\Translator\Formatting\Analysis\MessageAnalyzer;
use Webfactory\IcuTranslationBundle\Translator\Formatting\Exception\FormattingException;

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
     */
    public function __construct(FormatterInterface $innerFormatter, ?LoggerInterface $logger = null)
    {
        parent::__construct($innerFormatter);
        $this->logger = (null !== $logger) ? $logger : new NullLogger();
    }

    /**
     * Checks if all mentioned parameters are provided.
     *
     * @param string $locale
     * @param string $message
     * @param array(string=>mixed) $parameters
     *
     * @return string the formatted message
     */
    public function format($locale, $message, array $parameters)
    {
        $this->logIfParameterIsMissing($locale, $message, $parameters);

        return parent::format($locale, $message, $parameters);
    }

    /**
     * @param string $locale
     * @param string $message
     * @param array(string=>mixed) $parameters
     */
    private function logIfParameterIsMissing($locale, $message, array $parameters)
    {
        $usedParameters = (new MessageAnalyzer($message))->getParameters();
        $availableParameters = array_keys($parameters);
        $missingParameters = array_diff($usedParameters, $availableParameters);
        if (0 === \count($missingParameters)) {
            // All parameters available, nothing to do.
            return;
        }
        $logMessage = 'The parameters [%s] might be missing in locale "%s" in the message "%s".';
        $logMessage = \sprintf($logMessage, implode(',', $missingParameters), $locale, $message);
        $this->logger->warning(
            $logMessage,
            [
                'locale' => $locale,
                'message' => $message,
                'parameters' => $parameters,
                'usedParameters' => $usedParameters,
                'missingParameters' => $missingParameters,
                // Add an exception (but do not throw it) to ensure that we get a stack trace.
                'exception' => new FormattingException('Some message parameters are missing.'),
            ]
        );
    }
}
