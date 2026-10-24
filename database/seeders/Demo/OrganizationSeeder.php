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
        $service->set('organization.statute', [
            'de' => 'Demo-Satzung des CommuCore Mustervereins. Dieser Beispieltext ist keine rechtsgültige Vereinssatzung.',
            'en' => 'Demo statutes for the CommuCore sample association. This sample text is not a legally valid statute.',
            'hu' => 'A CommuCore mint egyesületének demó alapszabálya. Ez a mintaszöveg nem jogilag érvényes alapszabály.',
        ], 'json');

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

        // Multilingual about_us
        $service->set('organization.about_us', [
            'de' => 'CommuCore bringt Menschen in Berlin zusammen, die ihr Miteinander aktiv gestalten möchten. Bei offenen Treffen, Workshops und gemeinsamen Projekten entstehen neue Ideen und praktische Unterstützung im Alltag. Mitglieder und Interessierte sind herzlich eingeladen, sich einzubringen und das Vereinsleben mitzugestalten.',
            'en' => 'CommuCore brings together people in Berlin who want to actively shape their community. Open meetups, workshops, and shared projects create space for new ideas and practical support. Members and anyone interested are welcome to take part and help shape the life of the association.',
            'hu' => 'A CommuCore olyan berlini embereket hoz össze, akik aktívan szeretnék formálni a közösségüket. Nyitott találkozóinkon, műhelyeinken és közös projektjeinkben új ötletek születnek, és gyakorlati segítséget nyújtunk egymásnak. Tagjainkat és az érdeklődőket is szívesen látjuk, hogy együtt alakítsuk az egyesület életét.',
        ], 'json');
    }
}
