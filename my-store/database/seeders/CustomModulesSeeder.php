<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class CustomModulesSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            \Agency\SiteVisit\Database\Seeders\SiteVisitSeeder::class,
            \Agency\ProductSample\Database\Seeders\ProductSampleSeeder::class,
        ]);
    }
}
