<?php

namespace Database\Seeders\Demo;

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
            ],
            [
                'name' => [
                    'de' => 'Revisor(in)',
                    'en' => 'Auditor',
                    'hu' => 'Könyvvizsgáló',
                ],
                'description' => 'Kassenprüfer(in)',
                'sort' => 5,
                'can_manage_accounting' => true, // Revisor braucht Lesezugriff
            ],
        ];

        foreach ($roles as $roleData) {
            Role::firstOrCreate(
                ['name' => $roleData['name']],
                $roleData
            );
        }
    }
}
