<?php

declare(strict_types=1);

namespace Adminos\Core\Plugin;

/**
 * Source of the installed-package list the manifest loader inspects.
 *
 * Implementations decide where the data comes from (composer's installed.json,
 * an in-memory array for tests, etc.). Each entry must at least carry `name`
 * and `version`; an `extra` block is optional and only relevant for plugins.
 *
 * @api
 */
interface InstalledPackagesProvider
{
    /**
     * @return array<int, array<string, mixed>>
     */
    public function all(): array;
}
