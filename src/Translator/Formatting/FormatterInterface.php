<?php

namespace Webfactory\IcuTranslationBundle\Translator\Formatting;

/**
 * Interface for classes that are used to format a translation message.
 */
interface FormatterInterface
{
    /**
     * Formats the provided message.
     *
     * @param string $locale
     * @param string $message
     * @param array(string=>mixed) $parameters
     *
     * @return string the formatted message
     */
    public function format($locale, $message, array $parameters);
}
