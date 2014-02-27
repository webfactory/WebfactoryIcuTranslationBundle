<?php

namespace Webfactory\TranslationBundle\Translator\Formatting;

/**
 * Converts named parameters in a message to positional parameters.
 *
 * Example:
 *
 *     Hello {name}!
 *
 * Will be converted into:
 *
 *     Hello {0}!
 *
 * The conversion is necessary as named parameters are only supported in PHP 5.5 and above.
 *
 */
class NamedToPositionalParameterDecorator extends AbstractFormatterDecorator
{

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
        // TODO: Implement format() method.
    }

}
 