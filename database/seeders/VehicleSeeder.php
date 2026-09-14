<?php

namespace Database\Seeders;

use App\Models\Vehicle;
use Illuminate\Database\Seeder;

class VehicleSeeder extends Seeder
{
    public function run(): void
    {
        $vehicles = [
            ['name' => 'Toyota Innova Crysta', 'type' => 'Car', 'fuel_type' => 'Diesel', 'transmission' => 'Manual', 'price_per_day' => 2500, 'seats' => 7, 'model_year' => 2018, 'status' => 'available', 'features' => ['Power Steering', 'Power Windows', 'ABS', 'Airbags', 'Central Locking', 'GPS Navigation']],
            ['name' => 'Maruti Swift Dzire', 'type' => 'Car', 'fuel_type' => 'Petrol', 'transmission' => 'Manual', 'price_per_day' => 1300, 'seats' => 5, 'model_year' => 2020, 'status' => 'available', 'features' => ['Power Steering', 'Power Windows', 'ABS']],
            ['name' => 'Honda City', 'type' => 'Car', 'fuel_type' => 'Petrol', 'transmission' => 'Automatic', 'price_per_day' => 2000, 'seats' => 5, 'model_year' => 2021, 'status' => 'available', 'features' => ['Sunroof', 'Cruise Control', 'ABS']],
            ['name' => 'Hyundai Creta', 'type' => 'Car', 'fuel_type' => 'Diesel', 'transmission' => 'Manual', 'price_per_day' => 2200, 'seats' => 5, 'model_year' => 2022, 'status' => 'available', 'features' => ['Sunroof', 'Rear Camera', 'ABS']],
            ['name' => 'Mahindra Thar', 'type' => 'Car', 'fuel_type' => 'Diesel', 'transmission' => 'Manual', 'price_per_day' => 3000, 'seats' => 4, 'model_year' => 2021, 'status' => 'maintenance', 'features' => ['4x4', 'Off-road Tyres']],
            ['name' => 'Royal Enfield Classic 350', 'type' => 'Bike', 'fuel_type' => 'Petrol', 'transmission' => 'Manual', 'price_per_day' => 800, 'seats' => 2, 'model_year' => 2022, 'status' => 'available', 'features' => ['Dual Channel ABS']],
        ];

        foreach ($vehicles as $v) {
            Vehicle::create($v);
        }
    }
}
