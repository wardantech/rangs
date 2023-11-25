<?php

namespace App\Http\Controllers\Report;

use DataTables;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\Ticket\Ticket;
use App\Models\Inventory\Category;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\Inventory\InventoryStock;
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
            ->join('employees', 'jobs.employee_id', '=', 'employees.id')
            ->join('users', 'jobs.created_by', '=', 'users.id')
            ->join('tickets','tickets.id','=','jobs.ticket_id')
            ->join('outlets','tickets.outlet_id','=','outlets.id')
            ->join('purchases','tickets.purchase_id','=','purchases.id')
            ->join('brand_models','purchases.brand_model_id','=','brand_models.id')
            ->join('categories','tickets.product_category_id','=','categories.id')
            ->select('jobs.*','jobs.job_number as job_number','brand_models.model_name as model_name','categories.name as modelName',
            'tickets.fault_description_id as faultDescriptionId','tickets.fault_description_note as faultDescription','tickets.created_at as ticket_date',
            'tickets.delivery_date_by_team_leader as deliveryDate','tickets.status as status','tickets.is_ended as ended','tickets.is_closed_by_teamleader as closedbyteamleader','tickets.is_pending as is_pending',
            'tickets.is_reopened as reopened','tickets.is_delivered_by_teamleader as deliveredby_teamleader','tickets.is_closed as ticket_closed','tickets.is_delivered_by_call_center as deliveredby_call_center',
            'tickets.is_started as started','tickets.is_paused as is_paused','tickets.is_accepted as accepted','tickets.is_assigned as assigned','tickets.is_rejected as rejected','tickets.id as ticket_id',
            'jobs.created_at as job_assigned_date','jobs.job_close_remark as repairDescription','jobs.is_consumed as is_consumed','purchases.purchase_date as purchase_date','employees.name as employee_name','outlets.name as outlet_name','users.name as created_by',
            'jobs.job_start_time as receivedDate','jobs.job_end_time as repairDate','jobs.job_pending_note as job_pending_note')
            ->whereMonth('tickets.delivery_date_by_team_leader','=', $formattedCurrentMonth)
            ->where('tickets.status',8)
            ->where('jobs.deleted_at',null)
            ->latest()
			->get();
            if(request()->ajax()){

                    return DataTables::of($jobs)
                                ->addColumn('emplyee_name', function ($jobs) {
                                    $employee_name=$jobs->employee_name ?? null;
                                    return $employee_name;
                                })

                                ->addColumn('outlet_name', function ($jobs) {
                                    $outlet_name=$jobs->outlet_name ?? Null;
                                    return $outlet_name;
                                })
                                ->addColumn('ticket_sl', function ($jobs) {
                                    return '<a href="'.route('show-ticket-details', $jobs->ticket_id).'" class="badge badge-primary" title="Ticket Details" target="_blank">'.'TSL-'.''. $jobs->ticket_id.'</a>';
                                })
                                ->addColumn('created_at', function($jobs){
                                    $created_at=Carbon::parse($jobs->ticket_date)->format('m/d/Y H:i:s');  
                                    return $created_at;
                                })
                                ->addColumn('purchase_date', function ($jobs) {
                                    $purchase_date=Carbon::parse($jobs->purchase_date)->format('m/d/Y');                        
                                    return $purchase_date;
                                })
                                ->addColumn('created_by', function ($jobs) {
                                    $created_by=$jobs->created_by; 
                                    return $created_by;
                                })
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
                                ->addColumn('fault_description_note', function($jobs){
                                    return $jobs->faultDescription;
                                })
                                ->addColumn('job_ending_remark', function($jobs){
                                    return $jobs->job_ending_remark;
                                })
                                ->addColumn('ticket_date', function ($jobs) {
                                    $ticket_date=null;
                                    if($jobs->ticket_date){
                                        $ticket_date=Carbon::parse($jobs->ticket_date)->format('m/d/Y'); 
                                    }
                                    return $ticket_date;
                                })
                                ->addColumn('job_assigned_date', function ($jobs) {
                                    $job_assigned_date=null;
                                    if($jobs->job_assigned_date){
                                        $job_assigned_date=Carbon::parse($jobs->job_assigned_date)->format('m/d/Y'); 
                                    }
                                    return $job_assigned_date;
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
                                    $consumptions=DB::table('inventory_stocks')
                                    ->join('parts','parts.id','=','inventory_stocks.part_id')
                                    ->select('parts.name as part_name')
                                    ->where('is_consumed', 1)->where('job_id', $jobs->id)->get();
                                    $res='Not Found';
                                    if(!empty($consumptions)){
                                        $data = [];
                                        $part_name = '';
                                        foreach($consumptions as $detail){
                                                $data[] = $detail->part_name;
                                        }
        
                                        foreach ($data as $key => $result) {
                                            $total = count($data);
                                            if ($total == 1) {
                                                $part_name .= $result;
                                            } else {
                                                $part_name .= $result . '; ';
                                            }
                                        };
                                        return rtrim($part_name, ', ');
                                    }else{
                                        return $res; 
                                    }


                                })  
                                ->addColumn('part_code', function ($jobs) {
                                    $consumptions=DB::table('inventory_stocks')
                                    ->join('parts','parts.id','=','inventory_stocks.part_id')
                                    ->select('parts.code as part_code')
                                    ->where('is_consumed', 1)->where('job_id', $jobs->id)->get();
                                        $res='Not Found';
                                        if(!empty($consumptions)){

                                            $data = [];
                                            $part_code = '';
                                            foreach($consumptions as $detail){
                                                    $data[] = $detail->part_code;
                                            }
                                            foreach ($data as $key => $result) {
                                                $total = count($data);
                                                if ($total == 1) {
                                                    $part_code .= $result;
                                                } else {
                                                    $part_code .= $result . '<br/>';
                                                }
                                            };
                                            return rtrim($part_code, ', ');
                                        }else{
                                            return $res; 
                                        }
                                }) 
                                ->addColumn('status', function ($jobs) {

                                    if ($jobs->status == 9 && $jobs->reopened == 1){
                                        return '<span class="badge bg-red">Ticket Re-Opened</span>';
                                    }
                                    
                                    elseif( $jobs->status == 0 ){
                                        return '<span class="badge bg-yellow">Created</span>';
                                    }
            
                                    
                                    elseif($jobs->status == 6 && $jobs->is_pending==1 )
                                    {
                                        return '<span class="badge bg-orange">Pending</span>';
                                    }
            
                                    elseif($jobs->status == 5 && $jobs->is_paused == 1 )
                                    {
                                        return '<span class="badge bg-red">Paused</span>';
                                    }
            
                                    elseif($jobs->status == 7  && $jobs->closedbyteamleader == 1)
                                    {
                                        return '<span class="badge bg-green">Forwarded to CC</span>';
                                    }
                                    elseif($jobs->status == 10 && $jobs->deliveredby_call_center == 1 )
                                    {
                                        return '<span class="badge bg-green">Delivered by CC</span>';
                                    }
                                    elseif($jobs->status == 8 && $jobs->deliveredby_teamleader == 1 )
                                    {
                                        return '<span class="badge bg-green">Delivered by TL</span>';
                                    }
            
                                    elseif($jobs->status == 12 && $jobs->deliveredby_call_center == 1  && $jobs->ticket_closed == 1)
                                    {
                                        return '<span class="badge badge-danger">Tticket is Closed</span>';
                                    }
                                    elseif($jobs->status == 12 && $jobs->deliveredby_call_center == 0 && $jobs->ticket_closed == 1)
                                    {
                                        return '<span class="badge badge-danger">Ticket is Undelivered Closed</span>';
                                    }
                                    elseif($jobs->status == 11 && $jobs->ended == 1)
                                    {
                                        return '<span class="badge badge-success">Job Completed</span>';
                                    }
            
                                    elseif($jobs->status == 4 && $jobs->started == 1)
                                    {
                                        return '<span class="badge badge-info">Job Started</span>';
                                    }
                                    elseif($jobs->status == 3 && $jobs->accepted == 1)
                                    {
                                        return '<span class="badge badge-primary">Job Accepted</span>';
                                    }
                                    elseif($jobs->status == 1 && $jobs->assigned == 1)
                                    {
                                        return '<span class="badge bg-blue">Assigned</span>';
                                    }
                                    elseif ($jobs->status == 2 && $jobs->rejected == 1)
                                    {
                                        return '<span class="badge bg-red">Rejected</span>';
                                    }
                                    
                                })
                                ->addColumn('repair_tat', function ($jobs) {
                                    $job_assigned_date=Carbon::parse($jobs->job_assigned_date);
                                    $diff_in_days_repair_tat=null;
                                    if (isset($jobs->repairDate)) {
                                        $repair_date=Carbon::parse($jobs->repairDate);
                                        $diff_in_days_repair_tat = $repair_date->diffInDays($job_assigned_date);
                                        
                                        if($diff_in_days_repair_tat == 0)
                                        {
                                            return $diff_in_days_repair_tat;
                                        }else{
                                            return $diff_in_days_repair_tat+1;
                                        }
                                    }
                                    return $diff_in_days_repair_tat;
                                    
                                })    
                                ->addColumn('delivery_tat', function ($jobs) {
                                    $job_assigned_date=Carbon::parse($jobs->job_assigned_date);
                                    $diff_in_days_delivery_tat=null;
                                    if (isset($jobs->deliveryDate)) {
                                        $delivery_date=Carbon::parse($jobs->deliveryDate);
                                        $diff_in_days_delivery_tat = $delivery_date->diffInDays($job_assigned_date);
                                        
                                        if($diff_in_days_delivery_tat == 0)
                                        {
                                            return $diff_in_days_delivery_tat;
                                        }else{
                                            return $diff_in_days_delivery_tat+1;
                                        }
                                    }
                                    return $diff_in_days_delivery_tat;

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
                                    $customerFeedbacks= DB::table('customer_feedback')
                                    ->join('feedback_questions', 'feedback_questions.id', '=', 'customer_feedback.question_id')
                                    ->where('ticket_id', $jobs->ticket_id)
                                    ->select('customer_feedback.question_feedback as question_feedback', 'feedback_questions.question as question')
                                    ->get();
                                    $res='Not Found';
                                    if(!empty($customerFeedbacks)){
                                        $key=0;
                                        $data = [];
                                        $cmi = '';
                                        foreach($customerFeedbacks as $key=>$value){
                                            $ans='';
                                            if ($value->question_feedback==0) {
                                                $ans ='NA';
                                            } elseif($value->question_feedback==1) {
                                                $ans ='Average';
                                            } elseif($value->question_feedback==2) {
                                                $ans ='Good';
                                            } elseif($value->question_feedback==3) {
                                                $ans='Great';
                                            }
                                            
                                            $data[]="#".( $key + 1).'. '.$value->question.' : '.$ans;
                                        }
                                        foreach ($data as $result) {
                                            $total = count($data);
                                            if ($total == 1) {
                                                $cmi .= $result;
                                            } else {
                                                $cmi .= $result . '<br/>';
                                            }
                                        };
                                        return rtrim($cmi, ', ');
                                    }else{
                                        return $cmi; 
                                    }
                                })
                                ->addIndexColumn()
                                ->rawColumns(['data','fault_description','part_name','tat','status','cmi','ticket_sl'])
                                ->make(true);
            }
            return view('reports.kpi.kpi-report', compact('outlets','technicians'));
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
                $product_category_name=DB::table('categories')->where('id','=',$request->product_category)->first();
                $jobs=DB::table('jobs')
                ->join('employees', 'jobs.employee_id', '=', 'employees.id')
                ->join('users', 'jobs.created_by', '=', 'users.id')
                ->join('tickets','tickets.id','=','jobs.ticket_id')
                ->join('outlets','tickets.outlet_id','=','outlets.id')
                ->join('purchases','tickets.purchase_id','=','purchases.id')
                ->join('brand_models','purchases.brand_model_id','=','brand_models.id')
                ->join('categories','tickets.product_category_id','=','categories.id')
                ->select('jobs.id as job_id','jobs.job_number as job_number','brand_models.model_name as model_name','categories.name as modelName',
                'tickets.fault_description_id as faultDescriptionId','tickets.fault_description_note as faultDescription','tickets.created_at as ticket_date',
                'tickets.delivery_date_by_team_leader as deliveryDate','tickets.status as status','tickets.is_ended as ended','tickets.is_closed_by_teamleader as closedbyteamleader','tickets.is_pending as is_pending',
                'tickets.is_reopened as reopened','tickets.is_delivered_by_teamleader as deliveredby_teamleader','tickets.is_closed as ticket_closed','tickets.is_delivered_by_call_center as deliveredby_call_center',
                'tickets.is_started as started','tickets.is_paused as is_paused','tickets.is_accepted as accepted','tickets.is_assigned as assigned','tickets.is_rejected as rejected','tickets.id as ticket_id',
                'jobs.created_at as job_assigned_date','jobs.job_close_remark as repairDescription','jobs.is_consumed as is_consumed','purchases.purchase_date as purchase_date','employees.name as employee_name','outlets.name as outlet_name','users.name as created_by',
                'jobs.job_start_time as receivedDate','jobs.job_end_time as repairDate','jobs.job_pending_note as job_pending_note','jobs.job_ending_remark')
                ->where('tickets.product_category_id','=',$product_category)
                ->where('tickets.outlet_id','=',$branch)
                ->where('jobs.user_id','=',$technician)
                ->whereBetween('tickets.delivery_date_by_team_leader',[$startDate, $endDate])
                ->where('jobs.deleted_at',null)
                ->get();
                
            }
            else if(!empty($product_category) && !empty($branch) && !empty($technician))
            {
                $product_category_name=DB::table('categories')->where('id','=',$request->product_category)->first();
                $jobs=DB::table('jobs')
                ->join('employees', 'jobs.employee_id', '=', 'employees.id')
                ->join('users', 'jobs.created_by', '=', 'users.id')
                ->join('tickets','tickets.id','=','jobs.ticket_id')
                ->join('outlets','tickets.outlet_id','=','outlets.id')
                ->join('purchases','tickets.purchase_id','=','purchases.id')
                ->join('brand_models','purchases.brand_model_id','=','brand_models.id')
                ->join('categories','tickets.product_category_id','=','categories.id')
                ->select('jobs.id as job_id','jobs.job_number as job_number','brand_models.model_name as model_name','categories.name as modelName',
                'tickets.fault_description_id as faultDescriptionId','tickets.fault_description_note as faultDescription','tickets.created_at as ticket_date',
                'tickets.delivery_date_by_team_leader as deliveryDate','tickets.status as status','tickets.is_ended as ended','tickets.is_closed_by_teamleader as closedbyteamleader','tickets.is_pending as is_pending',
                'tickets.is_reopened as reopened','tickets.is_delivered_by_teamleader as deliveredby_teamleader','tickets.is_closed as ticket_closed','tickets.is_delivered_by_call_center as deliveredby_call_center',
                'tickets.is_started as started','tickets.is_paused as is_paused','tickets.is_accepted as accepted','tickets.is_assigned as assigned','tickets.is_rejected as rejected','tickets.id as ticket_id',
                'jobs.created_at as job_assigned_date','jobs.job_close_remark as repairDescription','jobs.is_consumed as is_consumed','purchases.purchase_date as purchase_date','employees.name as employee_name','outlets.name as outlet_name','users.name as created_by',
                'jobs.job_start_time as receivedDate','jobs.job_end_time as repairDate','jobs.job_pending_note as job_pending_note','jobs.job_ending_remark')
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
                ->join('employees', 'jobs.employee_id', '=', 'employees.id')
                ->join('users', 'jobs.created_by', '=', 'users.id')
                ->join('tickets','tickets.id','=','jobs.ticket_id')
                ->join('outlets','tickets.outlet_id','=','outlets.id')
                ->join('purchases','tickets.purchase_id','=','purchases.id')
                ->join('brand_models','purchases.brand_model_id','=','brand_models.id')
                ->join('categories','tickets.product_category_id','=','categories.id')
                ->select('jobs.id as job_id','jobs.job_number as job_number','brand_models.model_name as model_name','categories.name as modelName',
                'tickets.fault_description_id as faultDescriptionId','tickets.fault_description_note as faultDescription','tickets.created_at as ticket_date',
                'tickets.delivery_date_by_team_leader as deliveryDate','tickets.status as status','tickets.is_ended as ended','tickets.is_closed_by_teamleader as closedbyteamleader','tickets.is_pending as is_pending',
                'tickets.is_reopened as reopened','tickets.is_delivered_by_teamleader as deliveredby_teamleader','tickets.is_closed as ticket_closed','tickets.is_delivered_by_call_center as deliveredby_call_center',
                'tickets.is_started as started','tickets.is_paused as is_paused','tickets.is_accepted as accepted','tickets.is_assigned as assigned','tickets.is_rejected as rejected','tickets.id as ticket_id',
                'jobs.created_at as job_assigned_date','jobs.job_close_remark as repairDescription','jobs.is_consumed as is_consumed','purchases.purchase_date as purchase_date','employees.name as employee_name','outlets.name as outlet_name','users.name as created_by',
                'jobs.job_start_time as receivedDate','jobs.job_end_time as repairDate','jobs.job_pending_note as job_pending_note','jobs.job_ending_remark')
                ->where('tickets.product_category_id','=',$product_category)
                ->where('tickets.outlet_id','=',$branch)
                ->where('jobs.deleted_at',null)
                ->get();
            
            }
            else if(!empty($branch) && !empty($technician) && !empty($request->start_date) && !empty($request->end_date))
            {
                $product_category_name=DB::table('categories')->where('id','=',$request->product_category)->first();
                $jobs=DB::table('jobs')
                ->join('employees', 'jobs.employee_id', '=', 'employees.id')
                ->join('users', 'jobs.created_by', '=', 'users.id')
                ->join('tickets','tickets.id','=','jobs.ticket_id')
                ->join('outlets','tickets.outlet_id','=','outlets.id')
                ->join('purchases','tickets.purchase_id','=','purchases.id')
                ->join('brand_models','purchases.brand_model_id','=','brand_models.id')
                ->join('categories','tickets.product_category_id','=','categories.id')
                ->select('jobs.id as job_id','jobs.job_number as job_number','brand_models.model_name as model_name','categories.name as modelName',
                'tickets.fault_description_id as faultDescriptionId','tickets.fault_description_note as faultDescription','tickets.created_at as ticket_date',
                'tickets.delivery_date_by_team_leader as deliveryDate','tickets.status as status','tickets.is_ended as ended','tickets.is_closed_by_teamleader as closedbyteamleader','tickets.is_pending as is_pending',
                'tickets.is_reopened as reopened','tickets.is_delivered_by_teamleader as deliveredby_teamleader','tickets.is_closed as ticket_closed','tickets.is_delivered_by_call_center as deliveredby_call_center',
                'tickets.is_started as started','tickets.is_paused as is_paused','tickets.is_accepted as accepted','tickets.is_assigned as assigned','tickets.is_rejected as rejected','tickets.id as ticket_id',
                'jobs.created_at as job_assigned_date','jobs.job_close_remark as repairDescription','jobs.is_consumed as is_consumed','purchases.purchase_date as purchase_date','employees.name as employee_name','outlets.name as outlet_name','users.name as created_by',
                'jobs.job_start_time as receivedDate','jobs.job_end_time as repairDate','jobs.job_pending_note as job_pending_note','jobs.job_ending_remark')
                ->where('tickets.outlet_id','=',$branch)
                ->where('jobs.user_id','=',$technician)
                ->whereBetween('tickets.delivery_date_by_team_leader',[$startDate, $endDate])
                ->where('jobs.deleted_at',null)
                ->get();
            
            }
            else if(!empty($product_category) && !empty($technician) && !empty($request->start_date) && !empty($request->end_date))
            {
                $product_category_name=DB::table('categories')->where('id','=',$request->product_category)->first();
                $jobs=DB::table('jobs')
                ->join('employees', 'jobs.employee_id', '=', 'employees.id')
                ->join('users', 'jobs.created_by', '=', 'users.id')
                ->join('tickets','tickets.id','=','jobs.ticket_id')
                ->join('outlets','tickets.outlet_id','=','outlets.id')
                ->join('purchases','tickets.purchase_id','=','purchases.id')
                ->join('brand_models','purchases.brand_model_id','=','brand_models.id')
                ->join('categories','tickets.product_category_id','=','categories.id')
                ->select('jobs.id as job_id','jobs.job_number as job_number','brand_models.model_name as model_name','categories.name as modelName',
                'tickets.fault_description_id as faultDescriptionId','tickets.fault_description_note as faultDescription','tickets.created_at as ticket_date',
                'tickets.delivery_date_by_team_leader as deliveryDate','tickets.status as status','tickets.is_ended as ended','tickets.is_closed_by_teamleader as closedbyteamleader','tickets.is_pending as is_pending',
                'tickets.is_reopened as reopened','tickets.is_delivered_by_teamleader as deliveredby_teamleader','tickets.is_closed as ticket_closed','tickets.is_delivered_by_call_center as deliveredby_call_center',
                'tickets.is_started as started','tickets.is_paused as is_paused','tickets.is_accepted as accepted','tickets.is_assigned as assigned','tickets.is_rejected as rejected','tickets.id as ticket_id',
                'jobs.created_at as job_assigned_date','jobs.job_close_remark as repairDescription','jobs.is_consumed as is_consumed','purchases.purchase_date as purchase_date','employees.name as employee_name','outlets.name as outlet_name','users.name as created_by',
                'jobs.job_start_time as receivedDate','jobs.job_end_time as repairDate','jobs.job_pending_note as job_pending_note','jobs.job_ending_remark')
                ->where('tickets.product_category_id','=',$product_category)
                ->where('jobs.user_id','=',$technician)
                ->whereBetween('tickets.delivery_date_by_team_leader',[$startDate, $endDate])
                ->where('jobs.deleted_at',null)
                ->get();
            
            }
            else if(!empty($product_category) && !empty($technician))
            {
                $technician_name=DB::table('employees')->where('id','=',$technician)->first();
                $jobs=DB::table('jobs')
                ->join('employees', 'jobs.employee_id', '=', 'employees.id')
                ->join('users', 'jobs.created_by', '=', 'users.id')
                ->join('tickets','tickets.id','=','jobs.ticket_id')
                ->join('outlets','tickets.outlet_id','=','outlets.id')
                ->join('purchases','tickets.purchase_id','=','purchases.id')
                ->join('brand_models','purchases.brand_model_id','=','brand_models.id')
                ->join('categories','tickets.product_category_id','=','categories.id')
                ->select('jobs.id as job_id','jobs.job_number as job_number','brand_models.model_name as model_name','categories.name as modelName',
                'tickets.fault_description_id as faultDescriptionId','tickets.fault_description_note as faultDescription','tickets.created_at as ticket_date',
                'tickets.delivery_date_by_team_leader as deliveryDate','tickets.status as status','tickets.is_ended as ended','tickets.is_closed_by_teamleader as closedbyteamleader','tickets.is_pending as is_pending',
                'tickets.is_reopened as reopened','tickets.is_delivered_by_teamleader as deliveredby_teamleader','tickets.is_closed as ticket_closed','tickets.is_delivered_by_call_center as deliveredby_call_center',
                'tickets.is_started as started','tickets.is_paused as is_paused','tickets.is_accepted as accepted','tickets.is_assigned as assigned','tickets.is_rejected as rejected','tickets.id as ticket_id',
                'jobs.created_at as job_assigned_date','jobs.job_close_remark as repairDescription','jobs.is_consumed as is_consumed','purchases.purchase_date as purchase_date','employees.name as employee_name','outlets.name as outlet_name','users.name as created_by',
                'jobs.job_start_time as receivedDate','jobs.job_end_time as repairDate','jobs.job_pending_note as job_pending_note','jobs.job_ending_remark')
                ->where('tickets.product_category_id','=',$product_category)
                ->where('jobs.user_id','=',$technician)
                ->where('jobs.deleted_at',null)
                ->get();
            } 
            else if(!empty($product_category) && !empty($branch) && !empty($request->start_date) && !empty($request->end_date))
            {
                $product_category_name=DB::table('categories')->where('id','=',$request->product_category)->first();
                $jobs=DB::table('jobs')
                ->join('employees', 'jobs.employee_id', '=', 'employees.id')
                ->join('users', 'jobs.created_by', '=', 'users.id')
                ->join('tickets','tickets.id','=','jobs.ticket_id')
                ->join('outlets','tickets.outlet_id','=','outlets.id')
                ->join('purchases','tickets.purchase_id','=','purchases.id')
                ->join('brand_models','purchases.brand_model_id','=','brand_models.id')
                ->join('categories','tickets.product_category_id','=','categories.id')
                ->select('jobs.id as job_id','jobs.job_number as job_number','brand_models.model_name as model_name','categories.name as modelName',
                'tickets.fault_description_id as faultDescriptionId','tickets.fault_description_note as faultDescription','tickets.created_at as ticket_date',
                'tickets.delivery_date_by_team_leader as deliveryDate','tickets.status as status','tickets.is_ended as ended','tickets.is_closed_by_teamleader as closedbyteamleader','tickets.is_pending as is_pending',
                'tickets.is_reopened as reopened','tickets.is_delivered_by_teamleader as deliveredby_teamleader','tickets.is_closed as ticket_closed','tickets.is_delivered_by_call_center as deliveredby_call_center',
                'tickets.is_started as started','tickets.is_paused as is_paused','tickets.is_accepted as accepted','tickets.is_assigned as assigned','tickets.is_rejected as rejected','tickets.id as ticket_id',
                'jobs.created_at as job_assigned_date','jobs.job_close_remark as repairDescription','jobs.is_consumed as is_consumed','purchases.purchase_date as purchase_date','employees.name as employee_name','outlets.name as outlet_name','users.name as created_by',
                'jobs.job_start_time as receivedDate','jobs.job_end_time as repairDate','jobs.job_pending_note as job_pending_note','jobs.job_ending_remark')
                ->where('tickets.product_category_id','=',$product_category)
                ->where('tickets.outlet_id','=',$branch)
                ->whereBetween('tickets.delivery_date_by_team_leader',[$startDate, $endDate])
                ->where('jobs.deleted_at',null)
                ->get();
            
            }
            else if(!empty($product_category) && !empty($request->start_date) && !empty($request->end_date))
            {
                $branch_name=DB::table('outlets')->where('id','=',$branch)->first();
                $jobs=DB::table('jobs')
                ->join('employees', 'jobs.employee_id', '=', 'employees.id')
                ->join('users', 'jobs.created_by', '=', 'users.id')
                ->join('tickets','tickets.id','=','jobs.ticket_id')
                ->join('outlets','tickets.outlet_id','=','outlets.id')
                ->join('purchases','tickets.purchase_id','=','purchases.id')
                ->join('brand_models','purchases.brand_model_id','=','brand_models.id')
                ->join('categories','tickets.product_category_id','=','categories.id')
                ->select('jobs.id as job_id','jobs.job_number as job_number','brand_models.model_name as model_name','categories.name as modelName',
                'tickets.fault_description_id as faultDescriptionId','tickets.fault_description_note as faultDescription','tickets.created_at as ticket_date',
                'tickets.delivery_date_by_team_leader as deliveryDate','tickets.status as status','tickets.is_ended as ended','tickets.is_closed_by_teamleader as closedbyteamleader','tickets.is_pending as is_pending',
                'tickets.is_reopened as reopened','tickets.is_delivered_by_teamleader as deliveredby_teamleader','tickets.is_closed as ticket_closed','tickets.is_delivered_by_call_center as deliveredby_call_center',
                'tickets.is_started as started','tickets.is_paused as is_paused','tickets.is_accepted as accepted','tickets.is_assigned as assigned','tickets.is_rejected as rejected','tickets.id as ticket_id',
                'jobs.created_at as job_assigned_date','jobs.job_close_remark as repairDescription','jobs.is_consumed as is_consumed','purchases.purchase_date as purchase_date','employees.name as employee_name','outlets.name as outlet_name','users.name as created_by',
                'jobs.job_start_time as receivedDate','jobs.job_end_time as repairDate','jobs.job_pending_note as job_pending_note','jobs.job_ending_remark')
                ->where('tickets.product_category_id','=',$product_category)
                ->whereBetween('tickets.delivery_date_by_team_leader',[$startDate, $endDate])
                ->where('jobs.deleted_at',null)
                ->get();
            }
            else if(!empty($branch) && !empty($request->start_date) && !empty($request->end_date))
            {
                $branch_name=DB::table('outlets')->where('id','=',$branch)->first();
                $jobs=DB::table('jobs')
                ->join('employees', 'jobs.employee_id', '=', 'employees.id')
                ->join('users', 'jobs.created_by', '=', 'users.id')
                ->join('tickets','tickets.id','=','jobs.ticket_id')
                ->join('outlets','tickets.outlet_id','=','outlets.id')
                ->join('purchases','tickets.purchase_id','=','purchases.id')
                ->join('brand_models','purchases.brand_model_id','=','brand_models.id')
                ->join('categories','tickets.product_category_id','=','categories.id')
                ->select('jobs.id as job_id','jobs.job_number as job_number','brand_models.model_name as model_name','categories.name as modelName',
                'tickets.fault_description_id as faultDescriptionId','tickets.fault_description_note as faultDescription','tickets.created_at as ticket_date',
                'tickets.delivery_date_by_team_leader as deliveryDate','tickets.status as status','tickets.is_ended as ended','tickets.is_closed_by_teamleader as closedbyteamleader','tickets.is_pending as is_pending',
                'tickets.is_reopened as reopened','tickets.is_delivered_by_teamleader as deliveredby_teamleader','tickets.is_closed as ticket_closed','tickets.is_delivered_by_call_center as deliveredby_call_center',
                'tickets.is_started as started','tickets.is_paused as is_paused','tickets.is_accepted as accepted','tickets.is_assigned as assigned','tickets.is_rejected as rejected','tickets.id as ticket_id',
                'jobs.created_at as job_assigned_date','jobs.job_close_remark as repairDescription','jobs.is_consumed as is_consumed','purchases.purchase_date as purchase_date','employees.name as employee_name','outlets.name as outlet_name','users.name as created_by',
                'jobs.job_start_time as receivedDate','jobs.job_end_time as repairDate','jobs.job_pending_note as job_pending_note','jobs.job_ending_remark')
                ->where('tickets.outlet_id','=',$branch)
                ->whereBetween('tickets.delivery_date_by_team_leader',[$startDate, $endDate])
                ->where('jobs.deleted_at',null)
                ->get();
            }
            else if(!empty($technician) && !empty($request->start_date) && !empty($request->end_date))
            {
                $technician_name=DB::table('employees')->where('id','=',$technician)->first();
                $jobs=DB::table('jobs')
                ->join('employees', 'jobs.employee_id', '=', 'employees.id')
                ->join('users', 'jobs.created_by', '=', 'users.id')
                ->join('tickets','tickets.id','=','jobs.ticket_id')
                ->join('outlets','tickets.outlet_id','=','outlets.id')
                ->join('purchases','tickets.purchase_id','=','purchases.id')
                ->join('brand_models','purchases.brand_model_id','=','brand_models.id')
                ->join('categories','tickets.product_category_id','=','categories.id')
                ->select('jobs.id as job_id','jobs.job_number as job_number','brand_models.model_name as model_name','categories.name as modelName',
                'tickets.fault_description_id as faultDescriptionId','tickets.fault_description_note as faultDescription','tickets.created_at as ticket_date',
                'tickets.delivery_date_by_team_leader as deliveryDate','tickets.status as status','tickets.is_ended as ended','tickets.is_closed_by_teamleader as closedbyteamleader','tickets.is_pending as is_pending',
                'tickets.is_reopened as reopened','tickets.is_delivered_by_teamleader as deliveredby_teamleader','tickets.is_closed as ticket_closed','tickets.is_delivered_by_call_center as deliveredby_call_center',
                'tickets.is_started as started','tickets.is_paused as is_paused','tickets.is_accepted as accepted','tickets.is_assigned as assigned','tickets.is_rejected as rejected','tickets.id as ticket_id',
                'jobs.created_at as job_assigned_date','jobs.job_close_remark as repairDescription','jobs.is_consumed as is_consumed','purchases.purchase_date as purchase_date','employees.name as employee_name','outlets.name as outlet_name','users.name as created_by',
                'jobs.job_start_time as receivedDate','jobs.job_end_time as repairDate','jobs.job_pending_note as job_pending_note','jobs.job_ending_remark')
                ->where('jobs.user_id','=',$technician)
                ->whereBetween('tickets.delivery_date_by_team_leader',[$startDate, $endDate])
                ->where('jobs.deleted_at',null)
                ->get();                
            }
            else if( !empty($request->start_date) && !empty($request->end_date) && $request->start_date == $request->end_date)
            {
                $jobs=DB::table('jobs')
                ->join('employees', 'jobs.employee_id', '=', 'employees.id')
                ->join('users', 'jobs.created_by', '=', 'users.id')
                ->join('tickets','tickets.id','=','jobs.ticket_id')
                ->join('outlets','tickets.outlet_id','=','outlets.id')
                ->join('purchases','tickets.purchase_id','=','purchases.id')
                ->join('brand_models','purchases.brand_model_id','=','brand_models.id')
                ->join('categories','tickets.product_category_id','=','categories.id')
                ->select('jobs.id as job_id','jobs.job_number as job_number','brand_models.model_name as model_name','categories.name as modelName',
                'tickets.fault_description_id as faultDescriptionId','tickets.fault_description_note as faultDescription','tickets.created_at as ticket_date',
                'tickets.delivery_date_by_team_leader as deliveryDate','tickets.status as status','tickets.is_ended as ended','tickets.is_closed_by_teamleader as closedbyteamleader','tickets.is_pending as is_pending',
                'tickets.is_reopened as reopened','tickets.is_delivered_by_teamleader as deliveredby_teamleader','tickets.is_closed as ticket_closed','tickets.is_delivered_by_call_center as deliveredby_call_center',
                'tickets.is_started as started','tickets.is_paused as is_paused','tickets.is_accepted as accepted','tickets.is_assigned as assigned','tickets.is_rejected as rejected','tickets.id as ticket_id',
                'jobs.created_at as job_assigned_date','jobs.job_close_remark as repairDescription','jobs.is_consumed as is_consumed','purchases.purchase_date as purchase_date','employees.name as employee_name','outlets.name as outlet_name','users.name as created_by',
                'jobs.job_start_time as receivedDate','jobs.job_end_time as repairDate','jobs.job_pending_note as job_pending_note','jobs.job_ending_remark')
                ->whereDate('tickets.delivery_date_by_team_leader',$startDate)
                ->where('jobs.deleted_at',null)
                ->get();            
            }
            else if( !empty($request->start_date) && !empty($request->end_date))
            {
                $jobs=DB::table('jobs')
                ->join('employees', 'jobs.employee_id', '=', 'employees.id')
                ->join('users', 'jobs.created_by', '=', 'users.id')
                ->join('tickets','tickets.id','=','jobs.ticket_id')
                ->join('outlets','tickets.outlet_id','=','outlets.id')
                ->join('purchases','tickets.purchase_id','=','purchases.id')
                ->join('brand_models','purchases.brand_model_id','=','brand_models.id')
                ->join('categories','tickets.product_category_id','=','categories.id')
                ->select('jobs.id as job_id','jobs.job_number as job_number','brand_models.model_name as model_name','categories.name as modelName',
                'tickets.fault_description_id as faultDescriptionId','tickets.fault_description_note as faultDescription','tickets.created_at as ticket_date',
                'tickets.delivery_date_by_team_leader as deliveryDate','tickets.status as status','tickets.is_ended as ended','tickets.is_closed_by_teamleader as closedbyteamleader','tickets.is_pending as is_pending',
                'tickets.is_reopened as reopened','tickets.is_delivered_by_teamleader as deliveredby_teamleader','tickets.is_closed as ticket_closed','tickets.is_delivered_by_call_center as deliveredby_call_center',
                'tickets.is_started as started','tickets.is_paused as is_paused','tickets.is_accepted as accepted','tickets.is_assigned as assigned','tickets.is_rejected as rejected','tickets.id as ticket_id',
                'jobs.created_at as job_assigned_date','jobs.job_close_remark as repairDescription','jobs.is_consumed as is_consumed','purchases.purchase_date as purchase_date','employees.name as employee_name','outlets.name as outlet_name','users.name as created_by',
                'jobs.job_start_time as receivedDate','jobs.job_end_time as repairDate','jobs.job_pending_note as job_pending_note','jobs.job_ending_remark')
                ->whereBetween('tickets.delivery_date_by_team_leader',[$startDate, $endDate])
                ->where('tickets.deleted_at',null)
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
                ->select('jobs.id as job_id','jobs.job_number as job_number','brand_models.model_name as model_name','categories.name as modelName',
                'tickets.fault_description_id as faultDescriptionId','tickets.fault_description_note as faultDescription','tickets.created_at as ticket_date',
                'tickets.delivery_date_by_team_leader as deliveryDate','tickets.status as status','tickets.is_ended as ended','tickets.is_closed_by_teamleader as closedbyteamleader','tickets.is_pending as is_pending',
                'tickets.is_reopened as reopened','tickets.is_delivered_by_teamleader as deliveredby_teamleader','tickets.is_closed as ticket_closed','tickets.is_delivered_by_call_center as deliveredby_call_center',
                'tickets.is_started as started','tickets.is_paused as is_paused','tickets.is_accepted as accepted','tickets.is_assigned as assigned','tickets.is_rejected as rejected','tickets.id as ticket_id',
                'jobs.created_at as job_assigned_date','jobs.job_close_remark as repairDescription','jobs.is_consumed as is_consumed',
                'jobs.job_start_time as receivedDate','jobs.job_end_time as repairDate','jobs.job_pending_note as job_pending_note','jobs.job_ending_remark')
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
                ->select('jobs.id as job_id','jobs.job_number as job_number','brand_models.model_name as model_name','categories.name as modelName',
                'tickets.fault_description_id as faultDescriptionId','tickets.fault_description_note as faultDescription','tickets.created_at as ticket_date',
                'tickets.delivery_date_by_team_leader as deliveryDate','tickets.status as status','tickets.is_ended as ended','tickets.is_closed_by_teamleader as closedbyteamleader','tickets.is_pending as is_pending',
                'tickets.is_reopened as reopened','tickets.is_delivered_by_teamleader as deliveredby_teamleader','tickets.is_closed as ticket_closed','tickets.is_delivered_by_call_center as deliveredby_call_center',
                'tickets.is_started as started','tickets.is_paused as is_paused','tickets.is_accepted as accepted','tickets.is_assigned as assigned','tickets.is_rejected as rejected','tickets.id as ticket_id',
                'jobs.created_at as job_assigned_date','jobs.job_close_remark as repairDescription','jobs.is_consumed as is_consumed',
                'jobs.job_start_time as receivedDate','jobs.job_end_time as repairDate','jobs.job_pending_note as job_pending_note','jobs.job_ending_remark')
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
                ->select('jobs.id as job_id','jobs.job_number as job_number','brand_models.model_name as model_name','categories.name as modelName',
                'tickets.fault_description_id as faultDescriptionId','tickets.fault_description_note as faultDescription','tickets.created_at as ticket_date',
                'tickets.delivery_date_by_team_leader as deliveryDate','tickets.status as status','tickets.is_ended as ended','tickets.is_closed_by_teamleader as closedbyteamleader','tickets.is_pending as is_pending',
                'tickets.is_reopened as reopened','tickets.is_delivered_by_teamleader as deliveredby_teamleader','tickets.is_closed as ticket_closed','tickets.is_delivered_by_call_center as deliveredby_call_center',
                'tickets.is_started as started','tickets.is_paused as is_paused','tickets.is_accepted as accepted','tickets.is_assigned as assigned','tickets.is_rejected as rejected','tickets.id as ticket_id',
                'jobs.created_at as job_assigned_date','jobs.job_close_remark as repairDescription','jobs.is_consumed as is_consumed',
                'jobs.job_start_time as receivedDate','jobs.job_end_time as repairDate','jobs.job_pending_note as job_pending_note','jobs.job_ending_remark')
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
                ->select('jobs.id as job_id','jobs.job_number as job_number','brand_models.model_name as model_name','categories.name as modelName',
                'tickets.fault_description_id as faultDescriptionId','tickets.fault_description_note as faultDescription','tickets.created_at as ticket_date',
                'tickets.delivery_date_by_team_leader as deliveryDate','tickets.status as status','tickets.is_ended as ended','tickets.is_closed_by_teamleader as closedbyteamleader','tickets.is_pending as is_pending',
                'tickets.is_reopened as reopened','tickets.is_delivered_by_teamleader as deliveredby_teamleader','tickets.is_closed as ticket_closed','tickets.is_delivered_by_call_center as deliveredby_call_center',
                'tickets.is_started as started','tickets.is_paused as is_paused','tickets.is_accepted as accepted','tickets.is_assigned as assigned','tickets.is_rejected as rejected','tickets.id as ticket_id',
                'jobs.created_at as job_assigned_date','jobs.job_close_remark as repairDescription','jobs.is_consumed as is_consumed',
                'jobs.job_start_time as receivedDate','jobs.job_end_time as repairDate','jobs.job_pending_note as job_pending_note','jobs.job_ending_remark')
                ->whereDate('tickets.delivery_date_by_team_leader',$formattedCurrentDate)
                ->where('jobs.deleted_at',null)
                ->get();
            }
            $jobinfo=[];
            foreach ($jobs as $key => $value) {

                $item['employee_name'] = $value->employee_name;
                $item['outlet_name'] = $value->outlet_name;
                $item['ticket_sl'] = $value->ticket_id;
                $item['created_at'] = Carbon::parse($value->ticket_date)->format('m/d/Y H:i:s');
                $item['purchase_date'] = Carbon::parse($value->purchase_date)->format('m/d/Y');
                $item['assigned_by'] = $value->created_by;
                $item['job_number'] = $value->job_id;
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
                $item['fault_description_note'] = $value->faultDescription;
                $item['job_ending_remark'] = $value->job_ending_remark;

                $item['repair_description'] = $value->repairDescription;
                
                $ticket_date=null;
                if (isset($value->ticket_date)) {
                    $ticket_date=Carbon::parse($value->ticket_date)->format('m/d/Y');
                }
                $item['ticket_date'] = $ticket_date;
                
                $job_assigned_date=null;

                if (isset($value->job_assigned_date)) {
                    $job_assigned_date=Carbon::parse($value->job_assigned_date)->format('m/d/Y');
                }
                $item['job_assigned_date'] = $job_assigned_date;

                $repair_date=null;
                if (isset($value->repairDate)) {
                    $repair_date=Carbon::parse($value->repairDate)->format('m/d/Y');
                }
                $item['repair_date'] = $repair_date;

                $delivery_date=null;
                if (isset($value->deliveryDate)) {
                    $delivery_date=Carbon::parse($value->deliveryDate)->format('m/d/Y');
                } 
                $item['delivery_date'] = $delivery_date;
                
                $item['yes_no'] = $value->is_consumed ? 'Yes' : 'No';
                
                $item['status'] = $value->status ?? null;
                $item['is_pending'] = $value->is_pending ?? null;
                $item['is_paused'] = $value->is_paused ?? null;
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
                
                $consumptions=InventoryStock::where('is_consumed',1)->where('job_id', $value->job_id)->where('stock_out', '>', 0)->get();
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
                
                $job_assigned_date=Carbon::parse($value->job_assigned_date);   

                $job_repair_date=null;  
                if (isset($value->repairDate)) {
                    $job_repair_date=Carbon::parse($value->repairDate);   
                }    
                
                $diff_in_days_repair_res=0;
                if (isset($job_repair_date)) {
                    $diff_in_days_repair = $job_repair_date->diffInDays($job_assigned_date);
                    if($diff_in_days_repair != 0){
                        $diff_in_days_repair_res= $diff_in_days_repair+1;
                        }
                }
                $ticket_delivery_date=null;  
                if (isset($value->deliveryDate)) {
                    $ticket_delivery_date=Carbon::parse($value->deliveryDate);   
                } 
                
                $diff_in_days_delivery_res=0;        
                if(isset($ticket_delivery_date)){
                    $diff_in_days_delivery = $ticket_delivery_date->diffInDays($job_assigned_date);
                    
                    if($diff_in_days_delivery != 0){
                    $diff_in_days_delivery_res= $diff_in_days_delivery+1;
                    }
                }
                

                $item['repair_tat'] = $diff_in_days_repair_res;
                $item['delivery_tat'] = $diff_in_days_delivery_res;
                $item['repeat_repair'] = '';
                $item['ltp'] = '';
                $customerFeedbacks= DB::table('customer_feedback')
                ->join('feedback_questions', 'feedback_questions.id', '=', 'customer_feedback.question_id')
                ->where('customer_feedback.ticket_id', $value->ticket_id)
                ->select('customer_feedback.question_feedback as question_feedback', 'feedback_questions.question as question')
                ->get();
                $cmi = '';
                $data = [];

                if(!empty($customerFeedbacks)){
                    $key=0;

                    foreach($customerFeedbacks as $key=>$value){
                        $ans='';
                        if ($value->question_feedback==0) {
                            $ans ='NA';
                        } elseif($value->question_feedback==1) {
                            $ans ='Average';
                        } elseif($value->question_feedback==2) {
                            $ans ='Good';
                        } elseif($value->question_feedback==3) {
                            $ans='Great';
                        }
                        
                        $data[]='#'.($key+1) .'. '.$value->question.': '.$ans;

                    }

                    foreach ($data as $key => $result) {
                        $total = count($data);
                        if ($total == 1) {
                            $cmi .= $result;
                        } else {
                            $cmi .= $result . '; ';
                        }
                    };
                }
                $item['cmi'] = $cmi;
                array_push($jobinfo, $item);
            }

            return view ('reports.kpi.kpi-report-filter', compact('outlets','technicians','jobinfo','product_category_name','branch_name','technician_name','formattedCurrentDate'));
        } catch (\Exception $e) {
            $bug = $e->getMessage();
            return redirect()->back()->with('error', $bug);
        }
    }
}
