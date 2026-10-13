<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Support\Phone;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class PhoneTest extends TestCase
{
    /** @return iterable<string, array{string, string}> */
    public static function numbers(): iterable
    {
        yield 'format local avec 0' => ['0812345678', '+243812345678'];
        yield 'avec espaces' => ['081 234 56 78', '+243812345678'];
        yield 'déjà international' => ['+243 81 234 5678', '+243812345678'];
        yield 'préfixe 00' => ['00243812345678', '+243812345678'];
        yield 'indicatif sans +' => ['243812345678', '+243812345678'];
        yield 'sans le 0' => ['812345678', '+243812345678'];
        yield 'autre pays conservé' => ['+33 6 12 34 56 78', '+33612345678'];
    }

    #[DataProvider('numbers')]
    public function testNormalize(string $input, string $expected): void
    {
        self::assertSame($expected, Phone::normalize($input));
    }

    public function testWhatsappLinkEncodesMessage(): void
    {
        self::assertSame(
            'https://wa.me/243812345678?text=Bonjour%20%C3%A0%20vous',
            Phone::whatsappLink('+243812345678', 'Bonjour à vous'),
        );
    }
}
