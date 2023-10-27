<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class ProviderPlatformSeeder extends Seeder
{
    public function run()
    {
        \App\Models\ProviderPlatform::factory(10)->create(); // Create 10 provider platforms using the factory.
    }
}
