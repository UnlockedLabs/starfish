<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {

        $this->call(ProviderPlatformSeeder::class);
        $this->call(ProviderContentSeeder::class);
        $this->call(StudentSeeder::class);
        $this->call(StudentEnrollmentSeeder::class);
        $this->call(ConsumerPlatformSeeder::class);
        $this->call(PlatformConnectionSeeder::class);
        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);
    }
}
