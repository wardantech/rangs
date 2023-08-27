<?php

namespace App\Http\Controllers\Consumption;

use DB;
use Auth;
use App\Models\JobModel\Job;
use Illuminate\Http\Request;
use App\Models\Outlet\Outlet;
use App\Models\Inventory\Parts;
use App\Models\Inventory\Store;
use App\Models\Employee\Employee;
use App\Http\Controllers\Controller;
use App\Models\Requisition\Requisition;
use App\Models\Inventory\InventoryStock;
use App\Models\Inventory\PriceManagement;
use App\Models\Requisition\RequisitionDetails;
use App\Models\Ticket\Accessories;
use App\Models\Inventory\Fault;
use App\Models\Job\JobCloseRemark;
use App\Models\Job\JobPendingNote;
use App\Models\Job\JobPendingRemark;
use App\Models\Ticket\ServiceType;
use App\Models\Employee\TeamLeader;
use DataTables;
use Carbon\Carbon;

class ConsumptionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    //Part Consumption by job
    public function consumptionCreateByJob(Request $request, $id)
    {
        try {
            $job=Job::findOrFail($id);
            
            $consumptionsdetails=[];
            
            $mystore=Store::where('user_id',$job->user_id)->first();

            if ($mystore != null) {
                $inventoryStocksDetails=InventoryStock::where('store_id',$mystore->id)->where('is_consumed',1)->where('job_id',$id)->get();
                
                foreach ($inventoryStocksDetails as $key => $value) {
                    $item = [];
                    $price = PriceManagement::where(
                            'part_id', $value->part_id
                        )->latest('id')->first();
                    $item['id'] = $value->id;
                    $item['part_id'] = $value->part->id;
                    $item['type'] = $value->part->type;
                    $item['part_name'] = $value->part->name.'-'.$value->part->code;
                    $item['stock_out'] = $value->stock_out;
                    $item['price'] = floatval($price->selling_price_bdt);
                    if ($value->stock_out > 0) {
                        array_push($consumptionsdetails, $item);
                    }  
                }
            }

            $parts=Parts::where('status', 1)->orderBy('name')->get();

            $my_requisition=Requisition::where('job_id',$job->id)->latest()->first();
            if (empty($my_requisition)) {
                return redirect()->back()->with('error', __("Sorry you don't have raised any requisition for the job."));
            }
            // Requisitions Data
            $details = RequisitionDetails::where('requisition_id',$my_requisition->id)->with('part')->get();
            $stock_collect = [];
            foreach($details as $key=>$detail){
                $stock_in = InventoryStock::where('part_id',$detail->parts_id)->where('store_id',$my_requisition->from_store_id)->sum('stock_in');
                $stock_out = InventoryStock::where('part_id',$detail->parts_id)->where('store_id',$my_requisition->from_store_id)->sum('stock_out');
                $stock_in_hand = $stock_in - $stock_out;
                array_push($stock_collect,$stock_in_hand);
            }
            return view('employee.consumption.create',compact('parts','job','mystore','details','stock_collect','consumptionsdetails'));
        } catch (\Exception $e) {
            $bug = $e->getMessage();
            return redirect()->back()->with('error', $bug);
        }
    }

    public function getPartsStock(Request $request)
    {
            $part_id = $request->parts_id;
            $store_id=$request->from_store_id;
            $part_id_array = [];
            $model_id_array = [];
    
            $stock_collect = [];
            $partInfo_collect = [];
            foreach($part_id as $key=>$pr_id){
                $stock_in = InventoryStock::where('part_id',$pr_id)->where('store_id',$store_id)->sum('stock_in');
                $stock_out = InventoryStock::where('part_id',$pr_id)->where('store_id',$store_id)->sum('stock_out');
    
                $partsInfo=Parts::where('id', $pr_id)->first();
                $stock_in_hand = $stock_in - $stock_out;
                array_push($stock_collect,$stock_in_hand);
                array_push($partInfo_collect,$partsInfo);
            }
            $html = view('employee.consumption.technician_parts_info', compact('partInfo_collect','stock_collect'))->render();
            return response()->json(compact('html'));
    }

    public function consumptionStoreByJob(Request $request)
    {
        $this->validate($request, [
            'job_id' => 'required',
            'date' => 'required',
            'from_store_id' => 'required',
        ]);
        DB::beginTransaction();
        try {
            $job = Job::findOrFail($request->job_id);
            $job->update([
                'is_consumed'=> 1,
            ]);

            foreach ($request->required_quantity as $key => $value) {

                $InventoryStock=InventoryStock::where('store_id',$request->from_store_id)->where('is_consumed',1)->where('job_id', $request->job_id)->where('part_id',$request->part_id[$key])->first();
           
                if ($InventoryStock) {
                    $total=$InventoryStock->stock_out + $request->required_quantity[$key];
                    $InventoryStock->update([
                        'stock_out' => $total,
                        'updated_by' => Auth::id(),
                    ]);
                }else{
                    InventoryStock::create([
                        'store_id' => $request->from_store_id,
                        'date' => $request->date,
                        'job_id' => $request->job_id,
                        'part_id' => $request->part_id[$key],
                        'stock_out' => $request->required_quantity[$key],
                        'belong_to' => 3,
                        'type' => 2,
                        'is_consumed' => 1,
                        'created_by' => Auth::id(),
                    ]); 
                }
            }
            DB::commit();
            return redirect()
            ->route('technician.jobs.show',$request->job_id)
            ->with('success', 'Part consumed for this job successfully');
        } catch (\Exception $e) {
            DB::rollback();
            $bug = $e->getMessage();
            return redirect()->back()->with('error', $bug);
        }

    }
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $consumption=InventoryStock::findOrFail($id);
        
        $stock_in = InventoryStock::where('part_id',$consumption->part_id)->where('store_id',$consumption->store_id)->sum('stock_in');
        $stock_out = InventoryStock::where('part_id',$consumption->part_id)->where('store_id',$consumption->store_id)->sum('stock_out');
        $stock_in_hand = $stock_in - $stock_out;

        return view('employee.consumption.edit',compact('consumption','stock_in_hand'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        try {
            $consumption=InventoryStock::findOrFail($id);
            if ($consumption) {            
                $consumption->update([
                    'stock_out' => $request->required_quantity,
                    'updated_by' => Auth::id(),
                ]);
            }else{
                return redirect()->back()->with('error', __("Sorry you don't have any part consumption for the job.")); 
            }          
            return redirect()
            ->route('technician.jobs.show',$consumption->job_id)
            ->with('success', 'Consuption updated for the job successfully');
        } catch (\Exception $e) {
            $bug = $e->getMessage();
            return redirect()->back()->with('error', $bug);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
    public function withdraw($id)
    {
        $auth=Auth::user()->roles->first();
        $job = Job::findOrFail($id);
        $allAccessories=Accessories::where('status', 1)->get();
        $allFaults=Fault::where('status', 1)->get();
        $jobCloseRemarks = JobCloseRemark::orderBy('id', 'DESC')->get();
        $jobpendingRemarks = JobPendingRemark::orderBy('id', 'DESC')->get();
        $consumption=InventoryStock::where('is_consumed',1)->where('job_id', $id)->get();
        return view('employee.consumption.withdraw_show',compact('job','consumption','allAccessories','allFaults','jobCloseRemarks','jobpendingRemarks','auth'));
    }
    public function withdrawRequest(Request $request, $id){
        if ($request->ajax()) {
            $job = Job::findOrFail($id);
            $job->withdraw_request = $job->withdraw_request == 0 ? 1 : 0;
            $job->update();

            if ($job->withdraw_request == 1) {
                return response()->json([
                    'data' => $job,
                    'success' => true,
                    'message' => 'Request Sent',
                ]);
            } else {
                return response()->json([
                    'data' => $job,
                    'success' => false,
                    'message' => 'Failed',
                ]);
            }
        }
    }
    public function withdrawRequestStore(Request $request)
    {
        dd($request->all());
        // if ($request->ajax()) {
        //     $job = Job::findOrFail($id);
        //     $consumption=InventoryStock::where('is_consumed',1)->where('job_id', $id)->get();
        //     foreach ($consumption as $key => $value) {
        //         $consumption->update([
        //             'withdraw_qnty'=>$value->stock_out,
        //             'stock_out'=>0
        //         ]);
        //     };
        //     $job->withdraw_request = $job->withdraw_request == 1 ? 2 : 1;
        //     $job->update();
        //     if ($job->withdraw_request==2) {
        //         return response()->json([
        //             'data' => $job,
        //             'success' => true,
        //             'message' => 'Request Approved Successfully',
        //         ]);
        //     } else {
        //         return response()->json([
        //             'data' => $job,
        //             'success' => false,
        //             'message' => 'Failed',
        //         ]);
        //     }
        // }
    }
    public function withdrawRequestList()
    {
        // $job = Job::where('withdraw_request',1)->latest()->get();
        // dd($job);
        try{
            $auth = Auth::user();
            $user_role = $auth->roles->first();
            $employee = Employee::where('user_id', Auth::user()->id)->first();
            $serviceTypes = ServiceType::where('status', 1)->get();
            $data=DB::table('jobs')
            ->join('employees', 'jobs.employee_id', '=', 'employees.id')
            ->join('users', 'jobs.created_by', '=', 'users.id')
            ->join('tickets', 'jobs.ticket_id', '=', 'tickets.id')
            ->join('job_priorities', 'tickets.job_priority_id', '=', 'job_priorities.id')
            ->join('outlets','tickets.outlet_id','=','outlets.id')
            ->join('purchases','tickets.purchase_id','=','purchases.id')
            ->join('categories','tickets.product_category_id','=','categories.id')
            ->join('brand_models','purchases.brand_model_id', '=', 'brand_models.id')
            ->join('brands','purchases.brand_id', '=', 'brands.id')
            ->join('customers','purchases.customer_id', '=', 'customers.id')
            ->leftjoin('warranty_types','tickets.warranty_type_id', '=', 'warranty_types.id')
            ->select('jobs.id as job_id','jobs.job_number as job_number','jobs.date as assigning_date','jobs.created_at as job_created_at','employees.name as employee_name','employees.vendor_id as vendor_id','brand_models.model_name as model_name','brands.name as brand_name',
            'categories.name as product_category','users.name as created_by','customers.name as customer_name', 'customers.mobile as customer_mobile','purchases.product_serial as product_serial','purchases.invoice_number as invoice_number','purchases.purchase_date as purchase_date',
            'tickets.id as ticket_id','tickets.created_at as created_at','outlets.name as outlet_name','tickets.service_type_id as service_type_id','tickets.status as ticket_status',
            'tickets.is_reopened as is_reopened','tickets.is_accepted as is_accepted','tickets.is_pending as ticket_is_pending','tickets.is_paused as ticket_is_paused','tickets.is_ended as ticket_is_ended',
            'tickets.is_started as ticket_is_started','tickets.is_closed_by_teamleader as is_closed_by_teamleader','tickets.is_delivered_by_teamleader as is_delivered_by_teamleader',
            'tickets.is_delivered_by_call_center as is_delivered_by_call_center','tickets.is_closed as is_closed','tickets.is_assigned as is_assigned',
            'tickets.is_rejected as is_rejected','jobs.status as status','jobs.is_pending as is_pending','jobs.is_paused as is_paused','jobs.is_started as is_started','jobs.is_ended as is_ended','job_priorities.job_priority','tickets.outlet_id as outlet_id',
            'warranty_types.warranty_type as warranty_type','purchases.outlet_id as outletid')
            ->where('jobs.withdraw_request',1)
            ->where('jobs.deleted_at',null);

            if ($user_role->name == 'Super Admin' || $user_role->name == 'Admin' || $user_role->name =='Team Leader Admin') {
                $data;
            } elseif ($user_role->name == 'Team Leader') {
                $data->where('jobs.created_by',Auth::user()->id);
            } else {
                $data->where('jobs.user_id',Auth::user()->id);
            }
            // $jobs=$data->orderBy('jobs.id', 'desc');
            $jobs=$data->latest()->get();

            if (request()->ajax()) {
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
                        return '<a href="'.route('show-ticket-details', $jobs->ticket_id).'" class="badge badge-primary" title="Ticket Details">'.'TSL-'.''. $jobs->ticket_id.'</a>';
                    })
                    
                    ->addColumn('ticket_created_at', function ($jobs) {
                        $ticket_created_at=Carbon::parse($jobs->created_at)->format('m/d/Y');  
                         
                        return $ticket_created_at;
                    })
                    ->addColumn('purchase_date', function ($jobs) {
                        $purchase_date=Carbon::parse($jobs->purchase_date)->format('m/d/Y');                        
                        return $purchase_date;
                    })
                    ->addColumn('job_number', function ($jobs) {
                        $job_number='JSL-'.$jobs->job_id; 
                        return $job_number;
                    })
                    ->addColumn('service_type', function($jobs) use($serviceTypes){
                        $selectedServiceTypeIds=json_decode($jobs->service_type_id);
                        $data='';
                        foreach ($serviceTypes as $key => $serviceType) {
                           if (in_array($serviceType->id, $selectedServiceTypeIds)) {
                               $data=$serviceType->service_type;
                           }
                        }
                        return $data;
                   })
                   ->addColumn('warranty_type', function ($jobs) {
                        $warranty_type=$jobs->warranty_type ?? null; 
                        return $warranty_type;
                    })
                    ->addColumn('assigning_date', function ($jobs) {
                        $assigning_date=Carbon::parse($jobs->assigning_date)->format('m/d/Y');    
                        return $assigning_date;
                    })
                    ->addColumn('created_by', function ($jobs) {
                        $created_by=$jobs->created_by; 
                        return $created_by;
                    })
                    ->addColumn('product_category', function ($jobs) {
                        $product_category=$jobs->product_category ?? Null;
                        return $product_category;
                    })
                    ->addColumn('brand_name', function ($jobs) {
                        $brand_name=$jobs->brand_name ?? Null;
                        return $brand_name;
                    })
                    ->addColumn('model_name', function ($jobs) {
                        $model_name=$jobs->model_name ?? Null;
                        return $model_name;
                    })
                    ->addColumn('product_serial', function ($jobs) {
                        $product_serial=$jobs->product_serial ?? Null;
                        return $product_serial;
                    })
                    ->addColumn('point_of_purchase', function($tickets){
                        $point_of_purchase=Outlet::where('id', '=', $tickets->outletid)->first();
                            return $point_of_purchase->name ?? null;
                    })
                    ->addColumn('invoice_number', function ($jobs) {
                        $invoice_number=$jobs->invoice_number;
                        return $invoice_number;
                    })
                    ->addColumn('customer_name', function ($jobs) {
                        $invoice_number=$jobs->customer_name;
                        return $invoice_number;
                    })
                    ->addColumn('customer_mobile', function ($jobs) {
                        $invoice_number=$jobs->customer_mobile;
                        return $invoice_number;
                    })
                    ->addColumn('technician_type', function ($jobs) {
                        $tech_type='';
                        if ($jobs->vendor_id != null) {
                            $tech_type='Vendor';
                        }else{
                            $tech_type='Own';
                        }
                        return $tech_type;
                    })
                    ->addColumn('job_priority', function($jobs){
                        $job_priority=$jobs->job_priority?? Null;
                        return $job_priority;
                    })
                    ->addColumn('status', function ($jobs) {

                        if ($jobs->status == 6){
                            return '<span class="badge badge-red">Paused</span>';
                        }
                        
                        elseif( $jobs->status == 5 ){
                            return '<span class="badge badge-orange">Pending</span>';
                        }

                        
                        elseif($jobs->status == 0)
                        {
                            return '<span class="badge badge-yellow">Created</span>';
                        }

                        elseif($jobs->status == 4 )
                        {
                            return '<span class="badge badge-info">Job Completed</span>';
                        }

                        elseif($jobs->status == 3 )
                        {
                            return '<span class="badge badge-success">Job Started</span>';
                        }
                        elseif($jobs->status == 1)
                        {
                            return '<span class="badge badge-success">Accepted</span>';
                        }
                        elseif($jobs->status==2)
                        {
                            return '<span class="badge badge-danger">Rejected</span>';
                        }
                        
                    })
                    ->addColumn('job_created_at', function ($jobs) {
                        $job_created_at=Carbon::parse($jobs->job_created_at)->format('m/d/Y');
                        return $job_created_at;
                    })

                    ->addColumn('job_pending_remark', function ($jobs) {
                        $data=null;
                        $pendingNotes=DB::table('job_pending_notes')->where('job_id',$jobs->job_id)->get();
                        
                        foreach ($pendingNotes as $key => $item) {
                            $data.= '<ol style="font-weight: bold; color:red">'. $item->job_pending_remark.'-'.$item->job_pending_note.'</ol>';
                        }
                        return $data;
                    })

                    ->addColumn('action', function ($jobs) {
                            if (Auth::user()->can('show')) {
                                return '<div class="table-actions text-center" style="display: flex;">
                                            <a href=" '.route('technician.withdraw', $jobs->job_id). ' " title="View">
                                                <i class="ik ik-eye f-16 mr-15 text-blue"></i>
                                                </a>
                                        </div>';
                            }
                    })
                    ->addIndexColumn()
                    ->rawColumns(['ticket_sl','job_number','service_type','warranty_type','status','job_pending_remark','action'])
                    ->make(true);
            }
            // dd('dd');
            return view('employee.consumption.withdraw_list', compact('jobs'));
        } catch (\Exception $e) {
            $bug = $e->getMessage();
            return redirect()->back()->with('error', $bug);
        }
    }
}
