<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class StudentMappingSeeder extends Seeder
{
    public function run(): void
    {
        \App\Models\StudentMapping::factory(10)->create();
    }
}
