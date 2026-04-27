<?php

declare(strict_types=1);

namespace Adminos\Core\Tests\Unit;

use Adminos\Core\Plugin\ArrayInstalledPackagesProvider;
use Adminos\Core\Plugin\InvalidManifestException;
use Adminos\Core\Plugin\ManifestLoader;
use Adminos\Core\PluginRegistry;
use PHPUnit\Framework\TestCase;

final class ManifestLoaderTest extends TestCase
{
    public function test_loads_packages_with_adminos_extra(): void
    {
        $registry = new PluginRegistry();
        $loader = new ManifestLoader(
            new ArrayInstalledPackagesProvider([
                [
                    'name' => 'adminos/feedmanager',
                    'version' => '1.0.0',
                    'extra' => [
                        'adminos' => [
                            'id' => 'feedmanager',
                            'name' => 'Feed Manager',
                            'description' => 'Handles product feeds.',
                        ],
                    ],
                ],
                [
                    'name' => 'vendor/unrelated',
                    'version' => '2.0.0',
                ],
                [
                    'name' => 'vendor/other-plugin',
                    'version' => '0.5.0',
                    'extra' => [
                        'branch-alias' => ['dev-main' => '1.x-dev'],
                    ],
                ],
            ]),
            $registry,
        );

        $loader->load();

        $this->assertCount(1, $registry->all());
        $this->assertTrue($registry->has('feedmanager'));

        $manifest = $registry->get('feedmanager');
        $this->assertNotNull($manifest);
        $this->assertSame('Feed Manager', $manifest->name);
        $this->assertSame('adminos/feedmanager', $manifest->package);
        $this->assertSame('1.0.0', $manifest->version);
    }

    public function test_no_plugins_means_empty_registry(): void
    {
        $registry = new PluginRegistry();
        $loader = new ManifestLoader(
            new ArrayInstalledPackagesProvider([
                ['name' => 'a/b', 'version' => '1.0.0'],
                ['name' => 'c/d', 'version' => '1.0.0', 'extra' => []],
            ]),
            $registry,
        );

        $loader->load();

        $this->assertSame([], $registry->all());
    }

    public function test_invalid_manifest_propagates(): void
    {
        $registry = new PluginRegistry();
        $loader = new ManifestLoader(
            new ArrayInstalledPackagesProvider([
                [
                    'name' => 'adminos/broken',
                    'version' => '1.0.0',
                    'extra' => ['adminos' => ['name' => 'No id field']],
                ],
            ]),
            $registry,
        );

        $this->expectException(InvalidManifestException::class);
        $this->expectExceptionMessage('adminos/broken');

        $loader->load();
    }

    public function test_duplicate_id_throws(): void
    {
        $registry = new PluginRegistry();
        $loader = new ManifestLoader(
            new ArrayInstalledPackagesProvider([
                [
                    'name' => 'adminos/feedmanager',
                    'version' => '1.0.0',
                    'extra' => ['adminos' => ['id' => 'feedmanager']],
                ],
                [
                    'name' => 'rival/feedmanager',
                    'version' => '1.0.0',
                    'extra' => ['adminos' => ['id' => 'feedmanager']],
                ],
            ]),
            $registry,
        );

        $this->expectException(InvalidManifestException::class);
        $this->expectExceptionMessage('feedmanager');

        $loader->load();
    }

    public function test_empty_provider_is_safe(): void
    {
        $registry = new PluginRegistry();
        $loader = new ManifestLoader(
            new ArrayInstalledPackagesProvider([]),
            $registry,
        );

        $loader->load();

        $this->assertSame([], $registry->all());
    }
}
