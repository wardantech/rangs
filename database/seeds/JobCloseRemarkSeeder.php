<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Job\JobCloseRemark;
use Illuminate\Support\Facades\DB;

class JobCloseRemarkSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        JobCloseRemark::create([
            'title' => 'Cancelled, not repairable'
        ]);
        JobCloseRemark::create([
            'title' => 'Cancelled, Home call refused'
        ]);
        JobCloseRemark::create([
            'title' => 'Cancelled, Transferred to other branch'
        ]);
        JobCloseRemark::create([
            'title' => "Cancelled, Customer Don't  Want Service"
        ]);
        JobCloseRemark::create([
            'title' => 'Cancelled, Double Job'
        ]);
        JobCloseRemark::create([
            'title' => 'Completed with parts'
        ]);
        JobCloseRemark::create([
            'title' => 'Completed without parts'
        ]);
        JobCloseRemark::create([
            'title' => 'Completed with Gas Refill'
        ]);
        JobCloseRemark::create([
            'title' => 'Completed updating software'
        ]);
        JobCloseRemark::create([
            'title' => 'Returned, Parts not available'
        ]);  
        JobCloseRemark::create([
            'title' => 'Returned, not repairable'
        ]);
        JobCloseRemark::create([
            'title' => 'Returned, estimate refusal'
        ]);
    }
}
