<?php

namespace Webfactory\IcuTranslationBundle\Translator\Formatting;

/**
 * Base class for formatter decorators.
 */
abstract class AbstractFormatterDecorator implements FormatterInterface
{
    /**
     * The inner formatter.
     *
     * @var \Webfactory\IcuTranslationBundle\Translator\Formatting\FormatterInterface
     */
    protected $innerFormatter = null;

    /**
     * Creates a decorator for the provided formatter.
     *
     * @param \Webfactory\IcuTranslationBundle\Translator\Formatting\FormatterInterface $innerFormatter
     */
    public function __construct(FormatterInterface $innerFormatter)
    {
        $this->innerFormatter = $innerFormatter;
    }

    /**
     * Formats the provided message.
     *
     * @param string               $locale
     * @param string               $message
     * @param array(string=>mixed) $parameters
     *
     * @return string the formatted message
     */
    public function format($locale, $message, array $parameters)
    {
        return $this->innerFormatter->format(
            $this->preProcessLocale($locale),
            $this->preProcessMessage($message),
            $this->preProcessParameters($parameters)
        );
    }

    /**
     * Pre-processes the locale before it is passed to the inner formatter.
     *
     * @param string $locale for example 'en' or 'de_DE'
     *
     * @return string
     */
    protected function preProcessLocale($locale)
    {
        return $locale;
    }

    /**
     * Pre-processes the message before it is passed to the inner formatter.
     *
     * @param string $message the translation message
     *
     * @return string
     */
    protected function preProcessMessage($message)
    {
        return $message;
    }

    /**
     * Pre-processes the parameters before these are passed to the inner formatter.
     *
     * @param array(string=>mixed) $parameters
     *
     * @return array(string=>mixed)
     */
    protected function preProcessParameters(array $parameters)
    {
        return $parameters;
    }
}
