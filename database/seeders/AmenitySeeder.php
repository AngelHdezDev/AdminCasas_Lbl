<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AmenitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $amenities = [
            ['name' => 'Alberca', 'icon' => 'bi-water'],
            ['name' => 'Gimnasio', 'icon' => 'bi-fire'],
            ['name' => 'Seguridad 24/7', 'icon' => 'bi-shield-check'],
            ['name' => 'Cancha de Padel', 'icon' => 'bi-r-circle'],
            ['name' => 'Estacionamiento Visitas', 'icon' => 'bi-p-circle'],
            ['name' => 'Roof Garden', 'icon' => 'bi-building-up'],
            ['name' => 'Pet Park', 'icon' => 'bi-dog'],
            ['name' => 'Coworking', 'icon' => 'bi-laptop'],
        ];

        foreach ($amenities as $amenity) {
            \App\Models\Amenity::create($amenity);
        }
    }
}
