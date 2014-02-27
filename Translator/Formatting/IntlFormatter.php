<?php

namespace Webfactory\TranslationBundle\Translator\Formatting;

use Webfactory\TranslationBundle\Translator\Formatting\Exception\CannotInstantiateFormatterException;
use Webfactory\TranslationBundle\Translator\Formatting\Exception\CannotFormatException;

/**
 * Formatter that uses the Intl extension to format messages.
 */
class IntlFormatter implements FormatterInterface
{

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
        $formatter = new \MessageFormatter($locale, $message);
        if (!$formatter) {
            throw new CannotInstantiateFormatterException(
                intl_get_error_message(),
                intl_get_error_code()
            );
        }

        $result = $formatter->format($parameters);
        if ($result === false) {
            throw new CannotFormatException(
                $formatter->getErrorMessage(),
                $formatter->getErrorCode()
            );
        }

        return $result;
    }

}
