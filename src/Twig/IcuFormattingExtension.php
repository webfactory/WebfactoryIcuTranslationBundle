<?php

namespace Webfactory\IcuTranslationBundle\Twig;

use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Webfactory\IcuTranslationBundle\Translator\Formatting\FormatterInterface;

class IcuFormattingExtension extends AbstractExtension
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
            new TwigFilter('icu_format', [$this, 'format']),
        ];
    }

    public function format(string $message = '', array $parameters = [], string $locale = null): string
    {
        return $this->formatter->format($locale ?: $this->translator->getLocale(), $message, $parameters);
    }
}
