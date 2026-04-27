<?php

declare(strict_types=1);

namespace Adminos\Core\Plugin;

use Adminos\Core\PluginRegistry;

/**
 * Walks the installed-package list, picks out packages that declare an
 * `extra.adminos` block, builds a {@see Manifest} for each and registers it
 * with the {@see PluginRegistry}.
 *
 * @api
 */
final class ManifestLoader
{
    public function __construct(
        private readonly InstalledPackagesProvider $packages,
        private readonly PluginRegistry $registry,
    ) {
    }

    public function load(): void
    {
        foreach ($this->packages->all() as $package) {
            $extra = $package['extra']['adminos'] ?? null;

            if (! is_array($extra)) {
                continue;
            }

            $manifest = Manifest::fromComposerExtra(
                extra: $extra,
                package: (string) ($package['name'] ?? 'unknown'),
                version: (string) ($package['version'] ?? '0.0.0'),
            );

            if ($this->registry->has($manifest->id)) {
                $existing = $this->registry->get($manifest->id);
                throw InvalidManifestException::duplicateId(
                    id: $manifest->id,
                    existingPackage: $existing?->package ?? 'unknown',
                    newPackage: $manifest->package,
                );
            }

            $this->registry->register($manifest);
        }
    }
}
