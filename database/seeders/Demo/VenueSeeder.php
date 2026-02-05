<?php

namespace Database\Seeders\Demo;

use App\Models\Venue;
use Illuminate\Database\Seeder;

class VenueSeeder extends Seeder
{
    public function run(): void
    {
       for ($i = 0; $i < rand(1,4); $i++) {
           $venueData = DemoVenueText::randomVenue();

           Venue::create([
               'name' => $venueData['name']['de'], // interner Name
               'address' => $venueData['address'],
               'city' => $venueData['city'],
               'country' => $venueData['country'],
               'postal_code' => $venueData['zipcode'],

           ]);
       }
    }
}
