<?php
// app/Services/TicketService.php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class TicketService
{
    public static function buildQuery(){

        return DB::table('tickets')
        ->leftJoin('warranty_types','tickets.warranty_type_id','=','warranty_types.id')
        ->leftJoin('outlets','tickets.outlet_id','=','outlets.id')
        ->leftJoin('purchases','tickets.purchase_id','=','purchases.id')
        ->leftJoin('categories','tickets.product_category_id','=','categories.id')
        ->leftJoin('brand_models','purchases.brand_model_id', '=', 'brand_models.id')
        ->leftJoin('customers','purchases.customer_id', '=', 'customers.id')
        ->leftJoin('districts','tickets.district_id', '=','districts.id' )
        ->leftJoin('thanas','thanas.id', '=', 'tickets.thana_id')
        ->leftJoin('users', 'tickets.created_by', '=', 'users.id')
        ->leftJoin('ticket_recommendations', function($join) {
            $join->on('ticket_recommendations.ticket_id', '=', 'tickets.id')
                ->where('tickets.status', '=', 13)
                 ->where('ticket_recommendations.type', '=', 2)
                 ->whereRaw('ticket_recommendations.created_at = (SELECT MAX(created_at) FROM ticket_recommendations WHERE ticket_id = tickets.id AND type = 2)');
        })
        ->leftJoin('ticket_transfers', function($join) {
            $join->on('ticket_transfers.ticket_id', '=', 'tickets.id')
                ->where('tickets.status', '=', 14)
                ->whereNull('ticket_transfers.deleted_at')
                ->whereRaw('ticket_transfers.created_at = (SELECT MAX(created_at) FROM ticket_transfers WHERE ticket_id = tickets.id)');
        })
        ->select('ticket_recommendations.referrer_outlet_id','ticket_recommendations.recommended_outlet_id','ticket_transfers.referrer_outlet_id','ticket_transfers.recommended_outlet_id','users.name as created_by','brand_models.model_name as product_name','categories.name as product_category',
        'customers.name as customer_name', 'customers.mobile as customer_mobile', 'customers.address as customer_address',
        'purchases.product_serial as product_serial','purchases.invoice_number as invoice_number','tickets.id as ticket_id','tickets.created_at as created_at','outlets.name as outlet_name',
        'tickets.service_type_id as service_type_id','tickets.status as status',
        'tickets.is_closed_by_teamleader as is_closed_by_teamleader',
        'tickets.is_delivered_by_teamleader as is_delivered_by_teamleader','tickets.is_delivered_by_call_center as is_delivered_by_call_center',
        'tickets.delivery_date_by_team_leader as delivery_date_by_team_leader','tickets.delivery_date_by_call_center as delivery_date_by_call_center','purchases.outlet_id as outletid','warranty_types.warranty_type')
        ->whereNull('tickets.deleted_at');
    }

    public static function admin($query)
    {
        return $query;
    }

    public static function extendForTeamLeader($query, $districtIds, $thanaIds, $categoryIds, $outletId)
    {
        return $query->where(function ($query) use ($districtIds, $thanaIds, $categoryIds, $outletId) {
            $query->whereIn('tickets.district_id', $districtIds)
                    ->whereIn('tickets.thana_id', $thanaIds)
                    ->whereIn('tickets.product_category_id', $categoryIds)

                    ->orWhere(function ($query) use ($outletId) {
                        $query->where('ticket_recommendations.referrer_outlet_id', $outletId);
                    })
                    ->orWhere(function ($query) use ($outletId) {
                        $query->where('ticket_transfers.referrer_outlet_id', $outletId);
                    })
                    ->orWhere(function ($query) use ($outletId) {
                        $query->where('ticket_transfers.recommended_outlet_id', $outletId);
                    });
            });
    }    
    
    // public static function extendForoutlet($query, $outletId)
    // {
    //     return $query->where('tickets.outlet_id', $outletId);
    // }
    public static function extendForoutlet($query, $outletId)
    {
        return $query->where(function ($query) use ($outletId) {
            $query->where('tickets.outlet_id', $outletId)
                    ->orWhere(function ($query) use ($outletId) {
                        $query->where('ticket_recommendations.referrer_outlet_id', $outletId);
                })
                ->orWhere(function ($query) use ($outletId) {
                    $query->where('ticket_transfers.referrer_outlet_id', $outletId);
                })
                ->orWhere(function ($query) use ($outletId) {
                    $query->where('ticket_transfers.recommended_outlet_id', $outletId);
                });
        });
    }
    
    public static function extendForStatus($query, $statusId)
    {
        return $query->whereIn('tickets.status', $statusId);
    }

    public static function extendForDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('tickets.created_at', [$startDate, $endDate]);
    }
}
