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
     * @param string $message
     */
    public function __construct($message)
    {

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

    }
}
