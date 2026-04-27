<?php

declare(strict_types=1);

namespace Adminos\Core\Plugin;

/**
 * Value object representing a single ADMINOS plugin manifest.
 *
 * @api
 */
final class Manifest
{
    private const ID_PATTERN = '/^[a-z0-9]+(-[a-z0-9]+)*$/';

    public function __construct(
        public readonly string $id,
        public readonly string $name,
        public readonly string $description,
        public readonly string $package,
        public readonly string $version,
    ) {
    }

    /**
     * @param  array<string, mixed>  $extra  contents of `composer.json > extra.adminos`
     */
    public static function fromComposerExtra(array $extra, string $package, string $version): self
    {
        $id = $extra['id'] ?? null;

        if (! is_string($id) || $id === '') {
            throw InvalidManifestException::missingId($package);
        }

        if (preg_match(self::ID_PATTERN, $id) !== 1) {
            throw InvalidManifestException::malformedId($package, $id);
        }

        $name = isset($extra['name']) && is_string($extra['name']) && $extra['name'] !== ''
            ? $extra['name']
            : $id;

        $description = isset($extra['description']) && is_string($extra['description'])
            ? $extra['description']
            : '';

        return new self(
            id: $id,
            name: $name,
            description: $description,
            package: $package,
            version: $version,
        );
    }
}
