<?php

declare(strict_types=1);

namespace Database\Seeders\Demo;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

final class DemoIdentity
{
    /**
     * @return array{first_name: string, last_name: string, email: string}
     */
    public static function make(?string $email = null): array
    {
        $faker = fake('de_DE');

        return self::named($faker->firstName(), $faker->lastName(), $email);
    }

    /**
     * @return array{first_name: string, last_name: string, email: string}
     */
    public static function named(string $firstName, string $lastName, ?string $email = null): array
    {
        return [
            'first_name' => $firstName,
            'last_name' => $lastName,
            'email' => $email ?? self::emailFor($firstName, $lastName),
        ];
    }

    private static function emailFor(string $firstName, string $lastName): string
    {
        $base = Str::slug($firstName.' '.$lastName) ?: 'demo-person';
        $email = $base.'@example.test';
        $suffix = 2;

        while (
            DB::table('users')->where('email', $email)->exists()
            || DB::table('members')->where('email', $email)->exists()
        ) {
            $email = $base.'-'.$suffix++.'@example.test';
        }

        return $email;
    }
}
