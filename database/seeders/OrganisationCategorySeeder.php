<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class OrganisationCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
         DB::table('organisation_categories')->insert([

            ['name' => 'SPORTSWEAR', 'type' => 'supplier'],
            ['name' => 'SPORTS EQUIPMENT', 'type' => 'supplier'],
            ['name' => 'TROPHIES & AWARDS', 'type' => 'supplier'],
            ['name' => 'SIGNAGE', 'type' => 'supplier'],
            ['name' => 'GIFTS & PROMOTIONAL ITEMS', 'type' => 'supplier'],
            ['name' => 'SCHOOL UNIFORMS & SUPPLIERS', 'type' => 'supplier'],

            ['name' => 'CLUB', 'type' => 'customer'],
            ['name' => 'TEAM', 'type' => 'customer'],
            ['name' => 'ORGANISATION', 'type' => 'customer'],
            ['name' => 'SCHOOL', 'type' => 'customer'],
            ['name' => 'OTHER', 'type' => 'customer'],
        ]);
    }
}
