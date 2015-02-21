<?php

namespace Webfactory\IcuTranslationBundle\Translator\Formatting;

use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

/**
 * Catches exceptions generated in the decorated formatter to log them and to returns a string gracefully.
 *
 * @final by default.
 */
final class GracefulExceptionsDecorator extends AbstractFormatterDecorator
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
        $this->innerFormatter = $innerFormatter;
        $this->logger = ($logger !== null) ? $logger : new NullLogger();
    }

    /**
     * Formats the provided message.
     *
     * @param string $locale
     * @param string $message
     * @param array(string=>mixed) $parameters
     * @return string The formatted message.
     */
    public function format($locale, $message, array $parameters)
    {
        try {
            return parent::format($locale, $message, $parameters);
        } catch (\Exception $e) {
            $this->logger->error(
                'Formatting translation failed.',
                array(
                    'locale' => $locale,
                    'message' => $message,
                    'parameters' => $parameters,
                    'exception' => $e
                )
            );
            return ' (message formatting error)';
        }
    }
}
