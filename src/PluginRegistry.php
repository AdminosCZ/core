<?php

declare(strict_types=1);

namespace Adminos\Core;

use Adminos\Core\Plugin\Manifest;

/**
 * @api
 */
final class PluginRegistry
{
    /** @var array<string, Manifest> */
    private array $plugins = [];

    public function register(Manifest $manifest): void
    {
        $this->plugins[$manifest->id] = $manifest;
    }

    public function get(string $id): ?Manifest
    {
        return $this->plugins[$id] ?? null;
    }

    public function has(string $id): bool
    {
        return isset($this->plugins[$id]);
    }

    /**
     * @return array<string, Manifest>
     */
    public function all(): array
    {
        return $this->plugins;
    }
}
