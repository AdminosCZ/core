<?php

declare(strict_types=1);

namespace Adminos\Core\Plugin;

use RuntimeException;

/**
 * @api
 */
final class InvalidManifestException extends RuntimeException
{
    public static function missingId(string $package): self
    {
        return new self(sprintf(
            'Plugin manifest for package "%s" is missing required field "id".',
            $package,
        ));
    }

    public static function malformedId(string $package, string $id): self
    {
        return new self(sprintf(
            'Plugin manifest "id" must be a kebab-case slug (lowercase letters, digits, hyphens). Got "%s" in package "%s".',
            $id,
            $package,
        ));
    }

    public static function duplicateId(string $id, string $existingPackage, string $newPackage): self
    {
        return new self(sprintf(
            'Two packages declare the same plugin id "%s": "%s" and "%s". Plugin ids must be unique.',
            $id,
            $existingPackage,
            $newPackage,
        ));
    }
}
