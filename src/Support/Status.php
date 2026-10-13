<?php

declare(strict_types=1);

namespace App\Support;

/**
 * Cycle de vie d'une annonce : brouillon → en ligne → donné.
 */
final class Status
{
    public const DRAFT = 'draft';
    public const PUBLISHED = 'published';
    public const GIVEN = 'given';

    public const LABELS = [
        self::DRAFT     => 'Brouillon',
        self::PUBLISHED => 'En ligne',
        self::GIVEN     => 'Donné',
    ];

    public static function label(string $status): string
    {
        return self::LABELS[$status] ?? $status;
    }

    public static function values(): string
    {
        return implode(',', array_keys(self::LABELS));
    }
}
