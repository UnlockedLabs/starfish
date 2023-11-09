<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class StudentEnrollmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\StudentEnrollment::factory()->count(10)->create();
    }
}
