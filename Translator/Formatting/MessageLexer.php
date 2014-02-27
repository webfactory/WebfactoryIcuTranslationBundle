<?php

namespace Webfactory\TranslationBundle\Translator\Formatting;

use JMS\Parser\AbstractLexer;

/**
 * Splits a translation message into tokens that can be analyzed by a parser.
 */
class MessageLexer extends AbstractLexer
{

    /**
     * Identifies an opening brace.
     */
    const TOKEN_OPENING_BRACE = 0;

    /**
     * Identifies a closing brace.
     */
    const TOKEN_CLOSING_BRACE = 1;

    /**
     * Identifies a comma.
     */
    const TOKEN_COMMA = 2;

    /**
     * Identifies whitespace (spaces, newlines, ...).
     */
    const TOKEN_WHITESPACE = 3;

    /**
     * Identifies any text part.
     */
    const TOKEN_TEXT = 4;

    /**
     * Identifies a single quote.
     */
    const TOKEN_SINGLE_QUOTE = 5;

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
        if ($value === '{') {
            return array(self::TOKEN_OPENING_BRACE, $value);
        }
        if ($value === '}') {
            return array(self::TOKEN_CLOSING_BRACE, $value);
        }
        if ($value === ',') {
            return array(self::TOKEN_COMMA, $value);
        }
        if ($value === "'") {
            return array(self::TOKEN_SINGLE_QUOTE, $value);
        }
        if (trim($value) === '') {
            return array(self::TOKEN_WHITESPACE, $value);
        }
        return array(self::TOKEN_TEXT, $value);
    }

}