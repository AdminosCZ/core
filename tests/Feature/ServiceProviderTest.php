<?php

declare(strict_types=1);

namespace Adminos\Core\Tests\Feature;

use Adminos\Core\AdminosCoreServiceProvider;
use Adminos\Core\PluginRegistry;
use Adminos\Core\Tests\TestCase;

final class ServiceProviderTest extends TestCase
{
    public function test_provider_is_registered(): void
    {
        $this->assertTrue(
            $this->app->providerIsLoaded(AdminosCoreServiceProvider::class)
        );
    }

    public function test_plugin_registry_is_resolved_as_singleton(): void
    {
        $first = $this->app->make(PluginRegistry::class);
        $second = $this->app->make(PluginRegistry::class);

        $this->assertSame($first, $second);
    }

    public function test_plugin_registry_starts_empty_when_no_packages_declare_manifest(): void
    {
        $registry = $this->app->make(PluginRegistry::class);

        $this->assertSame([], $registry->all());
    }
}
