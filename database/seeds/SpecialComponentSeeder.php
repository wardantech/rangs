<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Job\SpecialComponent;
use App\Models\Job\JobPendingRemark;
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
        //1
        $jobPendingRemark=JobPendingRemark::updateOrCreate([
            'title' => 'Pending for Special Component'
        ]);
        SpecialComponent::updateOrCreate([
            'job_pending_remark_id'=>$jobPendingRemark->id,
            'name' => 'Compressor'
        ]);

        SpecialComponent::updateOrCreate([
            'job_pending_remark_id'=>$jobPendingRemark->id,
            'name' => 'Motor'
        ]);

        SpecialComponent::updateOrCreate([
            'job_pending_remark_id'=>$jobPendingRemark->id,
            'name' => 'O-Cell'
        ]);

        SpecialComponent::updateOrCreate([
            'job_pending_remark_id'=>$jobPendingRemark->id,
            'name' => "Panel"
        ]);

        SpecialComponent::updateOrCreate([
            'job_pending_remark_id'=>$jobPendingRemark->id,
            'name' => 'Magnetron'
        ]);

        //2
        $jobPendingRemark=JobPendingRemark::updateOrCreate([
            'title' => 'Pending for MCB'
        ]);
        SpecialComponent::updateOrCreate([
            'job_pending_remark_id'=>$jobPendingRemark->id,
            'name' => 'Inverter Board'
        ]);

        SpecialComponent::updateOrCreate([
            'job_pending_remark_id'=>$jobPendingRemark->id,
            'name' => 'Display PCB'
        ]);

        SpecialComponent::updateOrCreate([
            'job_pending_remark_id'=>$jobPendingRemark->id,
            'name' => 'Power Board'
        ]);

        SpecialComponent::updateOrCreate([
            'job_pending_remark_id'=>$jobPendingRemark->id,
            'name' => 'LD Board'
        ]);

        SpecialComponent::updateOrCreate([
            'job_pending_remark_id'=>$jobPendingRemark->id,
            'name' => 'IR Board'
        ]);

        SpecialComponent::updateOrCreate([
            'job_pending_remark_id'=>$jobPendingRemark->id,
            'name' => 'WiFi Board'
        ]);

        SpecialComponent::updateOrCreate([
            'job_pending_remark_id'=>$jobPendingRemark->id,
            'name' => 'T-Con Board'
        ]);

        //3
        $jobPendingRemark=JobPendingRemark::updateOrCreate([
            'title' => 'Pending for Others Parts'
        ]);
        SpecialComponent::updateOrCreate([
            'job_pending_remark_id'=>$jobPendingRemark->id,
            'name' => 'Relay'
        ]);

        SpecialComponent::updateOrCreate([
            'job_pending_remark_id'=>$jobPendingRemark->id,
            'name' => 'Over Load'
        ]);

        SpecialComponent::updateOrCreate([
            'job_pending_remark_id'=>$jobPendingRemark->id,
            'name' => 'Capacitor'
        ]);

        SpecialComponent::updateOrCreate([
            'job_pending_remark_id'=>$jobPendingRemark->id,
            'name' => 'Thermostat'
        ]);

        SpecialComponent::updateOrCreate([
            'job_pending_remark_id'=>$jobPendingRemark->id,
            'name' => 'Light'
        ]);

        SpecialComponent::updateOrCreate([
            'job_pending_remark_id'=>$jobPendingRemark->id,
            'name' => 'Gas'
        ]);

        SpecialComponent::updateOrCreate([
            'job_pending_remark_id'=>$jobPendingRemark->id,
            'name' => 'Condenser'
        ]);

        SpecialComponent::updateOrCreate([
            'job_pending_remark_id'=>$jobPendingRemark->id,
            'name' => 'Evaporator'
        ]);

        SpecialComponent::updateOrCreate([
            'job_pending_remark_id'=>$jobPendingRemark->id,
            'name' => 'Back-Light'
        ]);

        SpecialComponent::updateOrCreate([
            'job_pending_remark_id'=>$jobPendingRemark->id,
            'name' => 'Door'
        ]);

        SpecialComponent::updateOrCreate([
            'job_pending_remark_id'=>$jobPendingRemark->id,
            'name' => 'Transformer'
        ]);

        //4
        $jobPendingRemark=JobPendingRemark::updateOrCreate([
            'title' => 'Pending for Estimate Approval'
        ]);

        SpecialComponent::updateOrCreate([
            'job_pending_remark_id'=>$jobPendingRemark->id,
            'name' => 'Parts Name & Amount'
        ]);

        //5
        $jobPendingRemark=JobPendingRemark::updateOrCreate([
            'title' => 'Pending for Customer Feedback'
        ]);
        SpecialComponent::updateOrCreate([
            'job_pending_remark_id'=>$jobPendingRemark->id,
            'name' => 'Bill'
        ]);

        SpecialComponent::updateOrCreate([
            'job_pending_remark_id'=>$jobPendingRemark->id,
            'name' => 'Documents'
        ]);

        SpecialComponent::updateOrCreate([
            'job_pending_remark_id'=>$jobPendingRemark->id,
            'name' => 'Unable to Reach'
        ]);

        SpecialComponent::updateOrCreate([
            'job_pending_remark_id'=>$jobPendingRemark->id,
            'name' => 'Decision'
        ]);

        SpecialComponent::updateOrCreate([
            'job_pending_remark_id'=>$jobPendingRemark->id,
            'name' => 'Schedule'
        ]);

        //6
        $jobPendingRemark=JobPendingRemark::updateOrCreate([
            'title' => 'Pending for Transport'
        ]);
        SpecialComponent::updateOrCreate([
            'job_pending_remark_id'=>$jobPendingRemark->id,
            'name' => 'Collection'
        ]);

        SpecialComponent::updateOrCreate([
            'job_pending_remark_id'=>$jobPendingRemark->id,
            'name' => 'Delivery'
        ]);

        //7
        $jobPendingRemark=JobPendingRemark::updateOrCreate([
            'title' => 'Pending for Others Issue'
        ]);
        SpecialComponent::updateOrCreate([
            'job_pending_remark_id'=>$jobPendingRemark->id,
            'name' => 'Write as per requirement'
        ]);

        //8
        $jobPendingRemark=JobPendingRemark::updateOrCreate([
            'title' => 'Pending for Repair Parts'
        ]);
        SpecialComponent::updateOrCreate([
            'job_pending_remark_id'=>$jobPendingRemark->id,
            'name' => 'Board, O-Cell, Key Pad, Panel'
        ]);

        SpecialComponent::updateOrCreate([
            'job_pending_remark_id'=>$jobPendingRemark->id,
            'name' => 'O-Cell'
        ]);

        SpecialComponent::updateOrCreate([
            'job_pending_remark_id'=>$jobPendingRemark->id,
            'name' => 'Key Pad'
        ]);

        SpecialComponent::updateOrCreate([
            'job_pending_remark_id'=>$jobPendingRemark->id,
            'name' => 'Panel'
        ]);

        //9
        $jobPendingRemark=JobPendingRemark::updateOrCreate([
            'title' => 'Pending for Management Decision'
        ]);
        SpecialComponent::updateOrCreate([
            'job_pending_remark_id'=>$jobPendingRemark->id,
            'name' => 'Replacement'
        ]);

        SpecialComponent::updateOrCreate([
            'job_pending_remark_id'=>$jobPendingRemark->id,
            'name' => 'Refund'
        ]);

        SpecialComponent::updateOrCreate([
            'job_pending_remark_id'=>$jobPendingRemark->id,
            'name' => 'Part Order Approval'
        ]);

        SpecialComponent::updateOrCreate([
            'job_pending_remark_id'=>$jobPendingRemark->id,
            'name' => 'Feedback from Sales/Marketing'
        ]);

        SpecialComponent::updateOrCreate([
            'job_pending_remark_id'=>$jobPendingRemark->id,
            'name' => 'Legal Issues'
        ]);

        //10
        $jobPendingRemark=JobPendingRemark::updateOrCreate([
            'title' => 'Work In Progress '
        ]);
        SpecialComponent::updateOrCreate([
            'job_pending_remark_id'=>$jobPendingRemark->id,
            'name' => 'Under Test'
        ]);

        SpecialComponent::updateOrCreate([
            'job_pending_remark_id'=>$jobPendingRemark->id,
            'name' => 'Under Installation'
        ]);

        SpecialComponent::updateOrCreate([
            'job_pending_remark_id'=>$jobPendingRemark->id,
            'name' => 'Product Repair'
        ]);
    }
}
