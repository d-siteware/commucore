<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\Locale;
use App\Enums\MemberType;
use App\Models\Membership\Member;
use App\Models\Membership\Role;
use App\Models\Team;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Jetstream\Features;

/**
 * @extends Factory<User>
 */
final class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    protected $model = User::class;

    public function definition()
    {
        return [
            'name' => $this->faker->name(),
            'first_name' => $this->faker->firstName(),
            'email' => $this->faker->unique()->safeEmail(),
            'is_admin' => false,
            'email_verified_at' => now(),
            'password' => self::$password ??= Hash::make('password'),
            'two_factor_secret' => null,
            'two_factor_recovery_codes' => null,
            'remember_token' => Str::random(10),
            'profile_photo_path' => null,
            'current_team_id' => null,
            'locale' => fake()->randomElement(Locale::toArray()),
        ];
    }

    public function admin()
    {
        return $this->state([
            'is_admin' => true,
        ]);
    }

    public function accountant(): Factory|UserFactory
    {
        return $this->withAccountingRole();
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {

        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    /**
     * Indicate that the user should have a personal team.
     */
    public function withPersonalTeam(?callable $callback = null): static
    {

        if (! Features::hasTeamFeatures()) {
            return $this->state([]);
        }

        return $this->has(
            Team::factory()
                ->state(fn (array $attributes, User $user) => [
                    'name' => $user->name.'\'s Team',
                    'user_id' => $user->id,
                    'personal_team' => true,
                ])
                ->when(is_callable($callback), $callback),
            'ownedTeams'
        );
    }

    /**
     * User mit Buchhaltungsrechten (Kassenwart)
     */
    public function withAccountingRole(): static
    {
        return $this->afterCreating(function (User $user) {
            // Erstelle oder hole Member für diesen User
            $member = Member::factory()->create([
                'user_id' => $user->id,
                'type' => MemberType::MD->value, // Vorstandsmitglied
            ]);

            // Hole oder erstelle Kassenwart-Rolle
            $role = Role::firstOrCreate(
                ['name' => ['de' => 'Kassenwart(in)', 'en' => 'Treasurer', 'hu' => 'Pénztáros']],
                [
                    'description' => 'Verantwortlich für die Finanzen',
                    'sort' => 2,
                    'can_manage_accounting' => true,
                ]
            );

            // Weise Rolle zu
            $member->roles()->attach($role->id, [
                'designated_at' => now(),
                'resigned_at' => null,
            ]);
        });
    }

    /**
     * User mit Vorstandsrolle (Board Member)
     */
    public function withBoardRole(): static
    {
        return $this->afterCreating(function (User $user) {
            // Erstelle oder hole Member für diesen User
            $member = Member::factory()->create([
                'user_id' => $user->id,
                'type' => MemberType::MD->value, // Vorstandsmitglied
            ]);

            // Hole oder erstelle Vorstandsrolle (z.B. Vorsitzende(r))
            $role = Role::firstOrCreate(
                ['name' => ['de' => 'Vorsitzende(r)', 'en' => 'Chairperson', 'hu' => 'Elnök']],
                [
                    'description' => 'Vorsitzende(r) des Vereins',
                    'sort' => 1,
                    'can_manage_accounting' => false,
                ]
            );

            // Weise Rolle zu
            $member->roles()->attach($role->id, [
                'designated_at' => now(),
                'resigned_at' => null,
            ]);
        });
    }

    /**
     * User mit spezifischer Rolle
     */
    public function withRole(string $roleName): static
    {
        return $this->afterCreating(function (User $user) use ($roleName) {
            $member = Member::factory()->create([
                'user_id' => $user->id,
                'type' => MemberType::MD->value,
            ]);

            // Suche Rolle nach deutschem Namen
            $role = Role::whereJsonContains('name->de', $roleName)->firstOrFail();

            $member->roles()->attach($role->id, [
                'designated_at' => now(),
                'resigned_at' => null,
            ]);
        });
    }

    /**
     * User mit Member aber ohne spezielle Rolle (Standard-Mitglied)
     */
    public function withMember(array $memberAttributes = []): static
    {
        return $this->afterCreating(function (User $user) use ($memberAttributes) {
            Member::factory()->create(array_merge([
                'user_id' => $user->id,
                'type' => MemberType::ST->value, // Standard Member
            ], $memberAttributes));
        });
    }

    /**
     * User mit ausgeschiedener Rolle (resigned)
     */
    public function withResignedRole(string $roleName): static
    {
        return $this->afterCreating(function (User $user) use ($roleName) {
            $member = Member::factory()->create([
                'user_id' => $user->id,
                'type' => MemberType::MD->value,
            ]);

            $role = Role::whereJsonContains('name->de', $roleName)->firstOrFail();

            $member->roles()->attach($role->id, [
                'designated_at' => now()->subYear(),
                'resigned_at' => now(), // Bereits ausgeschieden
            ]);
        });
    }
}
