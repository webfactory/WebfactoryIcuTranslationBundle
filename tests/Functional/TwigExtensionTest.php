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
     */
    public function icu_format_filter_expands_parameters_in_curly_braced(): void
    {
        self::assertSame('Peter will arrive shortly.', $this->renderTemplate('{{ "{name} will arrive shortly." | icu_format({name: "Peter"}) }}'));
    }

    private function renderTemplate(string $template): string
    {
        $template = $this->twig->createTemplate($template);

        return $this->twig->render($template);
    }
}
