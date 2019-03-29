<?php

namespace Webfactory\IcuTranslationBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Compiler pass that adds the translator decorator to the registered translation service.
 */
class DecorateTranslatorCompilerPass implements CompilerPassInterface
{
    /**
     * Decorates the registered translator if available.
     *
     * @param ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        $definition = $this->getDefinition($container, 'translator');
        if ($definition === null) {
            return;
        }

        // Define the decorator...
        $decorated = new Definition(
            'Webfactory\IcuTranslationBundle\Translator\FormatterDecorator',
            [
                new Reference('webfactory_icu_translation.decorator.inner'),
                new Reference('webfactory_icu_translation.formatter'),
            ]
        );
        $container->setDefinition('webfactory_icu_translation.decorator', $decorated);

        if (method_exists($decorated, 'setDecoratedService')) {
            // ... and use the decoration capabilities of the Symfony container if available...
            $decorated->setDecoratedService('translator');
        } else {
            // ... or copy the original translator manually and point to the decorator.
            $container->setDefinition('webfactory_icu_translation.decorator.inner', $definition);
            $container->setAlias('translator', 'webfactory_icu_translation.decorator');
        }
    }

    /**
     * Returns the definition of the provided service.
     *
     * This method also resolves aliases.
     *
     * @param ContainerBuilder $container
     * @param string           $id
     *
     * @return \Symfony\Component\DependencyInjection\Definition|null
     */
    protected function getDefinition(ContainerBuilder $container, $id)
    {
        while ($container->hasAlias($id)) {
            $id = (string) $container->getAlias($id);
        }
        if (!$container->hasDefinition($id)) {
            return null;
        }

        return $container->getDefinition($id);
    }
}
