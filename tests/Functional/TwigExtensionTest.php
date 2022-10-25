<?php

namespace Webfactory\IcuTranslationBundle\Tests\Functional;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Twig\Environment;

class TwigExtensionTest extends KernelTestCase
{
    /**
     * @var Environment
     */
    protected $twig;

    protected function setUp(): void
    {
        parent::setUp();

        $kernel = static::bootKernel();
        $container = $kernel->getContainer();

        $this->twig = $container->get('twig');
    }

    /**
     * @test
     *
     * @dataProvider provideTranslationMessages
     */
    public function icu_format_filter_expands_parameters_in_curly_braces(string $expected, string $twigCode): void
    {
        self::assertSame($expected, $this->renderTemplate($twigCode));
    }

    public function provideTranslationMessages(): iterable
    {
        yield 'no-op translation' => ['test', '{{ "test" |icu_format }}'];
        yield 'substitute placeholders with curly braces' => ['test', '{{ "{param}" |icu_format({ param: "test" }) }}'];

        // Parameters passed may be surrounded by '%' signs (why is that a use-case? https://symfony.com/doc/current/translation.html#message-format)
        // and should be recognized anyway.
        yield 'strip percent signs from parameter names' => ['test', '{{ "{param}" |icu_format({ "%param%": "test" }) }}'];

        yield 'locale is used to format a number in the German locale' => ['3,141', '{{ "{param,number}" |icu_format({ param: 3.141 }, "de") }}'];
        yield 'locale is used to format a number in the English locale' => ['3.141', '{{ "{param,number}" |icu_format({ param: 3.141 }, "en") }}'];

        yield 'using select expressions' => ['A was used', '{{ "{param, select, a {A was used} other {something else}}" |icu_format({ param: "a"}) }}'];
    }

    private function renderTemplate(string $template): string
    {
        $template = $this->twig->createTemplate($template);

        return $this->twig->render($template);
    }
}
