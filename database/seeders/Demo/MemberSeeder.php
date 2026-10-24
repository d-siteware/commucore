<?php

declare(strict_types=1);

namespace Database\Seeders\Demo;

use App\Enums\MemberType;
use App\Models\Membership\Member;
use Illuminate\Database\Seeder;

final class MemberSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (app()->environment() !== 'production') {

            $this->createMembers(5, MemberType::MD);
            $this->createMembers(7, MemberType::AD);
            $this->createMembers(25, MemberType::ST);
            $this->createMembers(8, MemberType::AP, static fn (): array => [
                'applied_at' => now()->subDays(random_int(0, 4)),
            ]);

            //            Event::factory(10)
            //                ->create();
        }
    }

    private function createMembers(int $count, MemberType $type, ?callable $extraAttributes = null): void
    {
        for ($i = 0; $i < $count; $i++) {
            $identity = DemoIdentity::make();
            $attributes = [
                'name' => $identity['last_name'],
                'first_name' => $identity['first_name'],
                'email' => $identity['email'],
                'type' => $type->value,
            ];

            Member::factory()->create(array_merge(
                $attributes,
                $extraAttributes !== null ? $extraAttributes() : [],
            ));
        }
    }
}
