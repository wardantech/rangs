<?php

namespace App\Http\Controllers\Report;

use Validator;
use DataTables;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class PartConsumptionReportController extends Controller
{
    public function partConsumptionGet()
    {
        try {
            $currentDate = Carbon::now('Asia/Dhaka');
            $formattedCurrentDate=$currentDate->toDateString();
            $formattedCurrentYear=$currentDate->year;
            $formattedCurrentMonth=$currentDate->month;
            $outlets=DB::table('outlets')->where('deleted_at', '=', null)->orderBy('name', 'ASC')->get();
            $jobs=DB::table('inventory_stocks')
                ->join('parts','parts.id','=','inventory_stocks.part_id')
                ->join('price_management','price_management.part_id','=','parts.id')
                ->join('jobs','jobs.id','=','inventory_stocks.job_id')
                ->join('tickets','tickets.id','=','jobs.ticket_id')
                ->join('job_priorities','job_priorities.id','=','tickets.job_priority_id')
                ->join('purchases','tickets.purchase_id','=','purchases.id')
                ->join('brand_models','purchases.brand_model_id','=','brand_models.id')
                ->join('outlets','outlets.id','=','tickets.outlet_id')
                ->select('brand_models.model_name as model_name','purchases.product_serial as product_serial','job_priorities.job_priority as type_name','parts.name as part_name','parts.code as part_code','price_management.cost_price_usd as cpu','price_management.cost_price_bdt as cpb','price_management.selling_price_bdt as spb',
                'jobs.created_at as job_assign_date','jobs.job_end_time as repairDate','jobs.job_number as jobcode','jobs.id as jobid','outlets.name as branch','inventory_stocks.stock_out as qnty','jobs.job_number as jobNumber',
                'tickets.id as ticket_id','tickets.created_at as ticket_date','tickets.delivery_date_by_team_leader as deliveryDate')
                ->where('inventory_stocks.stock_out', '>', 0)
                ->where('inventory_stocks.is_consumed', 1)
                ->where('inventory_stocks.job_id', '!=', null)
                ->whereYear('tickets.delivery_date_by_team_leader','=', $formattedCurrentYear)
                ->whereMonth('tickets.delivery_date_by_team_leader','=', $formattedCurrentMonth)
                ->where('tickets.deleted_at', null)
                ->whereNull('inventory_stocks.deleted_at')
                ->groupBy('inventory_stocks.id')
                ->get();
                if(request()->ajax()){

                    return DataTables::of($jobs)
                    ->addColumn('ticket_sl', function ($jobs) {
                        $ticket_sl=null;
                        if($jobs->ticket_id){
                            $ticket_sl='TSL-'.$jobs->ticket_id; 
                        }
                        return $ticket_sl;
                    })
                    ->addColumn('job_sl', function ($jobs) {
                        $job_sl=null;
                        if($jobs->jobid){
                            $job_sl='JSL-'.$jobs->jobid; 
                        }
                        return $job_sl;
                    })
                    ->addColumn('ticket_date', function ($jobs) {
                        $ticket_date=null;
                        if($jobs->ticket_date){
                            $ticket_date=Carbon::parse($jobs->ticket_date)->format('m/d/Y'); 
                        }
                        return $ticket_date;
                    })

                    ->addColumn('job_assign_date', function ($jobs) {
                        $job_assign_date=null;
                        if($jobs->job_assign_date){
                            $job_assign_date=Carbon::parse($jobs->job_assign_date)->format('m/d/Y'); 
                        }
                        return $job_assign_date;
                    })
                    ->addColumn('repair_date', function ($jobs) {
                        $repair_date=null;
                        if (isset($jobs->repairDate)) {
                            $repair_date=Carbon::parse($jobs->repairDate)->format('m/d/Y');
                        }
                        return $repair_date;
                    })
                    ->addColumn('delivery_date', function ($jobs) {
                        $delivery_date=null;
                        if (isset($jobs->deliveryDate)) {
                            $delivery_date=Carbon::parse($jobs->deliveryDate)->format('m/d/Y');
                        }
                        return $delivery_date;
                    })
                            ->addIndexColumn()
                            ->make(true);
                }
                return view ('reports.consumption.consumption-report', compact('outlets'));
        }catch(\Exception $e){
            $bug = $e->getMessage();  
            return redirect()->back()->with('error', $bug);
        }
    }
    
    public function partConsumptionPost(Request $request)
    {
        try {

            $startDate=Carbon::parse($request->start_date)->format('Y-m-d');
            $endDate=Carbon::parse($request->end_date)->format('Y-m-d');
            $outlets=DB::table('outlets')->where('deleted_at', '=', null)->orderBy('name', 'ASC')->get();
            $soutlet= null;
            $query=DB::table('inventory_stocks')
            ->join('parts','parts.id','=','inventory_stocks.part_id')
            ->join('price_management','price_management.part_id','=','parts.id')
            ->join('jobs','jobs.id','=','inventory_stocks.job_id')
            ->join('tickets','tickets.id','=','jobs.ticket_id')
            ->join('job_priorities','job_priorities.id','=','tickets.job_priority_id')
            ->join('purchases','tickets.purchase_id','=','purchases.id')
            ->join('brand_models','purchases.brand_model_id','=','brand_models.id')
            ->join('outlets','outlets.id','=','tickets.outlet_id')
            ->select('brand_models.model_name as model_name','purchases.product_serial as product_serial','job_priorities.job_priority as type_name','parts.name as part_name','parts.code as part_code','price_management.cost_price_usd as cpu','price_management.cost_price_bdt as cpb','price_management.selling_price_bdt as spb',
            'jobs.created_at as job_assign_date','jobs.job_end_time as repairDate','jobs.job_number as jobcode','jobs.id as jobid','outlets.name as branch','inventory_stocks.stock_out as qnty','jobs.job_number as jobNumber',
            'tickets.id as ticket_id','tickets.created_at as ticket_date','tickets.delivery_date_by_team_leader as deliveryDate')
            ->where('inventory_stocks.stock_out', '>', 0)
            ->where('inventory_stocks.is_consumed', 1)
            ->where('inventory_stocks.job_id', '!=', null)
            ->whereNull('inventory_stocks.deleted_at');

            if ($request->outlet) 
            {
                $jobs=$query->where('tickets.outlet_id','=',$request->outlet)->groupBy('inventory_stocks.id')->orderBy('jobs.id');;
            }

            if($request->start_date && $request->end_date)
            {
                $jobs=$query->whereBetween('tickets.delivery_date_by_team_leader',[$startDate, $endDate])->groupBy('inventory_stocks.id')->orderBy('jobs.id');
            }

                if(request()->ajax()){

                    return DataTables::of($jobs)
                    ->addColumn('ticket_sl', function ($jobs) {
                        $ticket_sl=null;
                        if($jobs->ticket_id){
                            $ticket_sl='TSL-'.$jobs->ticket_id; 
                        }
                        return $ticket_sl;
                    })

                    ->addColumn('job_sl', function ($jobs) {
                        $job_sl=null;
                        if($jobs->jobid){
                            $job_sl='JSL-'.$jobs->jobid; 
                        }
                        return $job_sl;
                    })

                    ->addColumn('ticket_date', function ($jobs) {
                        $ticket_date=null;
                        if($jobs->ticket_date){
                            $ticket_date=Carbon::parse($jobs->ticket_date)->format('m/d/Y'); 
                        }
                        return $ticket_date;
                    })

                    ->addColumn('job_assign_date', function ($jobs) {
                        $job_assign_date=null;
                        if($jobs->job_assign_date){
                            $job_assign_date=Carbon::parse($jobs->job_assign_date)->format('m/d/Y'); 
                        }
                        return $job_assign_date;
                    })
                    ->addColumn('repair_date', function ($jobs) {
                        $repair_date=null;
                        if (isset($jobs->repairDate)) {
                            $repair_date=Carbon::parse($jobs->repairDate)->format('m/d/Y');
                        }
                        return $repair_date;
                    })
                    ->addColumn('delivery_date', function ($jobs) {
                        $delivery_date=null;
                        if (isset($jobs->deliveryDate)) {
                            $delivery_date=Carbon::parse($jobs->deliveryDate)->format('m/d/Y');
                        }
                        return $delivery_date;
                    })
                            ->addIndexColumn()
                            ->make(true);
                }
                return view('reports.consumption.consumption-report-filter', compact('outlets','soutlet'));
        }catch(\Exception $e){
            $bug = $e->getMessage();
            return redirect()->back()->with('error', $bug);
        }
    }
}
