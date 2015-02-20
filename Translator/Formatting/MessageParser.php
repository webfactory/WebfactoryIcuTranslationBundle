<?php

namespace Webfactory\IcuTranslationBundle\Translator\Formatting;

use JMS\Parser\AbstractParser;

/**
 * Parse a translation message and replaces named variables with indexes,
 * which are the only supported variable types in PHP versions prior 5.5.
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
     * Stack whose top element holds the current parsing state.
     *
     * @var \SplStack|null
     */
    protected $state = null;

    /**
     * Maps parameter names to indexes.
     *
     * @var array(string=>integer)
     */
    protected $parameters = null;

    /**
     * Parses the message and replaces parameter names by numerical indexes.
     *
     * Returns an object that contains the modified message as well as the
     * parameter mapping.
     *
     * The result contains the following attributes:
     * # message - The modified message.
     * # mapping - Array that maps parameter names to indices.
     *
     * @param string $message
     * @param string|null $context
     * @return \stdClass
     */
    public function parse($message, $context = null)
    {
        if (strpos($message, '{') === false) {
            // Message does not contain any declarations, therefore, we can avoid
            // the parsing process.
            return $this->createResult($message, array());
        }
        return parent::parse($message, $context);
    }

    /**
     * Performs the parsing and creates the result object that is returned by parse().
     *
     * @return \stdClass
     */
    protected function parseInternal()
    {
        $this->state = new \SplStack();
        $this->enterState(self::STATE_TEXT);
        $this->parameters = array();
        $message = '';
        $this->lexer->moveNext();
        while ($this->lexer->token !== null) {
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
                    $name = $this->getTokenValue();
                    $this->setTokenValue($this->getParameterIndex($name));
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

            $message .= $this->getTokenValue();

            $this->lexer->moveNext();
        }

        return $this->createResult($message, $this->parameters);
    }

    /**
     * Creates a result object that contains the provided message and parameter mapping.
     *
     * @param string $message
     * @param array(string=>integer) $parameterMapping
     * @return \stdClass
     */
    protected function createResult($message, $parameterMapping)
    {
        $result = new \stdClass();
        $result->message = $message;
        $result->mapping = $parameterMapping;
        return $result;
    }

    /**
     * Returns the index of the provided parameter.
     *
     * @param string $name
     * @return integer
     */
    protected function getParameterIndex($name)
    {
        if (!isset($this->parameters[$name])) {
            $this->parameters[$name] = count($this->parameters);
        }
        return $this->parameters[$name];
    }

    /**
     * Checks if the current token has the provided type.
     *
     * @param integer $type
     * @return boolean
     */
    protected function isToken($type)
    {
        return $this->lexer->token[2] === $type;
    }

    /**
     * Returns the value of the current token.
     *
     * @return string
     */
    protected function getTokenValue()
    {
        return $this->lexer->token[0];
    }

    /**
     * Sets the value ofg the current token.
     *
     * @param string $newValue
     */
    protected function setTokenValue($newValue)
    {
        $this->lexer->token[0] = $newValue;
    }

    /**
     * Sets $newState as new parsing state.
     *
     * The previous state is preserved.
     *
     * @param string $newState
     */
    protected function enterState($newState)
    {
        $this->state->push($newState);
    }

    /**
     * Leaves the current state and restores the previous one.
     */
    protected function leaveState()
    {
        $this->state->pop();
    }

    /**
     * Removes the current state and replaces it by $newState.
     *
     * @param string $newState
     */
    protected function swapState($newState)
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
    protected function isState($checkedState)
    {
        return $this->state->top() === $checkedState;
    }
}
