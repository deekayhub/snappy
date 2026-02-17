<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class OrganisationCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('organisation_categories')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $data = [
            ['name' => 'SPORTSWEAR', 'type' => 'supplier'],
            ['name' => 'SPORTS EQUIPMENT', 'type' => 'supplier'],
            ['name' => 'TROPHIES & AWARDS', 'type' => 'supplier'],
            ['name' => 'SIGNAGE', 'type' => 'supplier'],
            ['name' => 'GIFTS & PROMOTIONAL ITEMS', 'type' => 'supplier'],
            ['name' => 'SCHOOL UNIFORMS & SUPPLIES', 'type' => 'supplier'],
            ['name' => 'OTHER', 'type' => 'supplier'],

            ['name' => 'CLUB', 'type' => 'customer'],
            ['name' => 'TEAM', 'type' => 'customer'],
            ['name' => 'ORGANISATION', 'type' => 'customer'],
            ['name' => 'SCHOOL', 'type' => 'customer'],
            ['name' => 'OTHER', 'type' => 'customer'],
        ];

        $data = array_map(function ($item) {
            $item['name'] = Str::lower($item['name']);
            return $item;
        }, $data);

        DB::table('organisation_categories')->insert($data);
    }
}
