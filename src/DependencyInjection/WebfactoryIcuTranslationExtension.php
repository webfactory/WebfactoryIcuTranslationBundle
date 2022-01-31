<?php

namespace Webfactory\IcuTranslationBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

/**
 * Loads the definitions for translation related services.
 */
class WebfactoryIcuTranslationExtension extends Extension
{
    /**
     * Adds service definitions to the container.
     *
     * @param array(string) $config
     */
    public function load(array $config, ContainerBuilder $container)
    {
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');
    }
}
