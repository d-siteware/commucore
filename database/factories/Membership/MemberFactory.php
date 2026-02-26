<?php

declare(strict_types=1);

namespace Database\Factories\Membership;

use App\Enums\Gender;
use App\Enums\MemberType;
use App\Models\Membership\Member;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

final class MemberFactory extends Factory
{
    protected $model = Member::class;

    public function definition()
    {
        $faker = \Faker\Factory::create('de_DE');

        $gen = Gender::ma->value;

        return [
            'applied_at' => $this->faker->dateTimeBetween('-55 years', 'now')->format('Y-m-d'),
            'entered_at' => $this->faker->dateTimeBetween('-55 years', 'now')->format('Y-m-d'),
            'left_at' => null,
            'is_deducted' => false,
            'birth_date' => $this->faker->dateTimeBetween('-50 years', '-18 years')->format('Y-m-d'),
            'name' => $this->faker->lastName($gen),
            'first_name' => $this->faker->firstName($gen),
            'email' => $this->faker->safeEmail(),
            'phone' => null,
            'mobile' => $this->faker->phoneNumber(),
            'address' => $this->faker->streetAddress(),
            'city' => $this->faker->city(),
            'country' => $this->faker->country(),
            'gender' => $gen,
            'type' => $this->faker->randomElement([MemberType::ST, MemberType::MD->value]),
            'user_id' => null, // Nullable by default
        ];
    }

    public function boardMember(): MemberFactory
    {
        return $this->state([
            'type' => MemberType::MD->value,
        ]);
    }

    /**
     * Member mit User-Account
     */
    public function withUser(array $userAttributes = []): static
    {
        return $this->state(function (array $attributes) use ($userAttributes) {
            // Erstelle User mit Member-Email wenn nicht anders angegeben
            $user = User::factory()->create(array_merge([
                'email' => $attributes['email'],
                'name' => $attributes['name'],
            ], $userAttributes));

            return [
                'user_id' => $user->id,
            ];
        });
    }

    public function withAdminUser(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'user_id' => User::factory()->create([
                    'email_verified_at' => now(),
                    'is_admin' => true,
                ])->id,
            ];
        });
    }
}
