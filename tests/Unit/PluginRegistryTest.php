<?php

declare(strict_types=1);

namespace Adminos\Core\Tests\Unit;

use Adminos\Core\Plugin\Manifest;
use Adminos\Core\PluginRegistry;
use PHPUnit\Framework\TestCase;

final class PluginRegistryTest extends TestCase
{
    public function test_register_and_retrieve(): void
    {
        $registry = new PluginRegistry();
        $manifest = new Manifest(
            id: 'feedmanager',
            name: 'Feed Manager',
            description: 'Handles product feeds.',
            package: 'adminos/feedmanager',
            version: '1.0.0',
        );

        $registry->register($manifest);

        $this->assertTrue($registry->has('feedmanager'));
        $this->assertSame($manifest, $registry->get('feedmanager'));
    }

    public function test_get_returns_null_for_unknown_plugin(): void
    {
        $registry = new PluginRegistry();

        $this->assertNull($registry->get('unknown'));
        $this->assertFalse($registry->has('unknown'));
    }

    public function test_all_returns_every_registered_plugin(): void
    {
        $registry = new PluginRegistry();

        $a = new Manifest(id: 'a', name: 'A', description: '', package: 'adminos/a', version: '0.1.0');
        $b = new Manifest(id: 'b', name: 'B', description: '', package: 'adminos/b', version: '0.1.0');

        $registry->register($a);
        $registry->register($b);

        $this->assertSame(['a' => $a, 'b' => $b], $registry->all());
    }
}
