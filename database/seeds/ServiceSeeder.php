<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('vehicle_types')->insert([
            [ 
                'code' => 'MICRO',
                'name' => 'Micro',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
                'order' => 2,
                'is_highway_enabled' => 1 
            ],
            [ 
                'code' => 'AUTO',
                'name' => 'Auto',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
                'order' => 1,
                'is_highway_enabled' => 0
            ],
            [ 
                'code' => 'PRIME',
                'name' => 'Prime',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
                'order' => 1,
                'is_highway_enabled' => 1
            ]
        ]);
    }
}
