<?php

namespace Webfactory\IcuTranslationBundle\Translator;

use Symfony\Contracts\Translation\TranslatorInterface;
use Webfactory\IcuTranslationBundle\Translator\Formatting\FormatterInterface;
use Webfactory\IcuTranslationBundle\Translator\Formatting\IntlFormatter;

/**
 * Decorates a Symfony translator and adds support for message formatting.
 */
class FormatterDecorator implements TranslatorInterface
{
    /**
     * The inner translator.
     *
     * @var TranslatorInterface
     */
    protected $translator;

    /**
     * The formatter that is used to apply message transformations.
     *
     * @var IntlFormatter
     */
    protected $formatter;

    public function __construct(TranslatorInterface $translator, FormatterInterface $formatter)
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
     */
    public function trans($id, array $parameters = [], $domain = null, $locale = null): string
    {
        $message = $this->translator->trans($id, $parameters, $domain, $locale);

        return $this->handleFormatting($id, $message, $parameters, $locale);
    }

//    /**
//     * Sets the current locale.
//     *
//     * @param string $locale The locale
//     */
//    public function setLocale($locale)
//    {
//        $this->translator->setLocale($locale);
//    }

    public function getLocale(): string
    {
        return $this->translator->getLocale();
    }

    /**
     * Formats the message if possible and throws a normalized exception in case of error.
     *
     * @throws \Webfactory\IcuTranslationBundle\Translator\FormattingException if formatting fails
     */
    protected function handleFormatting(string $id, string $message, array $parameters = [], string $locale = null): string
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
     */
    protected function format(string $message, array $parameters = [], string $locale = null): string
    {
        return $this->formatter->format($locale, $message, $parameters);
    }

    /**
     * Returns a valid locale.
     *
     * If a correct locale is provided that one is used.
     * Otherwise, the default locale is returned.
     */
    protected function toLocale(string $locale = null): string
    {
        return $locale ?? $this->getLocale();
    }
}
