<?php

namespace Webfactory\TranslatorBundle\DependencyInjection;

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

        // Copy the existing service definition...
        $container->setDefinition('webfactory_translator.inner_translator', $definition);

        // ... and replace with a decorated version.
        $decorated = new Definition(
            'Webfactory\TranslatorBundle\Translator\FormatterDecorator',
            array(
                new Reference('webfactory_translator.inner_translator'),
                new Reference('webfactory_translator.formatter.twig_parameter_normalizer')
            )
        );
        $container->setDefinition('translator', $decorated);
    }

    /**
     * Returns the definition of the provided service.
     *
     * This method also resolves aliases.
     *
     * @param ContainerBuilder $container
     * @param string $id
     * @return \Symfony\Component\DependencyInjection\Definition|null
     */
    protected function getDefinition(ContainerBuilder $container, $id)
    {
        while ($container->hasAlias($id)) {
            $id = (string)$container->getAlias($id);
        }
        if (!$container->hasDefinition($id)) {
            return null;
        }
        return $container->getDefinition($id);
    }

}
