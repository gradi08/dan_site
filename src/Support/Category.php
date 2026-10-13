<?php

declare(strict_types=1);

namespace App\Support;

final class Category
{
    public const ALL = [
        'meubles'      => 'Meubles',
        'electronique' => 'Électronique',
        'vetements'    => 'Vêtements',
        'livres'       => 'Livres',
        'maison'       => 'Maison & cuisine',
        'enfants'      => 'Enfants',
        'loisirs'      => 'Sport & loisirs',
        'autre'        => 'Autre',
    ];

    public static function label(?string $slug): string
    {
        return self::ALL[$slug] ?? self::ALL['autre'];
    }

    public static function slugs(): string
    {
        return implode(',', array_keys(self::ALL));
    }
}
