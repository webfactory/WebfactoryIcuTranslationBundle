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
        $bracePattern = '/(?P<braces>\{|\})[^\\\']/u';
        $nonEscapedBraces = array();
        preg_match_all($bracePattern, $this->message, $nonEscapedBraces,  PREG_PATTERN_ORDER|PREG_OFFSET_CAPTURE);
        $openBracesBefore = function ($offset) use ($nonEscapedBraces) {
            $openBraces = 0;
            foreach ($nonEscapedBraces['braces'] as $match) {
                list($brace, $braceOffset) = $match;
                if ($braceOffset > $offset) {
                    continue;
                }
                if ($brace === '{') {
                    $openBraces++;
                } else {
                    $openBraces--;
                }
            }
            return $openBraces;
        };

        $choicesPattern = '/\{([a-zA-Z0-9_]+), (plural|select|choice),/u';
        $choices = array();
        preg_match_all($choicesPattern, $this->message, $choices,  PREG_PATTERN_ORDER|PREG_OFFSET_CAPTURE);
        $openChoicesBefore = function ($offset) use($choices) {
            $numberOfChoices = 0;
            foreach ($choices[0] as $match) {
                $matchOffset = $match[1];
                if ($matchOffset <= $offset) {
                    $numberOfChoices++;
                }
            }
            return $numberOfChoices;
        };

        $parameterPattern = '/\{(?P<parameters>[a-zA-Z0-9_]+)(,|\})/u';
        $possibleParameters = array();
        preg_match_all($parameterPattern, $this->message, $possibleParameters, PREG_PATTERN_ORDER|PREG_OFFSET_CAPTURE);
        $parameters = array();
        foreach ($possibleParameters['parameters'] as $matchIndex => $match) {
            list($name, $offset) = $match;
            //var_dump($name . ' braces:' . $openBracesBefore($offset) . ' choices:' . $openChoicesBefore($offset));
            $parameters[] = $name;
        }
        return array_values(array_unique($parameters));
    }
}
