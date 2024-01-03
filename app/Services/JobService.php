<?php
// app/Services/JobService.php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class JobService
{
    public static function buildQuery(){

        return DB::table('jobs')
                ->leftJoin('employees', 'jobs.employee_id', '=', 'employees.id')
                ->leftJoin('users', 'jobs.created_by', '=', 'users.id')
                ->leftJoin('tickets', 'jobs.ticket_id', '=', 'tickets.id')
                ->leftJoin('job_priorities', 'tickets.job_priority_id', '=', 'job_priorities.id')
                ->leftJoin('outlets','tickets.outlet_id','=','outlets.id')
                ->leftJoin('purchases','tickets.purchase_id','=','purchases.id')
                ->leftJoin('categories','tickets.product_category_id','=','categories.id')
                ->leftJoin('brand_models','purchases.brand_model_id', '=', 'brand_models.id')
                ->leftJoin('brands','purchases.brand_id', '=', 'brands.id')
                ->leftJoin('customers','purchases.customer_id', '=', 'customers.id')
                ->leftjoin('warranty_types','tickets.warranty_type_id', '=', 'warranty_types.id')
                ->select('jobs.id as job_id','jobs.job_number as job_number','jobs.date as assigning_date','jobs.created_at as job_created_at','employees.name as employee_name','employees.vendor_id as vendor_id','brand_models.model_name as model_name','brands.name as brand_name',
                'categories.name as product_category','users.name as created_by','customers.name as customer_name', 'customers.mobile as customer_mobile','purchases.product_serial as product_serial','purchases.invoice_number as invoice_number','purchases.purchase_date as purchase_date',
                'tickets.id as ticket_id','tickets.created_at as created_at','outlets.name as outlet_name','tickets.service_type_id as service_type_id','tickets.status as ticket_status',
                'tickets.is_reopened as is_reopened','tickets.is_accepted as is_accepted','tickets.is_pending as ticket_is_pending','tickets.is_paused as ticket_is_paused','tickets.is_ended as ticket_is_ended',
                'tickets.is_started as ticket_is_started','tickets.is_closed_by_teamleader as is_closed_by_teamleader','tickets.is_delivered_by_teamleader as is_delivered_by_teamleader',
                'tickets.is_delivered_by_call_center as is_delivered_by_call_center','tickets.is_closed as is_closed','tickets.is_assigned as is_assigned',
                'tickets.is_rejected as is_rejected','jobs.status as status','jobs.is_pending as is_pending','jobs.is_paused as is_paused','jobs.is_started as is_started','jobs.is_ended as is_ended','job_priorities.job_priority','tickets.outlet_id as outlet_id',
                'warranty_types.warranty_type as warranty_type','purchases.outlet_id as outletid')
                // ->whereIn('jobs.status',[0,1,3,5])
                ->where('jobs.deleted_at',null);
    }

    public static function extendForTeamLeader($query)
    {
        return $query->where('jobs.created_by', Auth::user()->id);
    }

    public static function extendForTechnician($query)
    {
        return $query->where('jobs.user_id', Auth::user()->id);
    }

    public static function extendForoutlet($query, $outletId)
    {
        return $query->where('jobs.outlet_id', $outletId);
    }

    public static function extendForStatus($query, $statusId)
    {
        return $query->whereIn('jobs.status', $statusId);
    }

    public static function extendForDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('jobs.created_at', [$startDate, $endDate]);
    }
}
