<?php

namespace Webfactory\IcuTranslationBundle\Translator;

use Symfony\Component\Translation\TranslatorInterface as LegacyTranslatorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Webfactory\IcuTranslationBundle\Translator\Formatting\FormatterInterface;

/**
 * Decorates a Symfony translator and adds support for message formatting.
 */
class FormatterDecorator implements LegacyTranslatorInterface, TranslatorInterface
{
    /**
     * The inner translator.
     *
     * @var \Symfony\Component\Translation\TranslatorInterface
     */
    protected $translator;

    /**
     * The formatter that is used to apply message transformations.
     *
     * @var \Webfactory\IcuTranslationBundle\Translator\Formatting\IntlFormatter
     */
    protected $formatter;

    /**
     * Creates a decorator for the provided translator.
     *
     * @param \Webfactory\IcuTranslationBundle\Translator\Formatting\FormatterInterface the formatter that is used
     */
    public function __construct(LegacyTranslatorInterface $translator, FormatterInterface $formatter)
    {
        $this->translator = $translator;
        $this->formatter = $formatter;
    }

    /**
     * Translates the given message.
     *
     * @param string $id         The message id
     * @param array  $parameters An array of parameters for the message
     * @param string $domain     The domain for the message
     * @param string $locale     The locale
     *
     * @return string The translated string
     */
    public function trans($id, array $parameters = [], $domain = null, $locale = null)
    {
        $message = $this->translator->trans($id, $parameters, $domain, $locale);

        return $this->handleFormatting($id, $message, $parameters, $locale);
    }

    /**
     * Translates the given choice message by choosing a translation according to a number.
     *
     * @param string $id         The message id
     * @param int    $number     The number to use to find the indice of the message
     * @param array  $parameters An array of parameters for the message
     * @param string $domain     The domain for the message
     * @param string $locale     The locale
     *
     * @return string The translated string
     */
    public function transChoice($id, $number, array $parameters = [], $domain = null, $locale = null)
    {
        $message = $this->translator->transChoice($id, $number, $parameters, $domain, $locale);

        return $this->handleFormatting($id, $message, $parameters, $locale);
    }

    /**
     * Sets the current locale.
     *
     * @param string $locale The locale
     */
    public function setLocale($locale)
    {
        $this->translator->setLocale($locale);
    }

    /**
     * Returns the current locale.
     *
     * @return string The locale
     */
    public function getLocale()
    {
        return $this->translator->getLocale();
    }

    /**
     * Formats the message if possible and throws a normalized exception in case of error.
     *
     * @param string       $id         the translation message ID
     * @param string       $message    the message pattern that will be used for formatting
     * @param array(mixed) $parameters
     * @param string|null  $locale
     *
     * @return string the formatted message
     *
     * @throws \Webfactory\IcuTranslationBundle\Translator\FormattingException if formatting fails
     */
    protected function handleFormatting($id, $message, array $parameters, $locale)
    {
        if (empty($message)) {
            // No formatting needed.
            return $message;
        }
        $locale = $this->toLocale($locale);
        try {
            return $this->format($message, $parameters, $locale);
        } catch (\Exception $e) {
            throw new FormattingException($locale, $id, $message, $parameters, $e);
        }
    }

    /**
     * Applies Intl formatting on the provided message.
     *
     * @param string       $message
     * @param array(mixed) $parameters
     * @param string       $locale
     *
     * @return string
     */
    protected function format($message, array $parameters, $locale)
    {
        return $this->formatter->format($locale, $message, $parameters);
    }

    /**
     * Returns a valid locale.
     *
     * If a correct locale is provided that one is used.
     * Otherwise, the default locale is returned.
     *
     * @param string|null $locale
     *
     * @return string
     */
    protected function toLocale($locale = null)
    {
        return (null === $locale) ? $this->getLocale() : $locale;
    }
}
