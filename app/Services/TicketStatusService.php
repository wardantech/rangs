<?php
// app/Services/TicketStatusService.php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class TicketStatusService
{
    public function totalStatus()
    {
        return $this->getStatusQuery()
            // ->whereNull('deleted_at')
            ->first();
    }

    public function totalStatusByTeam($districtIds, $thanaIds, $categoryIds)
    {
        return $this->getStatusQuery()
            ->whereIn('district_id', $districtIds)
            ->whereIn('thana_id', $thanaIds)
            ->whereIn('product_category_id', $categoryIds)
            // ->whereNull('deleted_at')
            ->first();
    }

    public function totalStatusByOutlet($outletId)
    {
        return $this->getStatusQuery()
            ->where('outlet_id', $outletId)
            // ->whereNull('deleted_at')
            ->first();
    }

    private function getStatusQuery()
    {
        return DB::table('tickets')
        ->selectRaw("count(case when deleted_at IS NULL then 1 end) as total")
        ->selectRaw("count(case when status = 0 and deleted_at IS NULL then 1 end) as created")
        ->selectRaw("count(case when status = 6 and is_pending = 1 and is_paused = 0 and is_ended = 0 and deleted_at IS NULL then 1 end) as pending")
        ->selectRaw("count(case when status = 9 and is_reopened = 1 and deleted_at IS NULL  then 1 end) as ticketReOpened")
        ->selectRaw("count(case when status = 12 and is_ended = 1 and is_closed = 1 and is_delivered_by_call_center = 1 and deleted_at IS NULL then 1 end) as ticketClosed")
        ->selectRaw("count(case when status = 11 and is_ended = 1 and deleted_at IS NULL then 1 end) as jobCompleted")
        ->selectRaw("count(case when status = 5 and is_paused = 1 and deleted_at IS NULL then 1 end) as jobPaused")
        ->selectRaw("count(case when status = 4 and is_started = 1 and deleted_at IS NULL then 1 end) as jobStarted")
        ->selectRaw("count(case when status = 3 and is_accepted = 1 and deleted_at IS NULL then 1 end) as jobAccepted")
        ->selectRaw("count(case when status = 1 and is_assigned = 1 and deleted_at IS NULL then 1 end) as assigned")
        ->selectRaw("count(case when status = 2 and is_rejected = 1 and deleted_at IS NULL then 1 end) as rejected")
        ->selectRaw("count(case when status = 10 and is_delivered_by_call_center = 1 and deleted_at IS NULL then 1 end) as deliveredby_call_center")
        ->selectRaw("count(case when status = 8 and is_delivered_by_teamleader = 1 and deleted_at IS NULL then 1 end) as deliveredby_teamleader")
        ->selectRaw("count(case when status = 12 and is_delivered_by_call_center = 0 and is_ended = 1 and is_closed = 1 and deleted_at IS NULL then 1 end) as undelivered_close");
    }
}
