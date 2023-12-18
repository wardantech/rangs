<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Job\JobPendingRemark;

class JobPendingRemarkSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        JobPendingRemark::create([
            'title' => 'Will attend on'
        ]);

        JobPendingRemark::create([
            'title' => 'Product not received yet'
        ]);

        JobPendingRemark::create([
            'title' => 'Waiting for Board'
        ]);

        JobPendingRemark::create([
            'title' => 'Waiting for Parts'
        ]);

        JobPendingRemark::create([
            'title' => 'Waiting for Compressor'
        ]);

        JobPendingRemark::create([
            'title' => 'Waiting for Motor'
        ]);

        JobPendingRemark::create([
            'title' => 'Waiting for Management Feedback'
        ]);

        JobPendingRemark::create([
            'title' => 'Job transferred'
        ]);

        JobPendingRemark::create([
            'title' => 'Under estimate'
        ]);

        JobPendingRemark::create([
            'title' => 'Critical issue'
        ]);

        JobPendingRemark::create([
            'title' => 'Waiting for Customer Feedback'
        ]);

        JobPendingRemark::create([
            'title' => 'Waiting for Vendor Feedback'
        ]);
        
        JobPendingRemark::create([
            'title' => 'No Fault Found'
        ]);

        JobPendingRemark::create([
            'title' => 'Under Test'
        ]);
        
        JobPendingRemark::create([
            'title' => 'Others'
        ]);
        
        JobPendingRemark::create([
            'title' => 'Pending for Special Component'
        ]);

        JobPendingRemark::create([
            'title' => 'Pending for MCB'
        ]);

        JobPendingRemark::create([
            'title' => 'Pending for Others Parts'
        ]);

        JobPendingRemark::create([
            'title' => 'Pending for Estimate Approval'
        ]);

        JobPendingRemark::create([
            'title' => 'Pending for Customer Feedback'
        ]);

        JobPendingRemark::create([
            'title' => 'Pending for Transport'
        ]);

        JobPendingRemark::create([
            'title' => 'Pending for Others Issue'
        ]);

        JobPendingRemark::create([
            'title' => 'Pending for Repair Parts'
        ]);

        JobPendingRemark::create([
            'title' => 'Pending for Management Decision'
        ]);

        JobPendingRemark::create([
            'title' => 'Work In Progress'
        ]);
    }
}
