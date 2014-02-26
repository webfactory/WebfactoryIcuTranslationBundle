<?php

namespace Webfactory\TranslatorBundle\Translator\Formatting;

use Webfactory\TranslatorBundle\Translator\Formatting\Exception\CannotInstantiateFormatterException;
use Webfactory\TranslatorBundle\Translator\Formatting\Exception\CannotFormatException;

/**
 * Formatter that uses the Intl extension to format messages.
 */
class IntlFormatter implements FormatterInterface
{

    /**
     * The message parser that is used internally.
     *
     * @var \Webfactory\TranslatorBundle\Translator\Formatting\MessageParser
     */
    protected $parser = null;

    /**
     * Creates the formatter.
     *
     * @param MessageParser $parser Used to parse translation messages.
     */
    public function __construct(MessageParser $parser)
    {
        $this->parser = $parser;
    }

    /**
     * Formats the message with the help of php intl extension.
     * 
     * @param string $locale
     * @param string $message
     * @param array(string=>mixed) $parameters
     * @return string
     * @throws CannotInstantiateFormatterException If the message pattern cannot be used.
     * @throws CannotFormatException If an error occurs during formatting.
     */
    public function format($locale, $message, array $parameters)
    {
        $result = $this->parser->parse($message);
        $intlParameters = array();
        foreach ($result->mapping as $name => $index) {
            $intlParameters[$index] = isset($parameters[$name]) ? $parameters[$name] : null;
        }

        $formatter = new \MessageFormatter($locale, $result->message);
        if (!$formatter) {
            throw new CannotInstantiateFormatterException(
                intl_get_error_message(),
                intl_get_error_code()
            );
        }

        $result = $formatter->format($intlParameters);
        if ($result === false) {
            throw new CannotFormatException(
                $formatter->getErrorMessage(),
                $formatter->getErrorCode()
            );
        }

        return $result;
    }

}
