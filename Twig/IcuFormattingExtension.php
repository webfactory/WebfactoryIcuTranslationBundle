<?php

namespace Webfactory\IcuTranslationBundle\Twig;

use Symfony\Component\Translation\TranslatorInterface;
use Webfactory\IcuTranslationBundle\Translator\Formatting\FormatterInterface;

class IcuFormattingExtension extends \Twig_Extension
{
    /**
     * @var FormatterInterface
     */
    private $formatter;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    public function __construct(FormatterInterface $formatter, TranslatorInterface $translator)
    {
        $this->formatter = $formatter;
        $this->translator = $translator;
    }

    public function getFilters()
    {
        return [
            new \Twig_SimpleFilter('icu_format', [$this, 'format']),
        ];
    }

    public function format($message = '', $parameters = [], $locale = null)
    {
        return $this->formatter->format($locale ?: $this->translator->getLocale(), $message, $parameters);
    }
}
