<?php

declare(strict_types=1);

namespace App\Service;

/**
 * Upload d'images sécurisé :
 *  - type détecté sur le contenu réel (finfo), jamais sur l'extension fournie ;
 *  - image ré-encodée avec GD → supprime tout code embarqué et les métadonnées EXIF (GPS…) ;
 *  - redimensionnée, nom de fichier aléatoire, extension imposée.
 */
final class ImageUploader
{
    private const ALLOWED = ['image/jpeg', 'image/png', 'image/webp'];

    public function __construct(
        private readonly string $directory,
        private readonly int $maxBytes,
        private readonly int $maxSide,
    ) {
    }

    /**
     * @param array<string, mixed> $file Entrée de $_FILES
     * @throws UploadException
     */
    public function store(array $file): string
    {
        if (($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
            throw new UploadException(match ($file['error'] ?? null) {
                UPLOAD_ERR_INI_SIZE, UPLOAD_ERR_FORM_SIZE => $this->sizeMessage(),
                UPLOAD_ERR_PARTIAL => "L'envoi de la photo a été interrompu. Réessayez.",
                default => "La photo n'a pas pu être envoyée. Réessayez.",
            });
        }

        $tmp = (string) $file['tmp_name'];
        if (!is_uploaded_file($tmp) && PHP_SAPI !== 'cli') {
            throw new UploadException("La photo n'a pas pu être envoyée. Réessayez.");
        }
        if ((int) $file['size'] > $this->maxBytes) {
            throw new UploadException($this->sizeMessage());
        }

        $mime = (new \finfo(FILEINFO_MIME_TYPE))->file($tmp);
        if (!in_array($mime, self::ALLOWED, true)) {
            throw new UploadException('Format non pris en charge. Utilisez une photo JPG, PNG ou WebP.');
        }

        $source = match ($mime) {
            'image/jpeg' => @imagecreatefromjpeg($tmp),
            'image/png'  => @imagecreatefrompng($tmp),
            'image/webp' => @imagecreatefromwebp($tmp),
        };
        if ($source === false) {
            throw new UploadException('Cette image semble endommagée. Essayez avec une autre photo.');
        }

        if ($mime === 'image/jpeg') {
            $source = $this->applyExifOrientation($source, $tmp);
        }

        $image = $this->resize($source);
        $name = bin2hex(random_bytes(16)) . '.jpg';

        if (!is_dir($this->directory)) {
            mkdir($this->directory, 0775, true);
        }
        if (!imagejpeg($image, $this->directory . '/' . $name, 82)) {
            throw new UploadException("La photo n'a pas pu être enregistrée. Réessayez.");
        }
        imagedestroy($image);

        return $name;
    }

    public function delete(?string $name): void
    {
        // Seuls les fichiers générés par store() sont supprimables (pas d'URL distante ni de « ../ »).
        if ($name !== null && preg_match('/^[a-f0-9]{32}\.jpg$/', $name)) {
            $path = $this->directory . '/' . $name;
            if (is_file($path)) {
                unlink($path);
            }
        }
    }

    private function resize(\GdImage $source): \GdImage
    {
        $w = imagesx($source);
        $h = imagesy($source);
        $ratio = min(1, $this->maxSide / max($w, $h));
        $nw = (int) round($w * $ratio);
        $nh = (int) round($h * $ratio);

        $canvas = imagecreatetruecolor($nw, $nh);
        // Fond blanc pour les PNG/WebP transparents (export en JPEG).
        imagefill($canvas, 0, 0, imagecolorallocate($canvas, 255, 255, 255));
        imagecopyresampled($canvas, $source, 0, 0, 0, 0, $nw, $nh, $w, $h);
        imagedestroy($source);

        return $canvas;
    }

    private function applyExifOrientation(\GdImage $image, string $path): \GdImage
    {
        if (!function_exists('exif_read_data')) {
            return $image;
        }
        $orientation = (int) (@exif_read_data($path)['Orientation'] ?? 1);
        $rotated = match ($orientation) {
            3 => imagerotate($image, 180, 0),
            6 => imagerotate($image, -90, 0),
            8 => imagerotate($image, 90, 0),
            default => false,
        };

        return $rotated ?: $image;
    }

    private function sizeMessage(): string
    {
        return sprintf('La photo dépasse %d Mo. Choisissez une image plus légère.', intdiv($this->maxBytes, 1024 * 1024));
    }
}
