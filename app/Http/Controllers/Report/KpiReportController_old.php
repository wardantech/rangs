<?php

namespace App\Http\Controllers\Report;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\Ticket\Ticket;
use App\Models\Inventory\Category;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\Inventory\InventoryStock;
use Yajra\DataTables\Facades\DataTables;
use App\Models\Inventory\ProductCategory;

class KpiReportController extends Controller
{
    public function KpiReportGet()
    {
        try{
            $currentDate = Carbon::now('Asia/Dhaka');
            $formattedCurrentDate=$currentDate->toDateString();
            $formattedCurrentMonth=$currentDate->month;
            $outlets=DB::table('outlets')->where('deleted_at', '=', null)->orderBy('name', 'ASC')->get();
            $technicians=DB::table('employees')->where('team_leader_id', '!=', null)->where('deleted_at', '=', null)->orderBy('name', 'ASC')->get();
            $faults = DB::table('faults')->get();

            $categories=DB::table('categories')->pluck('id');
            $jobs=DB::table('jobs')
                ->join('tickets','tickets.id','=','jobs.ticket_id')
                ->join('purchases','tickets.purchase_id','=','purchases.id')
                ->join('brand_models','purchases.brand_model_id','=','brand_models.id')
                ->join('categories','tickets.product_category_id','=','categories.id')
                ->join('inventory_stocks','jobs.id','=','inventory_stocks.job_id')
                ->leftJoin('parts', function ($join) {
                    $join->on('parts.id', '=', 'inventory_stocks.part_id');
                })

                ->select('jobs.*','jobs.job_number as jobNumber','brand_models.model_name as model_name','categories.name as modelName',
                    'tickets.fault_description_id as faultDescriptionId','tickets.fault_description_note as faultDescription',
                    'tickets.delivery_date_by_team_leader as deliveryDate','tickets.status as status','tickets.is_ended as ended','tickets.is_closed_by_teamleader as closedbyteamleader',
                    'tickets.is_reopened as reopened','tickets.is_delivered_by_teamleader as deliveredby_teamleader','tickets.is_closed as ticket_closed','tickets.is_delivered_by_call_center as deliveredby_call_center',
                    'tickets.is_started as started','tickets.is_accepted as accepted','tickets.is_assigned as assigned','tickets.is_rejected as rejected',

                    'jobs.job_close_remark as repairDescription','jobs.is_consumed as is_consumed', 'inventory_stocks.id as inventory_stock_id', 'parts.name as part_name','parts.code as part_code',
                    'jobs.job_start_time as receivedDate','jobs.job_end_time as repairDate','jobs.job_pending_note as job_pending_note')
                ->whereMonth('jobs.created_at','=', $formattedCurrentMonth)
                ->where('inventory_stocks.is_consumed', 1)
                ->where('jobs.deleted_at',null);

            if(request()->ajax()){

                return DataTables::of($jobs)
                    ->addColumn('fault_description', function ($jobs) use ($faults) {

                        $data = [];
                        $fault_name = '';
                        $faultId = json_decode($jobs->faultDescriptionId);
                        foreach ($faults as $fault) {
                            if ($faultId != null) {
                                if (in_array($fault->id, $faultId)) {
                                    $data[] = $fault->name;
                                }
                            }
                        }

                        foreach ($data as $key => $result) {
                            $total = count($data);
                            if ($total == 1) {
                                $fault_name .= $result;
                            } else {
                                $fault_name .= $result . '&nbsp, ';
                            }
                        };

                        return rtrim($fault_name, ', ');
                    })

                    ->addColumn('received_date', function ($jobs) {
                        $received_date=null;
                        if (isset($jobs->receivedDate)) {
                            $received_date=Carbon::parse($jobs->receivedDate)->format('m/d/Y');
                        }
                        return $received_date;
                    })
                    ->addColumn('repair_date', function ($jobs) {
                        $repair_date=null;
                        if (isset($jobs->job_end_time)) {
                            $repair_date=Carbon::parse($jobs->job_end_time)->format('m/d/Y');
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
                    ->addColumn('yes_no', function ($jobs) {
                        if ($jobs->is_consumed == 1) {
                            $yes_no="Yes";
                        }else{
                            $yes_no="No";
                        }

                        return $yes_no;

                    })

                    ->addColumn('part_name', function ($jobs) {
                        if (isset($jobs->part_name)) {
                            return $jobs->part_name;
                        }
                        return 'Not Found';
                    })
                    ->addColumn('part_code', function ($jobs) {
                        if (isset($jobs->part_code)) {
                            return $jobs->part_code;
                        }
                        return 'Not Found';
                    })
                    ->addColumn('status', function ($jobs) {
                        $res='Not Found';
                        if($jobs->status == 1 && $jobs->started==0 && $jobs->job_pending_note!=null){

                            $res='Pending';
                        }
                        elseif ($jobs->status == 0) {
                            $res='Created';
                        }
                        elseif($jobs->status == 1 && $jobs->ended == 1 && $jobs->closedbyteamleader == 1 && $jobs->reopened == 1)
                        {
                            $res='Ticket Re-Opened';
                        }
                        elseif($jobs->status == 1 && $jobs->ended == 1 && $jobs->closedbyteamleader == 1 && $jobs->deliveredby_teamleader == 0 && $jobs->ticket_closed == 0)
                        {
                            $res='Forwarded to CC';
                        }
                        elseif($jobs->status == 1 && $jobs->ended == 1 && $jobs->deliveredby_call_center == 1 && $jobs->ticket_closed == 0)
                        {
                            $res='Delivered by CC';
                        }
                        elseif($jobs->status == 1 && $jobs->ended == 1 && $jobs->deliveredby_teamleader == 1 && $jobs->ticket_closed == 0)
                        {
                            $res='Delivered by TL';
                        }
                        elseif($jobs->status == 1 && $jobs->ended == 1 && $jobs->deliveredby_call_center == 1 && $jobs->ticket_closed == 1)
                        {
                            $res='Ticket Closed';
                        }
                        elseif($jobs->status == 1 && $jobs->ended == 1 && $jobs->deliveredby_call_center == 0 && $jobs->ticket_closed == 1)
                        {
                            $res='Ticket Undelivered Closed';
                        }
                        elseif($jobs->status == 1 && $jobs->ended == 1)
                        {
                            $res='Job Completed';
                        }
                        elseif($jobs->status == 1 && $jobs->started == 1)
                        {
                            $res='Job Started';
                        }
                        elseif($jobs->status == 1 && $jobs->accepted == 1)
                        {
                            $res='Job Accepted';
                        }
                        elseif($jobs->status == 1 && $jobs->assigned == 1)
                        {
                            $res='Assigned';
                        }
                        elseif($jobs->status == 2 && $jobs->rejected == 1)
                        {
                            $res='Rejected';
                        }
                        return $res;
                    })
                    ->addColumn('repair_tat', function ($jobs) {
                        $received_date=Carbon::parse($jobs->receivedDate);
                        $repair_date=Carbon::parse($jobs->repairDate);
                        $diff_in_days = $repair_date->diffInDays($received_date);
                        return $diff_in_days;
                        
                    })    
                    ->addColumn('delivery_tat', function ($jobs) {
                        $received_date=Carbon::parse($jobs->receivedDate);
                        $diff_in_days_tat=null;
                        if (isset($jobs->deliveryDate)) {
                            $delivery_date=Carbon::parse($jobs->deliveryDate);
                            $diff_in_days_tat = $delivery_date->diffInDays($received_date);
                            
                            if($diff_in_days_tat == 0)
                            {
                                return $diff_in_days_tat;
                            }else{
                                return $diff_in_days_tat+1;
                            }
                        }
                        return $diff_in_days_tat;

                    })     
                    ->addColumn('repeat_repair', function ($jobs) {
                        $repeat_repair=0;
                        return $repeat_repair;
                    })    
                    ->addColumn('ltp', function ($jobs) {
                        $ltp=0;
                        return $ltp;
                    })     
                    ->addColumn('cmi', function ($jobs) {
                        $cpi=0;
                        return $cpi;
                    })
                    ->addIndexColumn()
                    ->rawColumns(['data','fault_description','part_name','tat'])
                    ->make(true);
            }
            return view ('reports.kpi.kpi-report', compact('outlets','technicians'));
        } catch (\Exception $e) {
            $bug = $e->getMessage();
            return redirect()->back()->with('error', $bug);
        }
    }

    public function KpiReportPost(Request $request)
    {
        try{
            $outlet=$request->outlet;
            $product_category=$request->product_category;
            $technician=$request->technician;
            $branch=$request->outlet;
            $product_category_name='';
            $branch_name='';
            $technician_name='';
            $formattedCurrentDate='';
            $startDate=Carbon::parse($request->start_date)->format('Y-m-d');
            $endDate=Carbon::parse($request->end_date)->format('Y-m-d');

            // Form Filtering start
            $outlets=DB::table('outlets')->where('deleted_at', '=', null)->orderBy('name', 'ASC')->get();
            $technicians=DB::table('employees')->where('team_leader_id', '!=', null)->where('deleted_at', '=', null)->orderBy('name', 'ASC')->get();
            // End

            $faults = DB::table('faults')->get();
            $categories=DB::table('categories')->pluck('id');

            if(!empty($product_category) && !empty($branch) && !empty($technician) && !empty($request->start_date) && !empty($request->end_date) )
            {
                // dd('product and branch and tech and date');
                $product_category_name=DB::table('categories')->where('id','=',$request->product_category)->first();
                $jobs=DB::table('jobs')
                ->join('tickets','tickets.id','=','jobs.ticket_id')
                ->join('purchases','tickets.purchase_id','=','purchases.id')
                ->join('brand_models','purchases.brand_model_id','=','brand_models.id')
                ->join('categories','tickets.product_category_id','=','categories.id')
                ->select('jobs.*','jobs.job_number as jobNumber','brand_models.model_name as model_name','categories.name as modelName','tickets.fault_description_id as faultDescriptionId','tickets.fault_description_note as faultDescription',
                'tickets.delivery_date_by_team_leader as deliveryDate','tickets.status as status','tickets.is_ended as ended','tickets.is_closed_by_teamleader as closedbyteamleader',
                'tickets.is_reopened as reopened','tickets.is_delivered_by_teamleader as deliveredby_teamleader','tickets.is_closed as ticket_closed','tickets.is_delivered_by_call_center as deliveredby_call_center',
                'tickets.is_started as started','tickets.is_accepted as accepted','tickets.is_assigned as assigned','tickets.is_rejected as rejected',

                'tickets.delivery_date_by_team_leader as deliveryDate','jobs.job_close_remark as repairDescription','jobs.job_start_time as receivedDate','jobs.job_end_time as repairDate','jobs.job_pending_note as job_pending_note')
                ->where('tickets.product_category_id','=',$product_category)
                ->where('tickets.outlet_id','=',$branch)
                ->where('jobs.user_id','=',$technician)
                ->whereBetween('jobs.created_at',[$startDate, $endDate])
                ->where('jobs.deleted_at',null)
                ->get();

            }
            else if(!empty($product_category) && !empty($branch) && !empty($technician))
            {
                $product_category_name=DB::table('categories')->where('id','=',$request->product_category)->first();
                $jobs=DB::table('jobs')
                ->join('tickets','tickets.id','=','jobs.ticket_id')
                ->join('purchases','tickets.purchase_id','=','purchases.id')
                ->join('brand_models','purchases.brand_model_id','=','brand_models.id')
                ->join('categories','tickets.product_category_id','=','categories.id')
                ->select('jobs.*','jobs.job_number as jobNumber','brand_models.model_name as model_name','categories.name as modelName','tickets.fault_description_id as faultDescriptionId','tickets.fault_description_note as faultDescription',
                'tickets.delivery_date_by_team_leader as deliveryDate','tickets.status as status','tickets.is_ended as ended','tickets.is_closed_by_teamleader as closedbyteamleader',
            'tickets.is_reopened as reopened','tickets.is_delivered_by_teamleader as deliveredby_teamleader','tickets.is_closed as ticket_closed','tickets.is_delivered_by_call_center as deliveredby_call_center',
            'tickets.is_started as started','tickets.is_accepted as accepted','tickets.is_assigned as assigned','tickets.is_rejected as rejected',

                'tickets.delivery_date_by_team_leader as deliveryDate','jobs.job_close_remark as repairDescription','jobs.job_start_time as receivedDate','jobs.job_end_time as repairDate','jobs.job_pending_note as job_pending_note')
                ->where('tickets.product_category_id','=',$product_category)
                ->where('tickets.outlet_id','=',$branch)
                ->where('jobs.user_id','=',$technician)
                ->where('jobs.deleted_at',null)
                ->get();

            }
            else if(!empty($product_category) && !empty($branch))
            {
                $product_category_name=DB::table('categories')->where('id','=',$request->product_category)->first();
                $jobs=DB::table('jobs')
                ->join('tickets','tickets.id','=','jobs.ticket_id')
                ->join('purchases','tickets.purchase_id','=','purchases.id')
                ->join('brand_models','purchases.brand_model_id','=','brand_models.id')
                ->join('categories','tickets.product_category_id','=','categories.id')
                ->select('jobs.*','jobs.job_number as jobNumber','brand_models.model_name as model_name','categories.name as modelName','tickets.fault_description_id as faultDescriptionId','tickets.fault_description_note as faultDescription',
                'tickets.delivery_date_by_team_leader as deliveryDate','tickets.status as status','tickets.is_ended as ended','tickets.is_closed_by_teamleader as closedbyteamleader',
            'tickets.is_reopened as reopened','tickets.is_delivered_by_teamleader as deliveredby_teamleader','tickets.is_closed as ticket_closed','tickets.is_delivered_by_call_center as deliveredby_call_center',
            'tickets.is_started as started','tickets.is_accepted as accepted','tickets.is_assigned as assigned','tickets.is_rejected as rejected',

                'tickets.delivery_date_by_team_leader as deliveryDate','jobs.job_close_remark as repairDescription','jobs.job_start_time as receivedDate','jobs.job_end_time as repairDate','jobs.job_pending_note as job_pending_note')
                ->where('tickets.product_category_id','=',$product_category)
                ->where('tickets.outlet_id','=',$branch)
                ->where('jobs.deleted_at',null)
                ->get();

            }
            else if(!empty($branch) && !empty($technician) && !empty($request->start_date) && !empty($request->end_date))
            {
                $product_category_name=DB::table('categories')->where('id','=',$request->product_category)->first();
                $jobs=DB::table('jobs')
                ->join('tickets','tickets.id','=','jobs.ticket_id')
                ->join('purchases','tickets.purchase_id','=','purchases.id')
                ->join('brand_models','purchases.brand_model_id','=','brand_models.id')
                ->join('categories','tickets.product_category_id','=','categories.id')
                ->select('jobs.*','jobs.job_number as jobNumber','brand_models.model_name as model_name','categories.name as modelName','tickets.fault_description_id as faultDescriptionId','tickets.fault_description_note as faultDescription',
                'tickets.delivery_date_by_team_leader as deliveryDate','tickets.status as status','tickets.is_ended as ended','tickets.is_closed_by_teamleader as closedbyteamleader',
            'tickets.is_reopened as reopened','tickets.is_delivered_by_teamleader as deliveredby_teamleader','tickets.is_closed as ticket_closed','tickets.is_delivered_by_call_center as deliveredby_call_center',
            'tickets.is_started as started','tickets.is_accepted as accepted','tickets.is_assigned as assigned','tickets.is_rejected as rejected',

                'tickets.delivery_date_by_team_leader as deliveryDate','jobs.job_close_remark as repairDescription','jobs.job_start_time as receivedDate','jobs.job_end_time as repairDate','jobs.job_pending_note as job_pending_note')
                ->where('tickets.outlet_id','=',$branch)
                ->where('jobs.user_id','=',$technician)
                ->whereBetween('jobs.created_at',[$startDate, $endDate])
                ->where('jobs.deleted_at',null)
                ->get();

            }
            else if(!empty($product_category) && !empty($technician) && !empty($request->start_date) && !empty($request->end_date))
            {
                $product_category_name=DB::table('categories')->where('id','=',$request->product_category)->first();
                $jobs=DB::table('jobs')
                ->join('tickets','tickets.id','=','jobs.ticket_id')
                ->join('purchases','tickets.purchase_id','=','purchases.id')
                ->join('brand_models','purchases.brand_model_id','=','brand_models.id')
                ->join('categories','tickets.product_category_id','=','categories.id')
                ->select('jobs.*','jobs.job_number as jobNumber','brand_models.model_name as model_name','categories.name as modelName','tickets.fault_description_id as faultDescriptionId','tickets.fault_description_note as faultDescription',
                'tickets.delivery_date_by_team_leader as deliveryDate','tickets.status as status','tickets.is_ended as ended','tickets.is_closed_by_teamleader as closedbyteamleader',
            'tickets.is_reopened as reopened','tickets.is_delivered_by_teamleader as deliveredby_teamleader','tickets.is_closed as ticket_closed','tickets.is_delivered_by_call_center as deliveredby_call_center',
            'tickets.is_started as started','tickets.is_accepted as accepted','tickets.is_assigned as assigned','tickets.is_rejected as rejected',

                'tickets.delivery_date_by_team_leader as deliveryDate','jobs.job_close_remark as repairDescription','jobs.job_start_time as receivedDate','jobs.job_end_time as repairDate','jobs.job_pending_note as job_pending_note')
                ->where('tickets.product_category_id','=',$product_category)
                ->where('jobs.user_id','=',$technician)
                ->whereBetween('jobs.created_at',[$startDate, $endDate])
                ->where('jobs.deleted_at',null)
                ->get();

            }
            else if(!empty($product_category) && !empty($technician))
            {
                $technician_name=DB::table('employees')->where('id','=',$technician)->first();
                $jobs=DB::table('jobs')
                ->join('tickets','tickets.id','=','jobs.ticket_id')
                ->join('purchases','tickets.purchase_id','=','purchases.id')
                ->join('brand_models','purchases.brand_model_id','=','brand_models.id')
                ->join('categories','tickets.product_category_id','=','categories.id')
                ->select('jobs.*','jobs.job_number as jobNumber','brand_models.model_name as model_name','categories.name as modelName','tickets.fault_description_id as faultDescriptionId','tickets.fault_description_note as faultDescription',
                'tickets.delivery_date_by_team_leader as deliveryDate','tickets.status as status','tickets.is_ended as ended','tickets.is_closed_by_teamleader as closedbyteamleader',
            'tickets.is_reopened as reopened','tickets.is_delivered_by_teamleader as deliveredby_teamleader','tickets.is_closed as ticket_closed','tickets.is_delivered_by_call_center as deliveredby_call_center',
            'tickets.is_started as started','tickets.is_accepted as accepted','tickets.is_assigned as assigned','tickets.is_rejected as rejected',

                'tickets.delivery_date_by_team_leader as deliveryDate','jobs.job_close_remark as repairDescription','jobs.job_start_time as receivedDate','jobs.job_end_time as repairDate','jobs.job_pending_note as job_pending_note')
                ->where('tickets.product_category_id','=',$product_category)
                ->where('jobs.user_id','=',$technician)
                ->where('jobs.deleted_at',null)
                ->get();
            }
            else if(!empty($product_category) && !empty($branch) && !empty($request->start_date) && !empty($request->end_date))
            {
                $product_category_name=DB::table('categories')->where('id','=',$request->product_category)->first();
                $jobs=DB::table('jobs')
                ->join('tickets','tickets.id','=','jobs.ticket_id')
                ->join('purchases','tickets.purchase_id','=','purchases.id')
                ->join('brand_models','purchases.brand_model_id','=','brand_models.id')
                ->join('categories','tickets.product_category_id','=','categories.id')
                ->select('jobs.*','jobs.job_number as jobNumber','brand_models.model_name as model_name','categories.name as modelName','tickets.fault_description_id as faultDescriptionId','tickets.fault_description_note as faultDescription',
                'tickets.delivery_date_by_team_leader as deliveryDate','tickets.status as status','tickets.is_ended as ended','tickets.is_closed_by_teamleader as closedbyteamleader',
            'tickets.is_reopened as reopened','tickets.is_delivered_by_teamleader as deliveredby_teamleader','tickets.is_closed as ticket_closed','tickets.is_delivered_by_call_center as deliveredby_call_center',
            'tickets.is_started as started','tickets.is_accepted as accepted','tickets.is_assigned as assigned','tickets.is_rejected as rejected',

                'tickets.delivery_date_by_team_leader as deliveryDate','jobs.job_close_remark as repairDescription','jobs.job_start_time as receivedDate','jobs.job_end_time as repairDate','jobs.job_pending_note as job_pending_note')
                ->where('tickets.product_category_id','=',$product_category)
                ->where('tickets.outlet_id','=',$branch)
                ->whereBetween('jobs.created_at',[$startDate, $endDate])
                ->where('jobs.deleted_at',null)
                ->get();

            }
            else if(!empty($product_category) && !empty($request->start_date) && !empty($request->end_date))
            {
                $branch_name=DB::table('outlets')->where('id','=',$branch)->first();
                $jobs=DB::table('jobs')
                ->join('tickets','tickets.id','=','jobs.ticket_id')
                ->join('purchases','tickets.purchase_id','=','purchases.id')
                ->join('brand_models','purchases.brand_model_id','=','brand_models.id')
                ->join('categories','tickets.product_category_id','=','categories.id')
                ->select('jobs.*','jobs.job_number as jobNumber','brand_models.model_name as model_name','categories.name as modelName','tickets.fault_description_id as faultDescriptionId','tickets.fault_description_note as faultDescription',
                'tickets.delivery_date_by_team_leader as deliveryDate','tickets.status as status','tickets.is_ended as ended','tickets.is_closed_by_teamleader as closedbyteamleader',
            'tickets.is_reopened as reopened','tickets.is_delivered_by_teamleader as deliveredby_teamleader','tickets.is_closed as ticket_closed','tickets.is_delivered_by_call_center as deliveredby_call_center',
            'tickets.is_started as started','tickets.is_accepted as accepted','tickets.is_assigned as assigned','tickets.is_rejected as rejected',

                'tickets.delivery_date_by_team_leader as deliveryDate','jobs.job_close_remark as repairDescription','jobs.job_start_time as receivedDate','jobs.job_end_time as repairDate','jobs.job_pending_note as job_pending_note')
                ->where('tickets.product_category_id','=',$product_category)
                ->whereBetween('jobs.created_at',[$startDate, $endDate])
                ->where('jobs.deleted_at',null)
                ->get();
            }
            else if(!empty($branch) && !empty($request->start_date) && !empty($request->end_date))
            {
                $branch_name=DB::table('outlets')->where('id','=',$branch)->first();
                $jobs=DB::table('jobs')
                ->join('tickets','tickets.id','=','jobs.ticket_id')
                ->join('purchases','tickets.purchase_id','=','purchases.id')
                ->join('brand_models','purchases.brand_model_id','=','brand_models.id')
                ->join('categories','tickets.product_category_id','=','categories.id')
                ->select('jobs.*','jobs.job_number as jobNumber','brand_models.model_name as model_name','categories.name as modelName','tickets.fault_description_id as faultDescriptionId','tickets.fault_description_note as faultDescription',
                'tickets.delivery_date_by_team_leader as deliveryDate','tickets.status as status','tickets.is_ended as ended','tickets.is_closed_by_teamleader as closedbyteamleader',
            'tickets.is_reopened as reopened','tickets.is_delivered_by_teamleader as deliveredby_teamleader','tickets.is_closed as ticket_closed','tickets.is_delivered_by_call_center as deliveredby_call_center',
            'tickets.is_started as started','tickets.is_accepted as accepted','tickets.is_assigned as assigned','tickets.is_rejected as rejected',

                'tickets.delivery_date_by_team_leader as deliveryDate','jobs.job_close_remark as repairDescription','jobs.job_start_time as receivedDate','jobs.job_end_time as repairDate','jobs.job_pending_note as job_pending_note')
                ->where('tickets.outlet_id','=',$branch)
                ->whereBetween('jobs.created_at',[$startDate, $endDate])
                ->where('jobs.deleted_at',null)
                ->get();
            }
            else if(!empty($technician) && !empty($request->start_date) && !empty($request->end_date))
            {
                $technician_name=DB::table('employees')->where('id','=',$technician)->first();
                $jobs=DB::table('jobs')
                ->join('tickets','tickets.id','=','jobs.ticket_id')
                ->join('purchases','tickets.purchase_id','=','purchases.id')
                ->join('brand_models','purchases.brand_model_id','=','brand_models.id')
                ->join('categories','tickets.product_category_id','=','categories.id')
                ->select('jobs.*','jobs.job_number as jobNumber','brand_models.model_name as model_name','categories.name as modelName','tickets.fault_description_id as faultDescriptionId','tickets.fault_description_note as faultDescription',
                'tickets.delivery_date_by_team_leader as deliveryDate','tickets.status as status','tickets.is_ended as ended','tickets.is_closed_by_teamleader as closedbyteamleader',
            'tickets.is_reopened as reopened','tickets.is_delivered_by_teamleader as deliveredby_teamleader','tickets.is_closed as ticket_closed','tickets.is_delivered_by_call_center as deliveredby_call_center',
            'tickets.is_started as started','tickets.is_accepted as accepted','tickets.is_assigned as assigned','tickets.is_rejected as rejected',

                'tickets.delivery_date_by_team_leader as deliveryDate','jobs.job_close_remark as repairDescription','jobs.job_start_time as receivedDate','jobs.job_end_time as repairDate','jobs.job_pending_note as job_pending_note')
                ->where('jobs.user_id','=',$technician)
                ->whereBetween('jobs.created_at',[$startDate, $endDate])
                ->where('jobs.deleted_at',null)
                ->get();
            }
            else if( !empty($request->start_date) && !empty($request->end_date) && $request->start_date == $request->end_date)
            {
                $jobs=DB::table('jobs')
                ->join('tickets','tickets.id','=','jobs.ticket_id')
                ->join('purchases','tickets.purchase_id','=','purchases.id')
                ->join('brand_models','purchases.brand_model_id','=','brand_models.id')
                ->join('categories','tickets.product_category_id','=','categories.id')
                ->select('jobs.*','jobs.job_number as jobNumber','brand_models.model_name as model_name','categories.name as modelName','tickets.fault_description_id as faultDescriptionId','tickets.fault_description_note as faultDescription',
                'tickets.delivery_date_by_team_leader as deliveryDate','tickets.status as status','tickets.is_ended as ended','tickets.is_closed_by_teamleader as closedbyteamleader',
            'tickets.is_reopened as reopened','tickets.is_delivered_by_teamleader as deliveredby_teamleader','tickets.is_closed as ticket_closed','tickets.is_delivered_by_call_center as deliveredby_call_center',
            'tickets.is_started as started','tickets.is_accepted as accepted','tickets.is_assigned as assigned','tickets.is_rejected as rejected',

                'tickets.delivery_date_by_team_leader as deliveryDate','jobs.job_close_remark as repairDescription','jobs.job_start_time as receivedDate','jobs.job_end_time as repairDate','jobs.job_pending_note as job_pending_note')
                ->whereDate('jobs.created_at',$startDate)
                ->where('jobs.deleted_at',null)
                ->get();
            }
            else if( !empty($request->start_date) && !empty($request->end_date))
            {
                $jobs=DB::table('jobs')
                ->join('tickets','tickets.id','=','jobs.ticket_id')
                ->join('purchases','tickets.purchase_id','=','purchases.id')
                ->join('brand_models','purchases.brand_model_id','=','brand_models.id')
                ->join('categories','tickets.product_category_id','=','categories.id')
                ->select('jobs.*','jobs.job_number as jobNumber','brand_models.model_name as model_name','categories.name as modelName','tickets.fault_description_id as faultDescriptionId','tickets.fault_description_note as faultDescription',
                'tickets.delivery_date_by_team_leader as deliveryDate','tickets.status as status','tickets.is_ended as ended','tickets.is_closed_by_teamleader as closedbyteamleader',
            'tickets.is_reopened as reopened','tickets.is_delivered_by_teamleader as deliveredby_teamleader','tickets.is_closed as ticket_closed','tickets.is_delivered_by_call_center as deliveredby_call_center',
            'tickets.is_started as started','tickets.is_accepted as accepted','tickets.is_assigned as assigned','tickets.is_rejected as rejected',

                'tickets.delivery_date_by_team_leader as deliveryDate','jobs.job_close_remark as repairDescription','jobs.job_start_time as receivedDate','jobs.job_end_time as repairDate','jobs.job_pending_note as job_pending_note')
                ->whereBetween('jobs.created_at',[$startDate, $endDate])
                ->where('jobs.deleted_at',null)
                ->get();
            }

            else if(!empty($product_category))
            {
                $product_category_name=DB::table('categories')->where('id','=',$request->product_category)->first();
                $jobs=DB::table('jobs')
                ->join('tickets','tickets.id','=','jobs.ticket_id')
                ->join('purchases','tickets.purchase_id','=','purchases.id')
                ->join('brand_models','purchases.brand_model_id','=','brand_models.id')
                ->join('categories','tickets.product_category_id','=','categories.id')
                ->select('jobs.*','jobs.job_number as jobNumber','brand_models.model_name as model_name','categories.name as modelName','tickets.fault_description_id as faultDescriptionId','tickets.fault_description_note as faultDescription',
                'tickets.delivery_date_by_team_leader as deliveryDate','tickets.status as status','tickets.is_ended as ended','tickets.is_closed_by_teamleader as closedbyteamleader',
            'tickets.is_reopened as reopened','tickets.is_delivered_by_teamleader as deliveredby_teamleader','tickets.is_closed as ticket_closed','tickets.is_delivered_by_call_center as deliveredby_call_center',
            'tickets.is_started as started','tickets.is_accepted as accepted','tickets.is_assigned as assigned','tickets.is_rejected as rejected',

                'tickets.delivery_date_by_team_leader as deliveryDate','jobs.job_close_remark as repairDescription','jobs.job_start_time as receivedDate','jobs.job_end_time as repairDate','jobs.job_pending_note as job_pending_note')
                ->where('tickets.product_category_id','=',$product_category)
                ->where('jobs.deleted_at',null)
                ->get();

            }
            else if(!empty($branch))
            {
                $branch_name=DB::table('outlets')->where('id','=',$branch)->first();
                $jobs=DB::table('jobs')
                ->join('tickets','tickets.id','=','jobs.ticket_id')
                ->join('purchases','tickets.purchase_id','=','purchases.id')
                ->join('brand_models','purchases.brand_model_id','=','brand_models.id')
                ->join('categories','tickets.product_category_id','=','categories.id')
                ->select('jobs.*','jobs.job_number as jobNumber','brand_models.model_name as model_name','categories.name as modelName','tickets.fault_description_id as faultDescriptionId','tickets.fault_description_note as faultDescription',
                'tickets.delivery_date_by_team_leader as deliveryDate','tickets.status as status','tickets.is_ended as ended','tickets.is_closed_by_teamleader as closedbyteamleader',
            'tickets.is_reopened as reopened','tickets.is_delivered_by_teamleader as deliveredby_teamleader','tickets.is_closed as ticket_closed','tickets.is_delivered_by_call_center as deliveredby_call_center',
            'tickets.is_started as started','tickets.is_accepted as accepted','tickets.is_assigned as assigned','tickets.is_rejected as rejected',

                'tickets.delivery_date_by_team_leader as deliveryDate','jobs.job_close_remark as repairDescription','jobs.job_start_time as receivedDate','jobs.job_end_time as repairDate','jobs.job_pending_note as job_pending_note')
                ->where('tickets.outlet_id','=',$branch)
                ->where('jobs.deleted_at',null)
                ->get();
            }
            else if(!empty($technician))
            {
                $technician_name=DB::table('employees')->where('id','=',$technician)->first();
                $jobs=DB::table('jobs')
                ->join('tickets','tickets.id','=','jobs.ticket_id')
                ->join('purchases','tickets.purchase_id','=','purchases.id')
                ->join('brand_models','purchases.brand_model_id','=','brand_models.id')
                ->join('categories','tickets.product_category_id','=','categories.id')
                ->select('jobs.*','jobs.job_number as jobNumber','brand_models.model_name as model_name','categories.name as modelName','tickets.fault_description_id as faultDescriptionId','tickets.fault_description_note as faultDescription',
                'tickets.delivery_date_by_team_leader as deliveryDate','tickets.status as status','tickets.is_ended as ended','tickets.is_closed_by_teamleader as closedbyteamleader',
            'tickets.is_reopened as reopened','tickets.is_delivered_by_teamleader as deliveredby_teamleader','tickets.is_closed as ticket_closed','tickets.is_delivered_by_call_center as deliveredby_call_center',
            'tickets.is_started as started','tickets.is_accepted as accepted','tickets.is_assigned as assigned','tickets.is_rejected as rejected',

                'tickets.delivery_date_by_team_leader as deliveryDate','jobs.job_close_remark as repairDescription','jobs.job_start_time as receivedDate','jobs.job_end_time as repairDate','jobs.job_pending_note as job_pending_note')
                ->where('jobs.user_id','=',$technician)
                ->where('jobs.deleted_at',null)
                ->get();
            }
            else
            {
                $currentDate = Carbon::now('Asia/Dhaka');
                $formattedCurrentDate=$currentDate->toDateString();
                $jobs=DB::table('jobs')
                ->join('tickets','tickets.id','=','jobs.ticket_id')
                ->join('purchases','tickets.purchase_id','=','purchases.id')
                ->join('brand_models','purchases.brand_model_id','=','brand_models.id')
                ->join('categories','tickets.product_category_id','=','categories.id')
                ->select('jobs.*','jobs.job_number as jobNumber','brand_models.model_name as model_name','categories.name as modelName','tickets.fault_description_id as faultDescriptionId','tickets.fault_description_note as faultDescription',
                'tickets.delivery_date_by_team_leader as deliveryDate','tickets.status as status','tickets.is_ended as ended','tickets.is_closed_by_teamleader as closedbyteamleader',
            'tickets.is_reopened as reopened','tickets.is_delivered_by_teamleader as deliveredby_teamleader','tickets.is_closed as ticket_closed','tickets.is_delivered_by_call_center as deliveredby_call_center',
            'tickets.is_started as started','tickets.is_accepted as accepted','tickets.is_assigned as assigned','tickets.is_rejected as rejected',

                'jobs.job_close_remark as repairDescription','jobs.job_start_time as receivedDate','jobs.job_end_time as repairDate','jobs.job_pending_note as job_pending_note')
                ->whereDate('jobs.created_at', $formattedCurrentDate)
                ->where('jobs.deleted_at',null)
                ->get();
            }
            $jobinfo=[];
            foreach ($jobs as $key => $value) {

                $item['job_number'] = $value->jobNumber;
                $item['model_name'] = $value->model_name;

                $data = [];
                $fault_name = '';
                if ($value->faultDescriptionId) {
                    $faultId = json_decode($value->faultDescriptionId);
                        foreach ($faults as $fault) {
                            if ($faultId != null) {
                                if (in_array($fault->id, $faultId)) {
                                    $data[] = $fault->name;
                                 }
                            }
                        }
                        foreach ($data as $key => $result) {
                            $total = count($data);
                                if ($total == 1) {
                                    $fault_name .= $result;
                                } else {
                                    $fault_name .= $result . '; ';
                                }
                        };
                $item['fault_description'] = $fault_name;
                }
                $item['repair_description'] = $value->repairDescription;
                $receive_date=null;
                if (isset($value->receivedDate)) {
                    $receive_date=Carbon::parse($value->receivedDate)->format('m/d/Y');
                }
                $item['received_date'] = $receive_date;

                $repair_date=null;
                if (isset($value->repairDate)) {
                    $repair_date=Carbon::parse($value->repairDate)->format('m/d/Y');
                }
                $item['repair_date'] = $repair_date;

                $delivery_date=null;
                if (isset($value->deliveryDate)) {
                    $delivery_date=Carbon::parse($value->deliveryDate);
                }
                $item['delivery_date'] = $delivery_date;

                $item['yes_no'] = $value->is_consumed ? 'Yes' : 'No';

                $item['status'] = $value->status ?? null;
                $item['ended'] = $value->ended ?? null;
                $item['closedbyteamleader'] = $value->closedbyteamleader ?? null;
                $item['reopened'] = $value->reopened ?? null;
                $item['deliveredby_teamleader'] = $value->deliveredby_teamleader ?? null;
                $item['ticket_closed'] = $value->ticket_closed ?? null;
                $item['deliveredby_call_center'] = $value->deliveredby_call_center ?? null;
                $item['started'] = $value->started ?? null;
                $item['accepted'] = $value->accepted ?? null;
                $item['assigned'] = $value->assigned ?? null;
                $item['rejected'] = $value->rejected ?? null;
                $item['job_pending_note'] = $value->job_pending_note ?? null;

                $consumptions=InventoryStock::where('is_consumed',1)->where('job_id', $value->id)->where('stock_out', '>', 0)->get();
                    $res='Not Found';
                    $name = [];
                    $code = [];
                    $part_name = '';
                    $part_code = '';
                if(!empty($consumptions)){
                            foreach($consumptions as $detail){
                                $name[] = $detail->part->name;
                                $code[] = $detail->part->code;
                            }

                            foreach ($name as $key => $result) {
                                $total = count($name);
                                if ($total == 1) {
                                    $part_name .= $result;
                                } else {
                                    $part_name .= $result . '; ';
                                }
                            };

                            foreach ($code as $key => $result) {
                                $total = count($code);
                                if ($total == 1) {
                                    $part_code .= $result;
                                } else {
                                    $part_code .= $result . '; ';
                                }
                            };
                }

                $item['part_name'] = $part_name ?? null;
                $item['part_code'] = $part_code ?? null;


                $received_date=Carbon::parse($value->receivedDate);
                $repair_date=null;
                if (isset($value->job_end_time)) {
                    $repair_date=Carbon::parse($value->repairDate);
                }



                $diff_in_days_repair=0;
                if (isset($repair_date)) {
                    $diff_in_days_repair = $repair_date->diffInDays($received_date);
                }

                $diff_in_days_delivery=0;
                if(isset($delivery_date)){
                    $diff_in_days_delivery = $delivery_date->diffInDays($received_date);
                }


                $item['repair_tat'] = $diff_in_days_repair;
                $item['delivery_tat'] = $diff_in_days_delivery;
                array_push($jobinfo, $item);
            }
            return view ('reports.kpi.kpi-report-filter', compact('outlets','technicians','jobinfo','product_category_name','branch_name','technician_name','formattedCurrentDate'));
        } catch (\Exception $e) {
            $bug = $e->getMessage();
            return redirect()->back()->with('error', $bug);
        }
    }
}
