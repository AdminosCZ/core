<?php

declare(strict_types=1);

namespace Adminos\Core;

use Adminos\Core\Plugin\ComposerInstalledPackagesProvider;
use Adminos\Core\Plugin\InstalledPackagesProvider;
use Adminos\Core\Plugin\ManifestLoader;
use Illuminate\Support\ServiceProvider;

/**
 * @api
 */
final class AdminosCoreServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(PluginRegistry::class);

        $this->app->bind(
            InstalledPackagesProvider::class,
            fn () => new ComposerInstalledPackagesProvider($this->app->basePath('vendor')),
        );

        $this->app->singleton(ManifestLoader::class);
    }

    public function boot(): void
    {
        $this->app->make(ManifestLoader::class)->load();
    }
}
