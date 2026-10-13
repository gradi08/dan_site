<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Core\Validator;
use PHPUnit\Framework\TestCase;

final class ValidatorTest extends TestCase
{
    public function testValidDataPasses(): void
    {
        $v = Validator::make(
            ['email' => 'a@b.cd', 'password' => 'secret123', 'password_confirmation' => 'secret123'],
            ['email' => 'required|email', 'password' => 'required|min:8|same:password_confirmation'],
        );

        self::assertFalse($v->fails());
    }

    public function testReportsFirstFailingRulePerField(): void
    {
        $v = Validator::make(
            ['email' => '', 'password' => 'abc', 'password_confirmation' => 'abc'],
            ['email' => 'required|email', 'password' => 'required|min:8'],
            ['email' => 'E-mail', 'password' => 'Mot de passe'],
        );

        self::assertSame([
            'email'    => 'Le champ « E-mail » est obligatoire.',
            'password' => '« Mot de passe » doit contenir au moins 8 caractères.',
        ], $v->errors());
    }

    public function testMismatchedConfirmation(): void
    {
        $v = Validator::make(
            ['password' => 'secret123', 'password_confirmation' => 'other1234'],
            ['password' => 'same:password_confirmation'],
        );

        self::assertArrayHasKey('password', $v->errors());
    }

    public function testInRuleAndOptionalFields(): void
    {
        $v = Validator::make(
            ['status' => 'hacked', 'location' => ''],
            ['status' => 'in:draft,published', 'location' => 'max:80'],
        );

        self::assertSame(['status'], array_keys($v->errors()));
    }

    public function testMaxCountsMultibyteCharacters(): void
    {
        $v = Validator::make(['title' => str_repeat('é', 10)], ['title' => 'max:10']);

        self::assertFalse($v->fails());
    }
}
