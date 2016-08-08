<?php

namespace Webfactory\IcuTranslationBundle\Translator\Formatting\Analysis;

use JMS\Parser\AbstractParser;

/**
 * Parses a translation message and returns the tokens of that message.
 *
 * @internal
 */
class MessageParser extends AbstractParser
{
    /**
     * State if the parser is currently in a text block.
     */
    const STATE_TEXT = 'text';

    /**
     * State if the parser has found the start of a declaration (started via opening brace).
     */
    const STATE_DECLARATION_START = 'declaration_start';

    /**
     * State if the a variable name is encountered.
     */
    const STATE_DECLARATION_VARIABLE = 'declaration_variable';

    /**
     * State if an operation is encountered (for example select expression
     * or number formatting identifier).
     */
    const STATE_DECLARATION_OPERATION = 'declaration_operation';

    /**
     * State if the parser is in an argument block (date format etc.).
     */
    const STATE_DECLARATION_ARGUMENT = 'declaration_argument';

    /**
     * State if the parser is in an expression (select, choice, plural).
     */
    const STATE_DECLARATION_EXPRESSION = 'declaration_expression';

    /**
     * State if the parser is in a block of quoted text.
     */
    const STATE_QUOTED_TEXT = 'quoted_text';

    /**
     * Token for parameter names, for example the "name" in {name}.
     */
    const TOKEN_PARAMETER_NAME = 'parameter_name';

    /**
     * Token for choice types, for example the "select" in {name, select, [...]}
     */
    const TOKEN_CHOICE_TYPE = 'choice_type';

    /**
     * Stack whose top element holds the current parsing state.
     *
     * @var \SplStack|null
     */
    private $state = null;

    /**
     * Parses the message and returns the tokens.
     *
     * The result is an array of tokens.
     * Each token is an array that consists of the token type as
     * first value and the message part as second value.
     * The token type is always one of the MessageLexer::TOKEN_* or
     * MessageParser::TOKEN_* constants.
     *
     * @param string $message
     * @param string|null $context
     * @return array<array<string>> The message tokens.
     */
    public function parse($message, $context = null)
    {
        if (strpos($message, '{') === false) {
            // Message does not contain any declarations, therefore, we can avoid
            // the parsing process.
            return array(array(MessageLexer::TOKEN_TEXT, $message));
        }
        return parent::parse($message, $context);
    }

    /**
     * Performs the parsing and creates the result that is returned by parse().
     *
     * @return array<array<string>> The message tokens.
     */
    protected function parseInternal()
    {
        $this->state = new \SplStack();
        $this->enterState(self::STATE_TEXT);
        $tokens = array();
        $this->lexer->moveNext();
        while ($this->lexer->token !== null) {
            $tokenType = $this->getTokenType();

            if ($this->isState(self::STATE_TEXT)) {
                if ($this->isToken(MessageLexer::TOKEN_SINGLE_QUOTE)) {
                    $this->enterState(self::STATE_QUOTED_TEXT);
                } elseif ($this->isToken(MessageLexer::TOKEN_OPENING_BRACE)) {
                    // Enter a new declaration scope.
                    $this->enterState(self::STATE_DECLARATION_START);
                } elseif ($this->isToken(MessageLexer::TOKEN_CLOSING_BRACE)) {
                    $this->leaveState();
                }

            } elseif ($this->isState(self::STATE_DECLARATION_START)) {
                if ($this->isToken(MessageLexer::TOKEN_TEXT)) {
                    $this->swapState(self::STATE_DECLARATION_VARIABLE);
                    $tokenType = self::TOKEN_PARAMETER_NAME;
                }

            } elseif ($this->isState(self::STATE_DECLARATION_VARIABLE)) {
                if ($this->isToken(MessageLexer::TOKEN_COMMA)) {
                    $this->swapState(self::STATE_DECLARATION_OPERATION);
                } elseif ($this->isToken(MessageLexer::TOKEN_CLOSING_BRACE)) {
                    $this->leaveState();
                }

            } elseif ($this->isState(self::STATE_DECLARATION_OPERATION)) {
                if ($this->isToken(MessageLexer::TOKEN_TEXT)) {
                    if (in_array($this->getTokenValue(), array('select', 'choice', 'plural'))) {
                        $this->swapState(self::STATE_DECLARATION_EXPRESSION);
                        $tokenType = self::TOKEN_CHOICE_TYPE;
                    }
                } elseif ($this->isToken(MessageLexer::TOKEN_COMMA)) {
                    $this->swapState(self::STATE_DECLARATION_ARGUMENT);
                } elseif ($this->isToken(MessageLexer::TOKEN_CLOSING_BRACE)) {
                    $this->leaveState();
                }

            } elseif ($this->isState(self::STATE_DECLARATION_ARGUMENT)) {
                if ($this->isToken(MessageLexer::TOKEN_CLOSING_BRACE)) {
                    $this->leaveState();
                }

            } elseif ($this->isState(self::STATE_DECLARATION_EXPRESSION)) {
                if ($this->isToken(MessageLexer::TOKEN_OPENING_BRACE)) {
                    $this->enterState(self::STATE_TEXT);
                }
            } elseif ($this->isState(self::STATE_QUOTED_TEXT)) {
                if ($this->isToken(MessageLexer::TOKEN_SINGLE_QUOTE)) {
                    $this->leaveState();
                }
            }

            $tokens[] = array($tokenType, $this->getTokenValue());

            $this->lexer->moveNext();
        }

        return $tokens;
    }

    /**
     * Checks if the current token has the provided type.
     *
     * @param integer $type
     * @return boolean
     */
    private function isToken($type)
    {
        return $this->getTokenType() === $type;
    }

    /**
     * Returns the type of the current token.
     *
     * @return integer One of the MessageLexer::TOKEN_* constants.
     */
    private function getTokenType()
    {
        return $this->lexer->token[2];
    }

    /**
     * Returns the value of the current token.
     *
     * @return string
     */
    private function getTokenValue()
    {
        return $this->lexer->token[0];
    }

    /**
     * Sets $newState as new parsing state.
     *
     * The previous state is preserved.
     *
     * @param string $newState
     */
    private function enterState($newState)
    {
        $this->state->push($newState);
    }

    /**
     * Leaves the current state and restores the previous one.
     */
    private function leaveState()
    {
        $this->state->pop();
    }

    /**
     * Removes the current state and replaces it by $newState.
     *
     * @param string $newState
     */
    private function swapState($newState)
    {
        $this->leaveState();
        $this->enterState($newState);
    }

    /**
     * Checks if $checkedState is the current state.
     *
     * @param string $checkedState
     * @return boolean
     */
    private function isState($checkedState)
    {
        return $this->state->top() === $checkedState;
    }
}
