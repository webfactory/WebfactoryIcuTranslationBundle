<?php

namespace Webfactory\IcuTranslationBundle\Translator\Formatting;

/**
 * Converts named parameters in a message to positional parameters.
 *
 * Example:
 *
 *     Hello {name}!
 *
 * Will be converted into:
 *
 *     Hello {0}!
 *
 * The conversion is necessary as named parameters are only supported in PHP 5.5 and above.
 *
 */
class NamedToPositionalParameterDecorator extends AbstractFormatterDecorator
{

    /**
     * The message parser that is used internally.
     *
     * @var \Webfactory\IcuTranslationBundle\Translator\Formatting\MessageParser
     */
    protected $parser = null;

    /**
     * Creates a decorator for the provided formatter.
     *
     * The decorator needs a parser that is used to analyze and modify the translation message.
     *
     * @param \Webfactory\IcuTranslationBundle\Translator\Formatting\FormatterInterface $innerFormatter
     * @param \Webfactory\IcuTranslationBundle\Translator\Formatting\MessageParser $parser
     */
    public function __construct(FormatterInterface $innerFormatter, MessageParser $parser)
    {
        parent::__construct($innerFormatter);
        $this->parser = $parser;
    }

    /**
     * Formats the provided message.
     *
     * @param string $locale
     * @param string $message
     * @param array(string=>mixed) $parameters
     * @return string The formatted message.
     */
    public function format($locale, $message, array $parameters)
    {
        $result = $this->parser->parse($message);
        $positionalParameters = array();
        foreach ($result->mapping as $name => $index) {
            $positionalParameters[$index] = isset($parameters[$name]) ? $parameters[$name] : null;
        }
        return $this->innerFormatter->format($locale, $result->message, $positionalParameters);
    }

}
 