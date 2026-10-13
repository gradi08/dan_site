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
}
