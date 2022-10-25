<?php

namespace Webfactory\IcuTranslationBundle\Translator\Formatting\Analysis;

use JMS\Parser\AbstractLexer;

/**
 * Splits a translation message into tokens that can be analyzed by a parser.
 *
 * @internal
 */
class MessageLexer extends AbstractLexer
{
    /**
     * Identifies an opening brace.
     */
    public const TOKEN_OPENING_BRACE = 0;

    /**
     * Identifies a closing brace.
     */
    public const TOKEN_CLOSING_BRACE = 1;

    /**
     * Identifies a comma.
     */
    public const TOKEN_COMMA = 2;

    /**
     * Identifies whitespace (spaces, newlines, ...).
     */
    public const TOKEN_WHITESPACE = 3;

    /**
     * Identifies any text part.
     */
    public const TOKEN_TEXT = 4;

    /**
     * Identifies a single quote.
     */
    public const TOKEN_SINGLE_QUOTE = 5;

    /**
     * Regular Expression that splits messages into tokens.
     *
     * @return string
     */
    protected function getRegex()
    {
        return '/({)|(})|(\,)|(\s+)|(\')|([^\{\}\,\'\s]+)/';
    }

    /**
     * Determines the type of the given value.
     *
     * This method may also modify the passed value for example to cast them to
     * a different PHP type where necessary.
     *
     * @param string $value
     *
     * @return array a tupel of type and normalized value
     */
    protected function determineTypeAndValue($value)
    {
        if ('{' === $value) {
            return [self::TOKEN_OPENING_BRACE, $value];
        }
        if ('}' === $value) {
            return [self::TOKEN_CLOSING_BRACE, $value];
        }
        if (',' === $value) {
            return [self::TOKEN_COMMA, $value];
        }
        if ("'" === $value) {
            return [self::TOKEN_SINGLE_QUOTE, $value];
        }
        if ('' === trim($value)) {
            return [self::TOKEN_WHITESPACE, $value];
        }

        return [self::TOKEN_TEXT, $value];
    }
}
