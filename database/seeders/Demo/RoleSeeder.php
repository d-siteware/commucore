<?php

namespace Database\Seeders\Demo;

use App\Models\Membership\Member;
use App\Models\Membership\MemberRole;
use App\Models\Membership\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            [
                'name' => [
                    'de' => 'Vorsitzende(r)',
                    'en' => 'Chairperson',
                    'hu' => 'Elnök',
                ],
                'description' => 'Vorsitzende(r) des Vereins',
                'sort' => 1,
                'can_manage_accounting' => false,
                'can_represent_organization' => true,
                'can_audit_accounting' => false,
            ],
            [
                'name' => [
                    'de' => 'Kassenwart(in)',
                    'en' => 'Treasurer',
                    'hu' => 'Pénztáros',
                ],
                'description' => 'Verantwortlich für die Finanzen',
                'sort' => 2,
                'can_manage_accounting' => true, // Kassenwart hat Buchhaltungsrechte
                'can_represent_organization' => false,
                'can_audit_accounting' => false,
            ],
            [
                'name' => [
                    'de' => 'Schriftführer(in)',
                    'en' => 'Secretary',
                    'hu' => 'Titkár',
                ],
                'description' => 'Schriftführer(in) des Vereins',
                'sort' => 3,
                'can_manage_accounting' => false,
                'can_represent_organization' => false,
                'can_audit_accounting' => false,
            ],
            [
                'name' => [
                    'de' => 'Beisitzer(in)',
                    'en' => 'Board Member',
                    'hu' => 'Választmányi tag',
                ],
                'description' => 'Beisitzer(in) im Vorstand',
                'sort' => 4,
                'can_manage_accounting' => false,
                'can_represent_organization' => false,
                'can_audit_accounting' => false,
            ],
            [
                'name' => [
                    'de' => 'Revisor(in)',
                    'en' => 'Auditor',
                    'hu' => 'Könyvvizsgáló',
                ],
                'description' => 'Kassenprüfer(in)',
                'sort' => 5,
                'can_manage_accounting' => false,
                'can_represent_organization' => false,
                'can_audit_accounting' => true,
            ],
        ];

        foreach ($roles as $roleData) {
            Role::updateOrCreate(
                ['name->de' => $roleData['name']['de']],
                $roleData,
            );
        }

        // Set up some demo roles for members
        if (Member::count() > 5) {

            $members = Member::query()->orderBy('id')->take(5)->get();

            $Chair = $members->get(0);
            $Treasurer = $members->get(1);
            $Secretary = $members->get(2);
            $Auditor = $members->get(3);
            $BoardMember = $members->get(4);

            MemberRole::createOrFirst([
                'member_id' => $Chair->id,
                'role_id' => Role::where('name->de', 'Vorsitzende(r)')->first()->id,
                'designated_at' => now(),
                'resigned_at' => null,
                'about_me' => null,
                'profile_image' => null,
            ]);

            MemberRole::createOrFirst([
                'member_id' => $Treasurer->id,
                'role_id' => Role::where('name->de', 'Kassenwart(in)')->first()->id,
                'designated_at' => now(),
                'resigned_at' => null,
                'about_me' => null,
                'profile_image' => null,
            ]);
            MemberRole::createOrFirst([
                'member_id' => $Secretary->id,
                'role_id' => Role::where('name->de', 'Schriftführer(in)')->first()->id,
                'designated_at' => now(),
                'resigned_at' => null,
                'about_me' => null,
                'profile_image' => null,
            ]);
            MemberRole::createOrFirst([
                'member_id' => $BoardMember->id,
                'role_id' => Role::where('name->de', 'Beisitzer(in)')->first()->id,
                'designated_at' => now(),
                'resigned_at' => null,
                'about_me' => null,
                'profile_image' => null,
            ]);
            MemberRole::createOrFirst([
                'member_id' => $Auditor->id,
                'role_id' => Role::where('name->de', 'Revisor(in)')->first()->id,
                'designated_at' => now(),
                'resigned_at' => null,
                'about_me' => null,
                'profile_image' => null,
            ]);

        }

    }
}
