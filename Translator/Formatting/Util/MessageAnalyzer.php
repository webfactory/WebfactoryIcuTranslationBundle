<?php

namespace Webfactory\IcuTranslationBundle\Translator\Formatting\Util;

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
        $pattern = '/\{(?P<parameters>[a-zA-Z0-9_]+)(,|\})/u';
        $matches = array();
        preg_match_all($pattern, $this->message, $matches,  PREG_PATTERN_ORDER);
        $parameters = array_unique($matches['parameters']);
        return $parameters;
    }
}
