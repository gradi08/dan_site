<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Http\Request;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class RequestTest extends TestCase
{
    /** @return iterable<string, array{string, string, string}> */
    public static function deployments(): iterable
    {
        yield 'racine web = public/' => ['/index.php', '/objets/3', ''];
        yield 'sous-dossier' => ['/videgrenier/public/index.php', '/videgrenier/public/objets', '/videgrenier/public'];
        yield '.htaccess racine vers public/' => ['/public/index.php', '/objets', ''];
        yield 'sous-dossier + .htaccess racine' => ['/site/public/index.php', '/site/objets', '/site'];
    }

    #[DataProvider('deployments')]
    public function testDetectBasePath(string $script, string $uri, string $expected): void
    {
        self::assertSame($expected, Request::detectBasePath($script, $uri));
    }

    public function testInputIsTrimmed(): void
    {
        $request = new Request('POST', '/', '', [], ['title' => '  Vélo  ']);

        self::assertSame('Vélo', $request->input('title'));
        self::assertSame(['title' => 'Vélo', 'missing' => ''], $request->only(['title', 'missing']));
    }

    public function testFilesNormalizesMultipleUploadAndSkipsEmptySlots(): void
    {
        $request = new Request('POST', '/', '', [], [], [
            'images' => [
                'name'     => ['a.jpg', '', 'b.png'],
                'type'     => ['image/jpeg', '', 'image/png'],
                'tmp_name' => ['/tmp/a', '', '/tmp/b'],
                'error'    => [UPLOAD_ERR_OK, UPLOAD_ERR_NO_FILE, UPLOAD_ERR_OK],
                'size'     => [10, 0, 20],
            ],
        ]);

        $files = $request->files('images');

        self::assertCount(2, $files);
        self::assertSame(['a.jpg', 'b.png'], array_column($files, 'name'));
        self::assertSame([], $request->files('missing'));
    }

    public function testDetectsBodyRejectedForExceedingPostMaxSize(): void
    {
        $tooBig = new Request('POST', '/', '', [], [], [], ['CONTENT_LENGTH' => '50000000']);
        $normal = new Request('POST', '/', '', [], ['_token' => 'x'], [], ['CONTENT_LENGTH' => '120']);

        self::assertTrue($tooBig->exceedsPostMaxSize());
        self::assertFalse($normal->exceedsPostMaxSize());
    }
}
