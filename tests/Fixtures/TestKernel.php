<?php

namespace Webfactory\IcuTranslationBundle\Tests\Fixtures;

use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Bundle\TwigBundle\TwigBundle;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Routing\RouteCollectionBuilder;
use Webfactory\IcuTranslationBundle\WebfactoryIcuTranslationBundle;

/**
 * A minimal kernel that is used for testing.
 */
final class TestKernel extends Kernel
{
    use MicroKernelTrait;

    /**
     * @return BundleInterface[] An array of bundle instances
     */
    public function registerBundles(): array
    {
        return [
            new FrameworkBundle(),
            new WebfactoryIcuTranslationBundle(),
            new TwigBundle(),
        ];
    }

    protected function configureContainer(ContainerBuilder $container, LoaderInterface $loader): void
    {
        $loader->load(__DIR__.'/config/config.yml');
    }

    public function getCacheDir(): string
    {
        return __DIR__.'/cache/'.$this->environment;
    }

    public function getLogDir(): string
    {
        return __DIR__.'/logs';
    }

    /**
     * Add or import routes into your application.
     *
     *     $routes->import('config/routing.yml');
     *     $routes->add('/admin', 'AppBundle:Admin:dashboard', 'admin_dashboard');
     */
    protected function configureRoutes(RouteCollectionBuilder $routes): void
    {
    }
}
