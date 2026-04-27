<?php

declare(strict_types=1);

namespace Adminos\Core\Plugin;

/**
 * Reads the package list from composer's `vendor/composer/installed.json`,
 * which Composer maintains for every project after `composer install`.
 *
 * @api
 */
final class ComposerInstalledPackagesProvider implements InstalledPackagesProvider
{
    public function __construct(private readonly string $vendorPath)
    {
    }

    public function all(): array
    {
        $path = $this->vendorPath . '/composer/installed.json';

        if (! is_file($path)) {
            return [];
        }

        $contents = file_get_contents($path);

        if ($contents === false) {
            return [];
        }

        $decoded = json_decode($contents, true);

        if (! is_array($decoded)) {
            return [];
        }

        $packages = $decoded['packages'] ?? $decoded;

        return is_array($packages) ? array_values($packages) : [];
    }
}
