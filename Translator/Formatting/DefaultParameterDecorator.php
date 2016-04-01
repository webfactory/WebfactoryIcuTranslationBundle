<?php

namespace Webfactory\IcuTranslationBundle\Translator\Formatting;

/**
 * Injects default parameters for placeholders that occur in a message.
 *
 * This decorator exists mainly for backward compatibility after removing the parser/lexer.
 * Injecting defaults ensures for example that an expression evaluates to false even if
 * the checked parameter was not passed.
 */
class DefaultParameterDecorator extends AbstractFormatterDecorator
{
    /**
     * Injects default parameters before forwarding to the inner formatter.
     *
     * @param string $locale
     * @param string $message
     * @param array(string=>mixed) $parameters
     * @return string The formatted message.
     */
    public function format($locale, $message, array $parameters)
    {
        return parent::format($locale, $message, $parameters);
    }
}
