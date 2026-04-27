<?php

declare(strict_types=1);

namespace Adminos\Core\Tests\Feature;

use Adminos\Core\Plugin\ArrayInstalledPackagesProvider;
use Adminos\Core\Plugin\InstalledPackagesProvider;
use Adminos\Core\PluginRegistry;
use Adminos\Core\Tests\TestCase;
use Illuminate\Foundation\Application;

final class ManifestLoaderBootTest extends TestCase
{
    /**
     * Bind the array provider before the core service provider boots so that
     * `ManifestLoader::load()` runs against our fixture instead of composer's
     * real installed.json.
     */
    protected function defineEnvironment($app): void
    {
        /** @var Application $app */
        $app->bind(InstalledPackagesProvider::class, fn () => new ArrayInstalledPackagesProvider([
            [
                'name' => 'adminos/feedmanager',
                'version' => '1.0.0',
                'extra' => ['adminos' => ['id' => 'feedmanager', 'name' => 'Feed Manager']],
            ],
            [
                'name' => 'vendor/unrelated',
                'version' => '2.0.0',
            ],
        ]));
    }

    public function test_registry_is_populated_during_boot(): void
    {
        $registry = $this->app->make(PluginRegistry::class);

        $this->assertCount(1, $registry->all());
        $this->assertTrue($registry->has('feedmanager'));
        $this->assertSame('Feed Manager', $registry->get('feedmanager')?->name);
        $this->assertSame('adminos/feedmanager', $registry->get('feedmanager')?->package);
    }
}
