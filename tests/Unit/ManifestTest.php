<?php

declare(strict_types=1);

namespace Adminos\Core\Tests\Unit;

use Adminos\Core\Plugin\InvalidManifestException;
use Adminos\Core\Plugin\Manifest;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class ManifestTest extends TestCase
{
    public function test_from_composer_extra_builds_manifest(): void
    {
        $manifest = Manifest::fromComposerExtra(
            extra: [
                'id' => 'feedmanager',
                'name' => 'Feed Manager',
                'description' => 'Handles product feeds.',
            ],
            package: 'adminos/feedmanager',
            version: '1.2.3',
        );

        $this->assertSame('feedmanager', $manifest->id);
        $this->assertSame('Feed Manager', $manifest->name);
        $this->assertSame('Handles product feeds.', $manifest->description);
        $this->assertSame('adminos/feedmanager', $manifest->package);
        $this->assertSame('1.2.3', $manifest->version);
    }

    public function test_name_falls_back_to_id_when_missing(): void
    {
        $manifest = Manifest::fromComposerExtra(
            extra: ['id' => 'rental'],
            package: 'adminos/rental',
            version: '0.1.0',
        );

        $this->assertSame('rental', $manifest->name);
        $this->assertSame('', $manifest->description);
    }

    public function test_missing_id_throws(): void
    {
        $this->expectException(InvalidManifestException::class);
        $this->expectExceptionMessage('missing required field "id"');

        Manifest::fromComposerExtra(
            extra: ['name' => 'No id'],
            package: 'adminos/broken',
            version: '0.1.0',
        );
    }

    public function test_empty_id_throws(): void
    {
        $this->expectException(InvalidManifestException::class);

        Manifest::fromComposerExtra(
            extra: ['id' => ''],
            package: 'adminos/broken',
            version: '0.1.0',
        );
    }

    #[DataProvider('malformedIdProvider')]
    public function test_malformed_id_throws(string $id): void
    {
        $this->expectException(InvalidManifestException::class);
        $this->expectExceptionMessage('kebab-case slug');

        Manifest::fromComposerExtra(
            extra: ['id' => $id],
            package: 'adminos/broken',
            version: '0.1.0',
        );
    }

    /**
     * @return array<string, array{0: string}>
     */
    public static function malformedIdProvider(): array
    {
        return [
            'uppercase' => ['FeedManager'],
            'underscore' => ['feed_manager'],
            'leading hyphen' => ['-feed'],
            'trailing hyphen' => ['feed-'],
            'double hyphen' => ['feed--manager'],
            'dot' => ['feed.manager'],
            'slash' => ['feed/manager'],
            'space' => ['feed manager'],
        ];
    }
}
