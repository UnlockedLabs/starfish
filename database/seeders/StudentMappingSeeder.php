<?php

namespace database\seeders;

use Illuminate\Database\Seeder;

class StudentMappingSeeder extends Seeder
{
    public function run(): void
    {
        \App\Models\StudentMapping::factory()->count(10)->create();
    }
}
