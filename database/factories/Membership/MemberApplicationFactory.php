<?php

declare(strict_types=1);

namespace Database\Factories\Membership;

use App\Enums\Gender;
use App\Models\Membership\MemberApplication;
use Illuminate\Database\Eloquent\Factories\Factory;

final class MemberApplicationFactory extends Factory
{
    protected $model = MemberApplication::class;

    public function definition(): array
    {
        return [
            'token' => $this->faker->unique()->regexify('[a-f0-9]{64}'),
            'email' => $this->faker->safeEmail(),
            'name' => $this->faker->lastName(),
            'first_name' => $this->faker->firstName(),
            'gender' => Gender::ma,
            'birth_date' => $this->faker->dateTimeBetween('-50 years', '-18 years')->format('Y-m-d'),
            'birth_place' => $this->faker->city(),
            'locale' => 'de',
            'address' => $this->faker->streetAddress(),
            'zip' => $this->faker->postcode(),
            'city' => $this->faker->city(),
            'country' => 'Deutschland',
            'phone' => null,
            'mobile' => $this->faker->phoneNumber(),
            'family_status' => null,
            'type' => null,
            'is_deducted' => false,
            'deduction_reason' => null,
            'applied_at' => now(),
            'verified_at' => null,
            'expires_at' => null,
            'gdpr_consent_at' => null,
            'newsletter_consent_at' => null,
            'photo_consent_at' => null,
        ];
    }
}
