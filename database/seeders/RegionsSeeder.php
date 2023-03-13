<?php

namespace Database\Seeders;

use App\Models\Region;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RegionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $regions = [
            ['name' => 'Monastir', 'postal_code' => '5000'],
            ['name' => 'Sousse', 'postal_code' => '4000'],
            ['name' => 'Kairouan', 'postal_code' => '3100'],
            ['name' => 'Gafsa', 'postal_code' => '2100'],
            ['name' => 'Tozeur', 'postal_code' => '	2200'],

        ];
        foreach ($regions as $region) {
            $reg = new Region();
            $reg->name = $region['name'];
            $reg->postal_code = $region['postal_code'];
            $reg->save();
        }
    }
}
