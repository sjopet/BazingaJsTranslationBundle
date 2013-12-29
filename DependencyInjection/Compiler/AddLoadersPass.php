<?php

namespace Bazinga\ExposeTranslationBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * @author William DURAND <william.durand1@gmail.com>
 */
class AddLoadersPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('bazinga.exposetranslation.controller')) {
            return;
        }

        foreach ($container->findTaggedServiceIds('translation.loader') as $loaderId => $attributes) {
            $attributes = array_shift($attributes);

            $this->registerLoader($container, $attributes['alias'], $loaderId);

            if (isset($attributes['legacy-alias'])) {
                $this->registerLoader($container, $attributes['legacy-alias'], $loaderId);
            }
        }
    }

    private function registerLoader(ContainerBuilder $container, $alias, $loaderId)
    {
        $container
            ->getDefinition('bazinga.exposetranslation.controller')
            ->addMethodCall('addLoader', array($alias, new Reference($loaderId)));

        $container
            ->getDefinition('bazinga.exposetranslation.dumper.translation_dumper')
            ->addMethodCall('addLoader', array($alias, new Reference($loaderId)));
    }
}
