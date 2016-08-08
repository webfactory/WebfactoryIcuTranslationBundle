<?php

namespace Webfactory\IcuTranslationBundle\Translator\Formatting\Analysis;

/**
 * Helper class that is used to analyze translation messages.
 *
 * @internal
 */
class MessageAnalyzer
{
    /**
     * The message that is analyzed.
     *
     * @var string
     */
    private $message = null;

    /**
     * @param string $message
     */
    public function __construct($message)
    {
        $this->message = $message;
    }

    /**
     * Returns the names of the parameters that are used in the message.
     *
     * Example:
     *
     *     // Will return ['description', 'name'].
     *     $parameterNames = (new MessageAnalyzer('Hello {'description'} {name}'))->getParameters();
     *
     * The analyzer works on a best-effort basis: As placeholder nesting can be quite complex it does neither
     * guarantee that all parameters are found nor that the message really uses all of the returned
     * parameters.
     *
     * @return string[]
     */
    public function getParameters()
    {
        $parameters = array();
        $tokens = (new MessageParser(new MessageLexer()))->parse($this->message);
        foreach ($tokens as $token) {
            /* @var $token array */
            if ($token[0] === MessageParser::TOKEN_PARAMETER_NAME) {
                $parameters[] = $token[1];
            }
        }
        return array_values(array_unique($parameters));
    }
}
