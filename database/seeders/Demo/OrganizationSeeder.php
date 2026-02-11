<?php

namespace Database\Seeders\Demo;

use App\Services\SettingsService;
use Illuminate\Database\Seeder;

class OrganizationSeeder extends Seeder
{
    public function run(): void
    {

       $service = app(SettingsService::class);
        // Basic organization info
        $service->set('organization.name', 'CommuCore');
        $service->set('organization.web', 'https://commu-core.org');
        $service->set('organization.email', 'hello@commu-core.org');

// Legal information
        $service->set('organization.register_id', 'NBR 123456 789');
        $service->set('organization.registered_date', '2026-01-20');
        $service->set('organization.court', 'Amtsgericht Berlin');
        $service->set('organization.tax_id', '1122334455');
        $service->set('organization.vat_id', 'EU1234567890');

// Multilingual slogan
        $service->set('organization.slogan', [
            'de' => 'Gemeinsam mehr erreichen',
            'en' => 'Achieving more together',
            'hu' => 'Együtt többet érünk el',
        ], 'json');

// Multilingual description
        $service->set('organization.description', [
            'de' => 'CommuCore ist eine moderne Plattform für Gemeinschaften, Vereine und Organisationen. Wir verbinden Menschen, vereinfachen Verwaltung und fördern Zusammenarbeit.',
            'en' => 'CommuCore is a modern platform for communities, associations and organizations. We connect people, simplify administration and promote collaboration.',
            'hu' => 'A CommuCore egy modern platform közösségek, egyesületek és szervezetek számára. Összekötjük az embereket, egyszerűsítjük az adminisztrációt és előmozdítjuk az együttműködést.',
        ], 'json');
    }
}
