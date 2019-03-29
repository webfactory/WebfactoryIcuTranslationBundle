<?php

namespace Webfactory\IcuTranslationBundle;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Webfactory\IcuTranslationBundle\DependencyInjection\DecorateTranslatorCompilerPass;

/**
 * Initializes the translator bundle.
 */
class WebfactoryIcuTranslationBundle extends Bundle
{
    /**
     * Ensures that the translator will be decorated.
     *
     * @param ContainerBuilder $container
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);
        $container->addCompilerPass(new DecorateTranslatorCompilerPass());
    }
}
