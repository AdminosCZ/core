<?php

declare(strict_types=1);

namespace Adminos\Core\Plugin;

/**
 * In-memory provider used by tests (and as a fallback) — pass a fixed list of
 * package entries shaped like composer's `installed.json` records.
 *
 * @api
 */
final class ArrayInstalledPackagesProvider implements InstalledPackagesProvider
{
    /**
     * @param  array<int, array<string, mixed>>  $packages
     */
    public function __construct(private readonly array $packages)
    {
    }

    public function all(): array
    {
        return $this->packages;
    }
}
