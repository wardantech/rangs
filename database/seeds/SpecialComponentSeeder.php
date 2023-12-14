<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Job\SpecialComponent;
use Illuminate\Support\Facades\DB;

class SpecialComponentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        SpecialComponent::create([
            'name' => 'Compressor'
        ]);
        SpecialComponent::create([
            'name' => 'Motor'
        ]);
        SpecialComponent::create([
            'name' => 'O-Cell'
        ]);
        SpecialComponent::create([
            'name' => "Panel"
        ]);
        SpecialComponent::create([
            'name' => 'Magnetron'
        ]);
    }
}
