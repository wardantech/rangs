<?php
// app/Services/TicketStatusService.php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class TicketStatusService
{
    public function totalStatus()
    {
        return $this->getStatusQuery()
            ->first();
    }

    public function totalStatusByTeam($districtIds, $thanaIds, $categoryIds, $outletId)
    {
        return $this->getStatusQuery()
            ->where(function ($query) use ($districtIds, $thanaIds, $categoryIds, $outletId) {
                $query->whereIn('district_id', $districtIds)
                    ->whereIn('thana_id', $thanaIds)
                    ->whereIn('product_category_id', $categoryIds)

                    ->orWhere(function ($query) use ($outletId) {
                        $query->where('ticket_recommendations.referrer_outlet_id', $outletId);
                    });
            })
            ->first();
    }

    public function totalStatusByOutlet($outletId)
    {
        return $this->getStatusQuery()
            ->where(function ($query) use ($outletId) {
                $query->where('tickets.outlet_id', $outletId)
                    ->orWhere(function ($query) use ($outletId) {
                        $query->where('ticket_recommendations.referrer_outlet_id', $outletId);
                    });
            })
            ->first();
    }
    

    private function getStatusQuery()
    {
        return DB::table('tickets')
        ->leftJoin('ticket_recommendations', function($join) {
            $join->on('ticket_recommendations.ticket_id', '=', 'tickets.id')
                ->where('tickets.status', '=', 13)
                ->where('ticket_recommendations.type', '=', 1)
                ->whereNull('ticket_recommendations.deleted_at')
                ->whereRaw('ticket_recommendations.created_at = (SELECT MAX(created_at) FROM ticket_recommendations WHERE ticket_id = tickets.id AND type = 1)');
        })
        ->selectRaw("count(case when tickets.deleted_at IS NULL then 1 end) as total")
        ->selectRaw("count(case when tickets.status = 0 and tickets.deleted_at IS NULL then 1 end) as created")
        ->selectRaw("count(case when tickets.status = 6 and is_pending = 1 and is_paused = 0 and is_ended = 0 and tickets.deleted_at IS NULL then 1 end) as pending")
        ->selectRaw("count(case when tickets.status = 9 and is_reopened = 1 and tickets.deleted_at IS NULL  then 1 end) as ticketReOpened")
        ->selectRaw("count(case when tickets.status = 12 and is_ended = 1 and is_closed = 1 and is_delivered_by_call_center = 1 and tickets.deleted_at IS NULL then 1 end) as ticketClosed")
        ->selectRaw("count(case when tickets.status = 11 and is_ended = 1 and tickets.deleted_at IS NULL then 1 end) as jobCompleted")
        ->selectRaw("count(case when tickets.status = 5 and is_paused = 1 and tickets.deleted_at IS NULL then 1 end) as jobPaused")
        ->selectRaw("count(case when tickets.status = 4 and is_started = 1 and tickets.deleted_at IS NULL then 1 end) as jobStarted")
        ->selectRaw("count(case when tickets.status = 3 and is_accepted = 1 and tickets.deleted_at IS NULL then 1 end) as jobAccepted")
        ->selectRaw("count(case when tickets.status = 1 and is_assigned = 1 and tickets.deleted_at IS NULL then 1 end) as assigned")
        ->selectRaw("count(case when tickets.status = 2 and is_rejected = 1 and tickets.deleted_at IS NULL then 1 end) as rejected")
        ->selectRaw("count(case when tickets.status = 10 and is_delivered_by_call_center = 1 and tickets.deleted_at IS NULL then 1 end) as deliveredby_call_center")
        ->selectRaw("count(case when tickets.status = 8 and is_delivered_by_teamleader = 1 and tickets.deleted_at IS NULL then 1 end) as deliveredby_teamleader")
        ->selectRaw("count(case when tickets.status = 12 and is_delivered_by_call_center = 0 and is_ended = 1 and is_closed = 1 and tickets.deleted_at IS NULL then 1 end) as undelivered_close")
        ->selectRaw("count(case when tickets.status = 13 and tickets.deleted_at IS NULL and ticket_recommendations.type = 1 then 1 end) as tl_recommended");
    }

    public function getOutgoingTransferCount($outletId)
    {
        return DB::table('ticket_transfers')
            ->join('tickets', 'ticket_transfers.ticket_id', '=', 'tickets.id')
            ->where('tickets.status', '=', 14)
            ->where('ticket_transfers.referrer_outlet_id', $outletId)
            ->whereNull('ticket_transfers.deleted_at')
            ->whereNull('tickets.deleted_at')
            ->distinct('tickets.id')
            ->count('tickets.id');
    }
    
    public function getIncomingTransferCount($outletId)
    {
        return DB::table('ticket_transfers')
            ->join('tickets', 'ticket_transfers.ticket_id', '=', 'tickets.id')
            ->where('tickets.status', '=', 14)
            ->where('ticket_transfers.recommended_outlet_id', $outletId)
            ->whereNull('ticket_transfers.deleted_at')
            ->whereNull('tickets.deleted_at')
            ->distinct('tickets.id')
            ->count('tickets.id');
    }
    
    public function getOutgoingTransferCountForSuperAdmin()
    {
        return DB::table('ticket_transfers')
            ->join('tickets', 'ticket_transfers.ticket_id', '=', 'tickets.id')
            ->where('tickets.status', '=', 14)
            ->whereNotNull('ticket_transfers.referrer_outlet_id')
            ->whereNull('ticket_transfers.deleted_at')
            ->whereNull('tickets.deleted_at')
            ->distinct('tickets.id')
            ->count('tickets.id');
    }
    
    public function getIncomingTransferCountForSuperAdmin()
    {
        return DB::table('ticket_transfers')
            ->join('tickets', 'ticket_transfers.ticket_id', '=', 'tickets.id')
            ->where('tickets.status', '=', 14)
            ->whereNotNull('ticket_transfers.recommended_outlet_id')
            ->whereNull('ticket_transfers.deleted_at')
            ->whereNull('tickets.deleted_at')
            ->distinct('tickets.id')
            ->count('tickets.id');
    }
    

}
