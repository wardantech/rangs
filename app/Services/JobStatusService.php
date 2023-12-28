<?php
// app/Services/JobStatusService.php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class JobStatusService
{
    public function totalStatus()
    {
        return $this->getStatusQuery()
            ->first();
    }

//Team Leader
    public function totalStatusByTeam($authId)
    {
        return $this->getStatusQuery()
            ->where('created_by', $authId)
            ->first();
    }

//Technician
    public function totalStatusByTechnician($userId)
    {
        return $this->getStatusQuery()
            ->where('user_id', $userId)
            ->first();
    }

    //Outlet
    public function totalStatusByOutlet($outletId)
    {
        return $this->getStatusQuery()
            ->where('outlet_id', $outletId)
            ->first();
    }

    private function getStatusQuery()
    {
        return DB::table('jobs')
            ->selectRaw("count(case when deleted_at IS NULL then 1 end) as totalJob")
            ->selectRaw("count(case when status = 5 and deleted_at IS NULL then 1 end) as pending")
            ->selectRaw("count(case when status = 6 and deleted_at IS NULL then 1 end) as jobPaused")
            ->selectRaw("count(case when status = 0 and deleted_at IS NULL then 1 end) as jobCreated")
            ->selectRaw("count(case when status = 4 and deleted_at IS NULL then 1 end) as jobCompleted")
            ->selectRaw("count(case when status = 3 and deleted_at IS NULL then 1 end) as jobStrated")
            ->selectRaw("count(case when status = 1 and deleted_at IS NULL then 1 end) as jobAccepted")
            ->selectRaw("count(case when status = 2 and deleted_at IS NULL then 1 end) as jobRejected");
    }
}
