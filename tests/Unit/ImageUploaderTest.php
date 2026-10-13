<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Service\ImageUploader;
use App\Service\UploadException;
use PHPUnit\Framework\TestCase;

final class ImageUploaderTest extends TestCase
{
    private string $dir;
    private ImageUploader $uploader;

    protected function setUp(): void
    {
        $this->dir = sys_get_temp_dir() . '/vg-uploads-' . bin2hex(random_bytes(4));
        $this->uploader = new ImageUploader($this->dir, 1024 * 1024, 400);
    }

    protected function tearDown(): void
    {
        array_map('unlink', glob($this->dir . '/*') ?: []);
        @rmdir($this->dir);
    }

    /** @return array<string, mixed> */
    private function file(string $path): array
    {
        return ['tmp_name' => $path, 'error' => UPLOAD_ERR_OK, 'size' => filesize($path), 'name' => basename($path)];
    }

    public function testStoresResizedJpegWithRandomName(): void
    {
        $src = tempnam(sys_get_temp_dir(), 'img');
        $image = imagecreatetruecolor(1200, 600);
        imagepng($image, $src);

        $name = $this->uploader->store($this->file($src));

        self::assertMatchesRegularExpression('/^[a-f0-9]{32}\.jpg$/', $name);
        [$w, $h, $type] = getimagesize($this->dir . '/' . $name);
        self::assertSame([400, 200, IMAGETYPE_JPEG], [$w, $h, $type]);
        unlink($src);
    }

    public function testRejectsScriptDisguisedAsImage(): void
    {
        $src = tempnam(sys_get_temp_dir(), 'php');
        file_put_contents($src, '<?php system($_GET["c"]);');

        $this->expectException(UploadException::class);
        try {
            $this->uploader->store($this->file($src));
        } finally {
            unlink($src);
        }
    }

    public function testRejectsOversizedFile(): void
    {
        $this->expectException(UploadException::class);
        $this->uploader->store(['tmp_name' => __FILE__, 'error' => UPLOAD_ERR_OK, 'size' => 2 * 1024 * 1024]);
    }

    public function testDeleteIgnoresPathTraversalAndRemoteUrls(): void
    {
        mkdir($this->dir);
        $outside = $this->dir . '/../vg-keep-me.txt';
        file_put_contents($outside, 'x');

        $this->uploader->delete('../vg-keep-me.txt');
        $this->uploader->delete('https://images.unsplash.com/photo.jpg');

        self::assertFileExists($outside);
        unlink($outside);
    }
}
