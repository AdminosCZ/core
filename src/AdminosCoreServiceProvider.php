<?php

declare(strict_types=1);

namespace Adminos\Core;

use Adminos\Core\Plugin\ComposerInstalledPackagesProvider;
use Adminos\Core\Plugin\InstalledPackagesProvider;
use Adminos\Core\Plugin\ManifestLoader;
use Filament\Forms\Components\Toggle;
use Filament\Tables\Columns\ToggleColumn;
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

        $this->configureToggleDefaults();
    }

    /**
     * Glob ální barvy pro Toggle (form) i ToggleColumn (table inline).
     * Napříč celým ADMINOS systémem: ON = success (zelená),
     * OFF = danger (červená) — bez ohledu na branding scheme. Boolean
     * stav má být čitelný na první pohled, ne odvíjet se od primary
     * paletty klienta.
     *
     * `isImportant: true` zajistí, že per-instance `->onColor('primary')`
     * apod. tuto vrstvu nepřepisuje (admin musí aktivně zavolat
     * `->onColor()` znovu po configureUsing, což explicitně signalizuje
     * že chce odchylku).
     */
    private function configureToggleDefaults(): void
    {
        if (! class_exists(Toggle::class)) {
            return;
        }

        Toggle::configureUsing(
            fn (Toggle $toggle): Toggle => $toggle
                ->onColor('success')
                ->offColor('danger'),
            isImportant: true,
        );

        ToggleColumn::configureUsing(
            fn (ToggleColumn $toggle): ToggleColumn => $toggle
                ->onColor('success')
                ->offColor('danger'),
            isImportant: true,
        );
    }
}
