<?php

namespace Webfactory\IcuTranslationBundle\Translator;

/**
 * Exception that wraps formatting exceptions and adds some information about the message.
 */
class FormattingException extends \Exception
{
    /**
     * The locale of the message.
     *
     * @var string
     */
    protected $locale = null;

    /**
     * The message ID of the affected translation.
     *
     * @var string
     */
    protected $messageId = null;

    /**
     * The pattern of the affected translation.
     *
     * @var string
     */
    protected $messagePattern = null;

    /**
     * The translation parameters.
     *
     * @var array(mixed)
     */
    protected $parameters = null;

    /**
     * Wraps an exception that occurred during formatting and provided additional information.
     *
     * @param string       $locale
     * @param string       $messageId
     * @param string       $messagePattern
     * @param array(mixed) $parameters
     * @param \Exception   $previous
     */
    public function __construct($locale, $messageId, $messagePattern, array $parameters, \Exception $previous)
    {
        $message = $this->toMessage($locale, $messageId, $messagePattern, $parameters, $previous->getMessage());
        parent::__construct($message, $previous->getCode(), $previous);
        $this->locale = $locale;
        $this->messageId = $messageId;
        $this->messagePattern = $messagePattern;
        $this->parameters = $parameters;
    }

    /**
     * Returns the locale of the message.
     *
     * @return string
     */
    public function getLocale()
    {
        return $this->locale;
    }

    /**
     * Returns the message ID of the affected translation.
     *
     * @return string
     */
    public function getMessageId()
    {
        return $this->messageId;
    }

    /**
     * Returns the message pattern of the affected translation.
     *
     * @return string
     */
    public function getMessagePattern()
    {
        return $this->messagePattern;
    }

    /**
     * Returns the parameters that have been passed.
     *
     * @return array(mixed)
     */
    public function getParameters()
    {
        return $this->parameters;
    }

    /**
     * Creates an error message.
     *
     * @param string       $locale         the used locale (for example "en")
     * @param string       $messageId      the translation message ID
     * @param string       $messagePattern the translation message pattern
     * @param array(mixed) $parameters
     * @param string       $error          description of the error that occurred
     *
     * @return string
     */
    protected function toMessage($locale, $messageId, $messagePattern, array $parameters, $error)
    {
        $message = 'Cannot format translation:'.PHP_EOL
                 .'-> Locale:          %s'.PHP_EOL
                 .'-> Message-ID:      %s'.PHP_EOL
                 .'-> Message-Pattern:'.PHP_EOL
                 .'%s'.PHP_EOL
                 .'-> Parameters:'.PHP_EOL
                 .'%s'.PHP_EOL
                 .'-> Error:'.PHP_EOL
                 .'%s';
        $message = sprintf($message, $locale, $messageId, $messagePattern, print_r($parameters, true), $error);

        return $message;
    }
}
