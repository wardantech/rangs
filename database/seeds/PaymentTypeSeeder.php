<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Ticket\PaymentType;

class PaymentTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $paymentTypes = [
            ['name' => 'Full Payable'],
            ['name' => 'Parts Payable'],
            ['name' => 'Full Warranty'],
            ['name' => 'Special Component Warranty'],
            ['name' => 'Unsold Product'],
        ];

        foreach ($paymentTypes as $paymentType) {
            PaymentType::create($paymentType);
        }
    }
}
