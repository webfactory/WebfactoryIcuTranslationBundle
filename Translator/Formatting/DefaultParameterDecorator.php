<?php

namespace Webfactory\IcuTranslationBundle\Translator\Formatting;

/**
 * Injects default parameters for placeholders that occur in a message.
 *
 * This decorator exists mainly for backward compatibility after removing the parser/lexer.
 * Injecting defaults ensures for example that an expression evaluates to false even if
 * the checked parameter was not passed.
 *
 * The decorator works on a best-effort basis: It does not guarantee that defaults are *only*
 * added for real parameters as placeholder nesting can be quote complex.
 * So in some cases superfluous defaults *might* be passed, which are usually ignored by
 * the following formatter.
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
