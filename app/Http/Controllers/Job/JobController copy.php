<?php

namespace App\Http\Controllers\Job;

use Session;
use Redirect;
use Validator;
use DataTables;
use Carbon\Carbon;
use App\Models\Job\JobNote;
use App\Models\JobModel\Job;
use Illuminate\Http\Request;
use App\Models\Ticket\Ticket;
use App\Models\Inventory\Fault;
use App\Models\Employee\Employee;
use App\Models\Job\JobAttachment;
use App\Models\Job\JobSubmission;
use App\Models\Job\JobCloseRemark;
use App\Models\Job\JobPendingNote;
use App\Models\Ticket\Accessories;
use Illuminate\Support\Facades\DB;
use App\Models\Employee\TeamLeader;
use App\Http\Controllers\Controller;
use App\Models\Job\JobPendingRemark;
use Illuminate\Support\Facades\Auth;
use App\Models\ProductPurchase\Purchase;
use App\Models\Job\CustomerAdvancedPayment;

class JobController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try{
            $auth = Auth::user();
            $user_role = $auth->roles->first();
            $employee=Employee::where('user_id', Auth::user()->id)->first();
            if ($user_role->name == 'Super Admin' || $user_role->name == 'Admin' || $user_role->name =='Team Leader Admin') {
                $jobs=DB::table('jobs')
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
                ->select('jobs.id as job_id','jobs.job_number as job_number','jobs.date as assigning_date','jobs.created_at as job_created_at','employees.name as employee_name','brand_models.model_name as model_name','brands.name as brand_name',
                'categories.name as product_category','users.name as created_by','customers.name as customer_name', 'customers.mobile as customer_mobile','purchases.product_serial as product_serial',
                'tickets.id as ticket_id','tickets.created_at as created_at','outlets.name as outlet_name','tickets.service_type_id as service_type_id','tickets.status as status',
                'tickets.is_reopened as is_reopened','tickets.is_accepted as is_accepted','tickets.is_pending as is_pending','tickets.is_paused as is_paused','tickets.is_ended as is_ended',
                'tickets.is_started as is_started','tickets.is_closed_by_teamleader as is_closed_by_teamleader','tickets.is_delivered_by_teamleader as is_delivered_by_teamleader',
                'tickets.is_delivered_by_call_center as is_delivered_by_call_center','tickets.is_closed as is_closed','tickets.is_assigned as is_assigned','tickets.is_accepted as is_accepted',
                'tickets.is_rejected as is_rejected','jobs.status as status','jobs.is_pending as is_pending','jobs.is_paused as is_paused','jobs.is_started as is_started','jobs.is_ended as is_ended','job_priorities.job_priority')
                ->where('jobs.deleted_at',null)
                ->latest()
                ->get();
                $totalJobStatus = $this->jobTotalstatus();
            } elseif ($user_role->name == 'Team Leader') {
                $jobs=DB::table('jobs')
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
                ->select('jobs.id as job_id','jobs.job_number as job_number','jobs.date as assigning_date','jobs.created_at as job_created_at','employees.name as employee_name','brand_models.model_name as model_name','brands.name as brand_name',
                'categories.name as product_category','users.name as created_by','customers.name as customer_name', 'customers.mobile as customer_mobile','purchases.product_serial as product_serial',
                'tickets.id as ticket_id','tickets.created_at as created_at','outlets.name as outlet_name','tickets.service_type_id as service_type_id','tickets.status as status',
                'tickets.is_reopened as is_reopened','tickets.is_accepted as is_accepted','tickets.is_pending as is_pending','tickets.is_paused as is_paused','tickets.is_ended as is_ended',
                'tickets.is_started as is_started','tickets.is_closed_by_teamleader as is_closed_by_teamleader','tickets.is_delivered_by_teamleader as is_delivered_by_teamleader',
                'tickets.is_delivered_by_call_center as is_delivered_by_call_center','tickets.is_closed as is_closed','tickets.is_assigned as is_assigned','tickets.is_accepted as is_accepted',
                'tickets.is_rejected as is_rejected','jobs.status as status','jobs.is_pending as is_pending','jobs.is_paused as is_paused','jobs.is_started as is_started','jobs.is_ended as is_ended','job_priorities.job_priority')
                ->where('jobs.created_by',Auth::user()->id)
                ->where('jobs.deleted_at',null)
                ->latest()
                ->get();
                $totalJobStatus = $this->jobTotalStatusByTeam($auth->id);
            } else {
                $jobs=DB::table('jobs')
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
                ->select('jobs.id as job_id','jobs.job_number as job_number','jobs.date as assigning_date','jobs.created_at as job_created_at','employees.name as employee_name','brand_models.model_name as model_name','brands.name as brand_name',
                'categories.name as product_category','users.name as created_by','customers.name as customer_name', 'customers.mobile as customer_mobile','purchases.product_serial as product_serial',
                'tickets.id as ticket_id','tickets.created_at as created_at','outlets.name as outlet_name','tickets.service_type_id as service_type_id','tickets.status as status',
                'tickets.is_reopened as is_reopened','tickets.is_accepted as is_accepted','tickets.is_pending as is_pending','tickets.is_paused as is_paused','tickets.is_ended as is_ended',
                'tickets.is_started as is_started','tickets.is_closed_by_teamleader as is_closed_by_teamleader','tickets.is_delivered_by_teamleader as is_delivered_by_teamleader',
                'tickets.is_delivered_by_call_center as is_delivered_by_call_center','tickets.is_closed as is_closed','tickets.is_assigned as is_assigned','tickets.is_accepted as is_accepted',
                'tickets.is_rejected as is_rejected','jobs.status as status','jobs.is_pending as is_pending','jobs.is_paused as is_paused','jobs.is_started as is_started','jobs.is_ended as is_ended','job_priorities.job_priority')
                ->where('tickets.outlet_id', $employee->outlet_id)
                ->where('jobs.deleted_at',null)
                ->latest()
                ->get();
                $totalJobStatus = $this->jobTotalstatus();
            }
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
                        // $ticket_created_at=0;
                            
                        return $ticket_created_at;
                    })
                    ->addColumn('job_number', function ($jobs) {
                        $job_number=$jobs->job_number; 
                        return $job_number;
                    })
                    
                    ->addColumn('assigning_date', function ($jobs) {
                        $assigning_date=$jobs->assigning_date; 
                        return $assigning_date;
                    })
                    ->addColumn('created_by', function ($jobs) {
                        $created_by=$jobs->created_by; 
                        return $created_by;
                    })
                    ->addColumn('job_priority', function($jobs){
                        $job_priority=$jobs->job_priority?? Null;
                        return $job_priority;
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

                    ->addColumn('status', function ($jobs) {

                        if ($jobs->status == 1 && $jobs->is_pending == 1 && $jobs->is_paused==1){
                            return '<span class="badge badge-lime">Paused</span>';
                        }
                        
                        elseif( $jobs->status==1 && $jobs->is_pending == 1 && $jobs->is_ended !=1){
                            return '<span class="badge badge-orange">Pending</span>';
                        }

                        
                        elseif($jobs->status==0)
                        {
                            return '<span class="badge badge-yellow">Created</span>';
                        }

                        elseif($jobs->status==1 && $jobs->is_started==1 && $jobs->is_ended==1)
                        {
                            return '<span class="badge badge-info">Job Completed</span>';
                        }

                        elseif($jobs->status==1 && $jobs->is_started==1)
                        {
                            return '<span class="badge badge-success">Job Started</span>';
                        }
                        elseif($jobs->status==1)
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
                            if (Auth::user()->can('edit') && Auth::user()->can('delete') && Auth::user()->can('show')) {
                                return '<div class="table-actions text-center" style="display: flex;">
                                            <a href=" '.route('job.job.show', $jobs->job_id). ' " title="View">
                                                <i class="ik ik-eye f-16 mr-15 text-green"></i>
                                            </a>
                                            <a href=" '.route('job.job.edit', $jobs->job_id). ' " title="View">
                                                <i class="ik ik-edit f-16 mr-15 text-green" title="Edit"></i>
                                            </a>
                                            <a type="submit" onclick="showDeleteConfirm(' . $jobs->job_id . ')" title="Delete"><i class="ik ik-trash-2 f-16 text-red"></i></a>
                                        </div>';
                            } elseif (Auth::user()->can('edit') && Auth::user()->can('show')) {
                                return '<div class="table-actions" style="display: flex;">
                                                <a href=" '.route('job.job.show', $jobs->job_id). ' " title="View">
                                                    <i class="ik ik-eye f-16 mr-15 text-green"></i>
                                                </a>
                                                <a href=" '.route('job.job.edit', $jobs->job_id). ' " title="View">
                                                    <i class="ik ik-edit f-16 mr-15 text-green" title="Edit"></i>
                                                </a>
                                                </div>';
                            } elseif (Auth::user()->can('delete')) {
                                return '<div class="table-actions">
                                            <a type="submit" onclick="showDeleteConfirm(' . $jobs->job_id . ')" title="Delete"><i class="ik ik-trash-2 f-16 text-red"></i></a>
                                        </div>';
                            } elseif (Auth::user()->can('show')) {
                                return '<div class="table-actions">
                                            <a href=" '.route('job.job.show', $jobs->job_id). ' " title="View">
                                                <i class="ik ik-eye f-16 mr-15 text-green"></i>
                                            </a>
                                        </div>';
                            } 
                    })
                    ->addIndexColumn()
                    ->rawColumns(['ticket_sl','status','job_pending_remark','action'])
                    ->make(true);
            }
            return view('job.index', compact('jobs', 'totalJobStatus'));
        } catch (\Exception $e) {
            $bug = $e->getMessage();
            return redirect()->back()->with('error', $bug);
        }
    }

    //Technician's Job
    public function employeeJobs()
    {
        try{
            $auth = Auth::user();
            $user_role = $auth->roles->first();
            $employee = Employee::where('user_id', Auth::user()->id)->first();
            if ($user_role->name == 'Super Admin' || $user_role->name == 'Admin' || $user_role->name =='Team Leader Admin') {
                $jobs=DB::table('jobs')
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
                ->select('jobs.id as job_id','jobs.job_number as job_number','jobs.date as assigning_date','jobs.created_at as job_created_at','employees.name as employee_name','brand_models.model_name as model_name','brands.name as brand_name',
                'categories.name as product_category','users.name as created_by','customers.name as customer_name', 'customers.mobile as customer_mobile','purchases.product_serial as product_serial',
                'tickets.id as ticket_id','tickets.created_at as created_at','outlets.name as outlet_name','tickets.service_type_id as service_type_id','tickets.status as status',
                'tickets.is_reopened as is_reopened','tickets.is_accepted as is_accepted','tickets.is_pending as is_pending','tickets.is_paused as is_paused','tickets.is_ended as is_ended',
                'tickets.is_started as is_started','tickets.is_closed_by_teamleader as is_closed_by_teamleader','tickets.is_delivered_by_teamleader as is_delivered_by_teamleader',
                'tickets.is_delivered_by_call_center as is_delivered_by_call_center','tickets.is_closed as is_closed','tickets.is_assigned as is_assigned','tickets.is_accepted as is_accepted',
                'tickets.is_rejected as is_rejected','jobs.status as status','jobs.is_pending as is_pending','jobs.is_paused as is_paused','jobs.is_started as is_started','jobs.is_ended as is_ended','job_priorities.job_priority')
                ->where('jobs.deleted_at',null)
                ->latest()
                ->get();
                $totalJobStatus = $this->jobTotalstatus();
            } elseif ($user_role->name == 'Team Leader') {
                $jobs=DB::table('jobs')
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
                ->select('jobs.id as job_id','jobs.job_number as job_number','jobs.date as assigning_date','jobs.created_at as job_created_at','employees.name as employee_name','brand_models.model_name as model_name','brands.name as brand_name',
                'categories.name as product_category','users.name as created_by','customers.name as customer_name', 'customers.mobile as customer_mobile','purchases.product_serial as product_serial',
                'tickets.id as ticket_id','tickets.created_at as created_at','outlets.name as outlet_name','tickets.service_type_id as service_type_id','tickets.status as status',
                'tickets.is_reopened as is_reopened','tickets.is_accepted as is_accepted','tickets.is_pending as is_pending','tickets.is_paused as is_paused','tickets.is_ended as is_ended',
                'tickets.is_started as is_started','tickets.is_closed_by_teamleader as is_closed_by_teamleader','tickets.is_delivered_by_teamleader as is_delivered_by_teamleader',
                'tickets.is_delivered_by_call_center as is_delivered_by_call_center','tickets.is_closed as is_closed','tickets.is_assigned as is_assigned','tickets.is_accepted as is_accepted',
                'tickets.is_rejected as is_rejected','jobs.status as status','jobs.is_pending as is_pending','jobs.is_paused as is_paused','jobs.is_started as is_started','jobs.is_ended as is_ended','job_priorities.job_priority')
                ->where('jobs.created_by',Auth::user()->id)
                ->where('jobs.deleted_at',null)
                ->latest()
                ->get();
                $totalJobStatus = $this->jobTotalStatusByTeam($auth->id);
            } else {
                $jobs=DB::table('jobs')
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
                ->select('jobs.id as job_id','jobs.job_number as job_number','jobs.date as assigning_date','jobs.created_at as job_created_at','employees.name as employee_name','brand_models.model_name as model_name','brands.name as brand_name',
                'categories.name as product_category','users.name as created_by','customers.name as customer_name', 'customers.mobile as customer_mobile','purchases.product_serial as product_serial',
                'tickets.id as ticket_id','tickets.created_at as created_at','outlets.name as outlet_name','tickets.service_type_id as service_type_id','tickets.status as status',
                'tickets.is_reopened as is_reopened','tickets.is_accepted as is_accepted','tickets.is_pending as is_pending','tickets.is_paused as is_paused','tickets.is_ended as is_ended',
                'tickets.is_started as is_started','tickets.is_closed_by_teamleader as is_closed_by_teamleader','tickets.is_delivered_by_teamleader as is_delivered_by_teamleader',
                'tickets.is_delivered_by_call_center as is_delivered_by_call_center','tickets.is_closed as is_closed','tickets.is_assigned as is_assigned','tickets.is_accepted as is_accepted',
                'tickets.is_rejected as is_rejected','jobs.status as status','jobs.is_pending as is_pending','jobs.is_paused as is_paused','jobs.is_started as is_started','jobs.is_ended as is_ended','job_priorities.job_priority')
                ->where('jobs.user_id',Auth::user()->id)
                ->where('jobs.deleted_at',null)
                ->latest()
                ->get();
                $totalJobStatus = $this->jobTotalStatusByUser(Auth::user()->id);
            }
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
                        // $ticket_created_at=0;
                            
                        return $ticket_created_at;
                    })
                    ->addColumn('job_number', function ($jobs) {
                        $job_number=$jobs->job_number; 
                        return $job_number;
                    })
                    
                    ->addColumn('assigning_date', function ($jobs) {
                        $assigning_date=$jobs->assigning_date; 
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
                    ->addColumn('job_priority', function($jobs){
                        $job_priority=$jobs->job_priority?? Null;
                        return $job_priority;
                    })

                    ->addColumn('status', function ($jobs) {

                        if ($jobs->status == 1 && $jobs->is_pending == 1 && $jobs->is_paused==1){
                            return '<span class="badge badge-red">Paused</span>';
                        }
                        
                        elseif( $jobs->status==1 && $jobs->is_pending == 1 && $jobs->is_ended !=1){
                            return '<span class="badge badge-orange">Pending</span>';
                        }

                        
                        elseif($jobs->status==0)
                        {
                            return '<span class="badge badge-yellow">Created</span>';
                        }

                        elseif($jobs->status==1 && $jobs->is_started==1 && $jobs->is_ended==1)
                        {
                            return '<span class="badge badge-info">Job Completed</span>';
                        }

                        elseif($jobs->status==1 && $jobs->is_started==1)
                        {
                            return '<span class="badge badge-success">Job Started</span>';
                        }
                        elseif($jobs->status==1)
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
                                            <a href=" '.route('technician.jobs.show', $jobs->job_id). ' " title="View">
                                                <i class="ik ik-eye f-16 mr-15 text-green"></i>
                                                </a>
                                        </div>';
                            }
                    })
                    ->addIndexColumn()
                    ->rawColumns(['ticket_sl','status','job_pending_remark','action'])
                    ->make(true);
            }
            return view('job.technician-index', compact('jobs','totalJobStatus'));
        } catch (\Exception $e) {
            $bug = $e->getMessage();
            return redirect()->back()->with('error', $bug);
        }
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        dd('create');
    }

    public function job_create(Request $request, $id)
    {
        try{
            $auth = Auth::user();
            $user_role = $auth->roles->first();
            if ($user_role->name == 'Super Admin' || $user_role->name == 'Admin') {
                $employees=Employee::whereNotNull('team_leader_id')->latest()->get();
            } else {
                $teamleader=TeamLeader::where('user_id',Auth::user()->id)->first();
                if ($teamleader != null) {
                    $employees=Employee::where('team_leader_id',$teamleader->id)->latest()->get();
                }else{
                    return redirect()->back()->with('error', __('Sorry you dont have the permission.'));
                }
            }

            $job_list=Job::latest()->first();
            if(!empty($job_list)){
                $trim=trim($job_list->job_number,"JSL-");
                $sl=$trim + 1;
                $job_number="JSL-".$sl;
            }else{
                $job_number="JSL-"."1";
            }
            $ticket=Ticket::where('id',$id)->first();
            return view('job.create', compact('ticket','job_number','employees'));
        } catch (\Exception $e) {
            $bug = $e->getMessage();
            return redirect()->back()->with('error', $bug);
        }
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'purchase_id' => 'required',
            'ticket_id' => 'required',
            'date' => 'required',
            // 'job_number' => 'required|unique:jobs,job_number,NULL,id,deleted_at,NULL',
            'employee_id' => 'required',
        ]);

        DB::beginTransaction();

        try{

            $job_number = $this->generateUniqueJobSl();
            $ticket = Ticket::findOrFail($request->ticket_id);
            $employee=Employee::where('id',$request->employee_id)->first();

            Job::create([
                'purchase_id' =>  $request->purchase_id,
                'employee_id' =>  $request->employee_id,
                'user_id' =>  $employee->user_id,
                'outlet_id' =>  $employee->outlet_id,
                'date' =>  $request->date,
                'ticket_id' =>  $request->ticket_id,
                'is_ticket_reopened_job' =>  $ticket->is_reopened ? $ticket->is_reopened : 0,
                'job_number' =>  $job_number,
                'note' =>  $request->note,
                'created_by' => Auth::id(),
            ]);

            if ( $ticket->is_rejected == 1) {
                $ticket->update([
                    'status' => 1,
                    'is_rejected' => 0,
                    'is_re_assigned' => 1,
                ]);
            }else{
                $ticket->update([
                    'status' => 1,
                    'is_assigned' => 1,
                ]); 
            }

            DB::commit();
            return redirect('job/job')
            ->with('success', __('label.NEW_JOB_CREATED'));
        } catch (\Exception $e) {
            DB::rollback();
            $bug = $e->getMessage();
            return redirect()->back()->with('error', $bug);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try{
            $job=Job::findOrFail($id);
            $customerAdvancedPayment= CustomerAdvancedPayment::where('job_id',$job->id)->first();
            $fault_description_id=$job->ticket->fault_description_id;
            $faults = Fault::where('status', 1)->where('id',[$job->ticket->fault_description_id] )
                        ->pluck('name','id')->toArray();
            $accessories_lists = Accessories::where('status', 1)
                    ->where('id',[$job->accessories_list_id] )
                    ->pluck('accessories_name','id')
                    ->toArray();

            $allAccessories=Accessories::where('status', 1)->get();
            $allFaults=Fault::where('status', 1)->get();
            $JobAttachment = JobAttachment::where('job_id',$job->id)->get();
            $submittedJobs=JobSubmission::where('job_id',$job->id)->latest()->get();
            return view('job.show', compact('job','faults','accessories_lists', 'allAccessories','allFaults','customerAdvancedPayment','submittedJobs','JobAttachment'));
        } catch (\Exception $e) {
            $bug = $e->getMessage();
            return redirect()->back()->with('error', $bug);
        }
    }

    //Technician Show
    public function employeeJobShow($id)
    {
        try{
            $job=Job::findOrFail($id);

            $customerAdvancedPayment= CustomerAdvancedPayment::where('job_id',$job->id)->first();
            $fault_description_id=$job->ticket->fault_description_id;
            $faults = Fault::where('status', 1)
                        ->where('id',[$job->ticket->fault_description_id] )
                        ->pluck('name','id')->toArray();

            $accessories_lists = Accessories::where('status', 1)
                        ->where('id',[$job->accessories_list_id] )
                        ->pluck('accessories_name','id')
                        ->toArray();

            $allAccessories=Accessories::where('status', 1)->get();
            $allFaults=Fault::where('status', 1)->get();
            $jobCloseRemarks = JobCloseRemark::orderBy('id', 'DESC')->get();
            $jobpendingRemarks = JobPendingRemark::orderBy('id', 'DESC')->get();
            $JobAttachment = JobAttachment::where('job_id',$job->id)->get();
            $submittedJobs=JobSubmission::where('job_id',$job->id)->latest()->get();
            return view('job.technician-show', compact(
                'job','faults','accessories_lists', 'allAccessories','allFaults', 'jobCloseRemarks', 'jobpendingRemarks' ,'customerAdvancedPayment','submittedJobs','JobAttachment'
            ));
        } catch (\Exception $e) {
            $bug = $e->getMessage();
            return redirect()->back()->with('error', $bug);
        }
    }
    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        try{
            $auth = Auth::user();
            $user_role = $auth->roles->first();
            if ($user_role->name == 'Super Admin' || $user_role->name == 'Admin') {
                $employees=Employee::whereNotNull('team_leader_id')->latest()->get();
            } else {
                $teamleader=TeamLeader::where('user_id',Auth::user()->id)->first();
                if ($teamleader != null) {
                    $employees=Employee::where('team_leader_id',$teamleader->id)->latest()->get();
                }else{
                    return redirect()->back()->with('error', __('Sorry you dont have the permission.'));
                }
            }
            $job = Job::findOrFail($id);
            $ticket = Ticket::findOrFail($job->ticket_id);
            return view('job.edit', compact('job', 'ticket', 'employees'));
        } catch (\Exception $e) {
            $bug = $e->getMessage();
            return redirect()->back()->with('error', $bug);
        }
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

        $this->validate($request, [
            'date' => 'required',
            'job_number' => 'required|unique:jobs,job_number,' . $id,
            'employee_id' => 'required',
        ]);

        try{
            $job = Job::findOrFail($id);
            $employee=Employee::where('id',$request->employee_id)->first();
            $job->update([
                'date' => $request->date,
                'employee_id' =>  $request->employee_id,
                'user_id' =>  $employee->user_id,
                'note' => $request->note
            ]);

            return redirect('job/job')->with('success','Job Updated Successfully');
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
        try {
            $job=Job::findOrFail($id);
            if($job){
                $jobSubmission= JobSubmission::where('job_id', $job->id)->get();
                if(count($jobSubmission) > 0){
                    return response()->json([
                        'success' => false,
                        'message' => "Sorry! Can't Delete. This Job is Submitted Already",
                    ]);
                } else {
                    $job->delete();
                    return response()->json([
                        'success' => true,
                        'message' => 'Job Deleted Successfully.',
                    ]);
                }
            }
        } catch (\Exception $e) {
            $bug = $e->getMessage();
            return redirect()->back()->with('error', $bug);
        }
    }

    public function employeeJobList()
    {
        $jobs=Job::all();
        return view('employee.job_list.job_list_index', compact('jobs'));
    }

    public function employeeJobDetails($id)
    {
        try{
            $job=Job::find($id);
            $fault_description_id=$job->ticket->fault_description_id;
            $faults = Fault::where('status', 1)
                    ->where('id',[$job->ticket->fault_description_id] )
                    ->pluck('name','id')->toArray();

            $accessories_lists = Accessories::where('status', 1)
                        ->where('id',[$job->accessories_list_id] )
                        ->pluck('accessories_name','id')
                        ->toArray();

            $allAccessories=Accessories::where('status', 1)->get();
            $allFaults=Fault::where('status', 1)->get();
            return view('employee.job_list.show_job_details', compact('job', 'faults','accessories_lists', 'allAccessories','allFaults'));
        } catch (\Exception $e) {
            $bug = $e->getMessage();
            return redirect()->back()->with('error', $bug);
        }
    }

    public function acceptJob($id){
        try {
            $job=Job::find($id);
            $job->update([
                'status' => 1
            ]);
            $ticket = Ticket::where('id',$job->ticket_id)
            ->update([
                'is_accepted' => 1,
            ]);
            return redirect()->back()->with('success','Job Accepted Successfully');
        } catch (\Exception $e) {
            $bug = $e->getMessage();
            return redirect()->back()->with('error', $bug);
        } 
    }
    public function startJob($id){
        try {
            $current = Carbon::now('Asia/Dhaka');
            $job=Job::find($id);
            $ticket = Ticket::where('id',$job->ticket_id);
            $message='';
            if ($job->is_started == 1 && $job->is_paused == 1) {
                $job->update([
                    'is_paused' => 0,
                    'is_pending' => 1,
                    // 'status' => 3,
                ]);
                $ticket->update([
                    'is_paused' => 0,
                    'is_pending' => 1,
                ]);
                
                $message='Job is re-started successfully';
            }
            elseif($job->is_started == 1 && $job->is_paused == 0)
            {
                $job->update([
                    'is_paused' => 1,
                    'is_pending' => 0,
                    // 'status' => 6,
                ]); 
                $ticket->update([
                    'is_paused' => 1,
                    'is_pending' => 0,
                ]);
                $message='Job is paused successfully';
            }else{
                $job->update([
                    'status' => 1,
                    'is_started' => 1,
                    'job_start_time' => $current,
                ]); 
                $message='Job is started successfully';
                
                $ticket->update([
                    'is_started' => 1,
                ]);
            }


            return redirect()->back()->with('success',$message);
        } catch (\Exception $e) {
            $bug = $e->getMessage();
            return redirect()->back()->with('error', $bug);
        }
    }
    public function endJob($id, Request $request)
    {
        $this->validate($request, [
            'job_close_remark' => 'required',
        ]);

        try {
            $current = Carbon::now('Asia/Dhaka');
            $job=Job::find($id);

            $job->update([
                // 'status' => 4,
                'is_ended' => 1,
                'job_end_time' => $current,
                'job_ending_remark' => $request->remark,
                'job_close_remark' => $request->job_close_remark,
            ]);
            $ticket = Ticket::where('id',$job->ticket_id)
            ->update([
                'is_ended' => 1,
            ]);
        return redirect()->back()->with('success','Job End Successfully');
        } catch (\Exception $e) {
            $bug = $e->getMessage();
            return redirect()->back()->with('error', $bug);
        }
    }

    public function pendingJob($id, Request $request)
    {
        $this->validate($request, [
            'job_pending_remark' => 'required',
        ]);
        try {
            $job=Job::find($id);
            $job->update([
                'is_pending' => 1,
                'status' => 5,
            ]);

            JobPendingNote::create([
                'job_id' => $id,
                'job_pending_note' => $request->remark,
                'job_pending_remark' => $request->job_pending_remark, 
            ]);

            
            $ticket = Ticket::where('id',$job->ticket_id)
            ->update([
                'is_pending' => 1,
            ]);
        return redirect()->back()->with('success','Pending Note Added Successfully');
        } catch (\Exception $e) {
            $bug = $e->getMessage();
            return redirect()->back()->with('error', $bug);
        }
    }
    public function denyJob(Request $request){
        $this->validate($request, [
            'reject_note' => 'required',
        ]);

        try {
            $job=Job::find($request->job_id);
            $job->status = 2;
            $job->save();

            $ticket = Ticket::where('id',$job->ticket_id)
            ->update([
                'status' => 2,
                'is_rejected' => 1,
            ]);

            $jobNote=new JobNote();
            $jobNote->job_id       = $request->job_id;
            $jobNote->decline_note = $request->reject_note;
            $jobNote->save();
            return redirect()->back()->with('error','Job Rejected');
        } catch (\Exception $e) {
            $bug = $e->getMessage();
            return redirect()->back()->with('error', $bug);
        }
    }

    // For Admin & Super Admin
    protected function jobTotalstatus()
    {
        return DB::table('jobs')
            ->selectRaw("count(case when status = 5 and is_pending = 1 and is_ended !=1 and deleted_at IS NULL then 1 end) as pending")
            ->selectRaw("count(case when status = 1 and is_paused = 1 and is_ended = 0 and deleted_at IS NULL then 1 end) as jobPaused")
            ->selectRaw("count(case when status = 0 and deleted_at IS NULL then 1 end) as jobCreated")
            ->selectRaw("count(case when is_ended = 1 and deleted_at IS NULL then 1 end) as jobCompleted")
            ->selectRaw("count(case when status = 1 and is_started = 1 and is_pending = 0 and is_paused = 0 and is_ended = 0 and deleted_at IS NULL then 1 end) as jobStrated")
            ->selectRaw("count(case when status = 1 and is_started = 0 and is_pending = 0 and is_paused = 0 and is_ended = 0 and deleted_at IS NULL then 1 end) as jobAccepted")
            ->selectRaw("count(case when status = 2 and deleted_at IS NULL then 1 end) as jobRejected")
            ->selectRaw("count(case when deleted_at IS NULL then 1 end) as totalJob")
            ->first();
    }

    // For Teamleader
    protected function jobTotalStatusByTeam($authId)
    {
        return DB::table('jobs')
            ->selectRaw("count(case when created_by = $authId and status = 1 and is_pending = 1 and is_ended !=1 and deleted_at IS NULL then 1 end) as pending")
            ->selectRaw("count(case when created_by = $authId and status = 1 and is_paused = 1 and is_ended = 0 and deleted_at IS NULL then 1 end) as jobPaused")
            ->selectRaw("count(case when created_by = $authId and status = 0 and deleted_at IS NULL then 1 end) as jobCreated")
            ->selectRaw("count(case when created_by = $authId and  is_ended = 1 and deleted_at IS NULL then 1 end) as jobCompleted")
            ->selectRaw("count(case when created_by = $authId and status = 1 and is_started = 1 and is_pending = 0 and is_paused = 0 and is_ended = 0 and deleted_at IS NULL then 1 end) as jobStrated")
            ->selectRaw("count(case when created_by = $authId and status = 1 and is_started = 0 and is_pending = 0 and is_paused = 0 and is_ended = 0 and deleted_at IS NULL then 1 end) as jobAccepted")
            ->selectRaw("count(case when created_by = $authId and status = 2 and deleted_at IS NULL then 1 end) as jobRejected")
            ->selectRaw("count(case when created_by = $authId and deleted_at IS NULL then 1 end) as totalJob")
            ->first();
    }

    // For Technician
    protected function jobTotalStatusByUser($userId)
    {
        return DB::table('jobs')
            ->selectRaw("count(case when user_id = $userId and status = 1 and is_pending = 1 and is_ended !=1 and deleted_at IS NULL then 1 end) as pending")
            ->selectRaw("count(case when user_id = $userId and status = 1 and is_paused = 1 and is_ended = 0 and deleted_at IS NULL then 1 end) as jobPaused")
            ->selectRaw("count(case when user_id = $userId and status = 0 and deleted_at IS NULL then 1 end) as jobCreated")
            ->selectRaw("count(case when user_id = $userId and  is_ended = 1 and deleted_at IS NULL then 1 end) as jobCompleted")
            ->selectRaw("count(case when user_id = $userId and status = 1 and is_started = 1 and is_pending = 0 and is_paused = 0 and is_ended = 0 and deleted_at IS NULL then 1 end) as jobStrated")
            ->selectRaw("count(case when user_id = $userId and status = 1 and is_started = 0 and is_pending = 0 and is_paused = 0 and is_ended = 0 and deleted_at IS NULL then 1 end) as jobAccepted")
            ->selectRaw("count(case when user_id = $userId and status = 2 and deleted_at IS NULL then 1 end) as jobRejected")
            ->selectRaw("count(case when user_id = $userId and deleted_at IS NULL then 1 end) as totalJob")
            ->first();
    }

    // Unique Job SL
    protected function generateUniqueJobSl()
    {
        do {
            $job = Job::latest('id')->first();
       
            if(!$job) {
                return "JSL-1";
            }

            $string = preg_replace("/[^0-9\.]/", '', $job->job_number);
            
            $jobNumber = 'JSL-' . sprintf('%01d', $string+1);

        } while (Job::where('job_number', '==', $jobNumber)->first());

        return $jobNumber;
    }
    public function status($id)
    {
        $auth = Auth::user();
        $user_role = $auth->roles->first();

        try {
            switch($id) {
                case 1:
                    if ($user_role->name == 'Super Admin' || $user_role->name == 'Admin' || $user_role->name =='Team Leader Admin') {
                        $jobs=DB::table('jobs')
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
                        ->select('jobs.id as job_id','jobs.job_number as job_number','jobs.date as assigning_date','jobs.created_at as job_created_at','employees.name as employee_name','brand_models.model_name as model_name','brands.name as brand_name',
                        'categories.name as product_category','users.name as created_by','customers.name as customer_name', 'customers.mobile as customer_mobile','purchases.product_serial as product_serial',
                        'tickets.id as ticket_id','tickets.created_at as created_at','outlets.name as outlet_name','tickets.service_type_id as service_type_id','tickets.status as status',
                        'tickets.is_reopened as is_reopened','tickets.is_accepted as is_accepted','tickets.is_pending as is_pending','tickets.is_paused as is_paused','tickets.is_ended as is_ended',
                        'tickets.is_started as is_started','tickets.is_closed_by_teamleader as is_closed_by_teamleader','tickets.is_delivered_by_teamleader as is_delivered_by_teamleader',
                        'tickets.is_delivered_by_call_center as is_delivered_by_call_center','tickets.is_closed as is_closed','tickets.is_assigned as is_assigned','tickets.is_accepted as is_accepted',
                        'tickets.is_rejected as is_rejected','jobs.status as status','jobs.is_pending as is_pending','jobs.is_paused as is_paused','jobs.is_started as is_started','jobs.is_ended as is_ended','job_priorities.job_priority')
                        ->where('jobs.status','=',5)
                        ->where('jobs.is_pending','=',1)
                        ->where('jobs.is_ended','=',0)
                        ->where('jobs.deleted_at',null)
                        ->latest()
                        ->get();
                        $totalJobStatus = $this->jobTotalstatus();
                    } elseif ($user_role->name == 'Team Leader') {
                        $jobs=DB::table('jobs')
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
                        ->select('jobs.id as job_id','jobs.job_number as job_number','jobs.date as assigning_date','jobs.created_at as job_created_at','employees.name as employee_name','brand_models.model_name as model_name','brands.name as brand_name',
                        'categories.name as product_category','users.name as created_by','customers.name as customer_name', 'customers.mobile as customer_mobile','purchases.product_serial as product_serial',
                        'tickets.id as ticket_id','tickets.created_at as created_at','outlets.name as outlet_name','tickets.service_type_id as service_type_id','tickets.status as status',
                        'tickets.is_reopened as is_reopened','tickets.is_accepted as is_accepted','tickets.is_pending as is_pending','tickets.is_paused as is_paused','tickets.is_ended as is_ended',
                        'tickets.is_started as is_started','tickets.is_closed_by_teamleader as is_closed_by_teamleader','tickets.is_delivered_by_teamleader as is_delivered_by_teamleader',
                        'tickets.is_delivered_by_call_center as is_delivered_by_call_center','tickets.is_closed as is_closed','tickets.is_assigned as is_assigned','tickets.is_accepted as is_accepted',
                        'tickets.is_rejected as is_rejected','jobs.status as status','jobs.is_pending as is_pending','jobs.is_paused as is_paused','jobs.is_started as is_started','jobs.is_ended as is_ended','job_priorities.job_priority')
                        // ->where('jobs.status','=',6)
                        ->where('jobs.is_pending','=',1)
                        ->where('jobs.is_ended','=',0)
                        ->where('jobs.created_by',Auth::user()->id)
                        ->where('jobs.deleted_at',null)
                        ->latest()
                        ->get();
                        $totalJobStatus = $this->jobTotalStatusByTeam($auth->id);
                    } else {
                        $jobs=DB::table('jobs')
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
                        ->select('jobs.id as job_id','jobs.job_number as job_number','jobs.date as assigning_date','jobs.created_at as job_created_at','employees.name as employee_name','brand_models.model_name as model_name','brands.name as brand_name',
                        'categories.name as product_category','users.name as created_by','customers.name as customer_name', 'customers.mobile as customer_mobile','purchases.product_serial as product_serial',
                        'tickets.id as ticket_id','tickets.created_at as created_at','outlets.name as outlet_name','tickets.service_type_id as service_type_id','tickets.status as status',
                        'tickets.is_reopened as is_reopened','tickets.is_accepted as is_accepted','tickets.is_pending as is_pending','tickets.is_paused as is_paused','tickets.is_ended as is_ended',
                        'tickets.is_started as is_started','tickets.is_closed_by_teamleader as is_closed_by_teamleader','tickets.is_delivered_by_teamleader as is_delivered_by_teamleader',
                        'tickets.is_delivered_by_call_center as is_delivered_by_call_center','tickets.is_closed as is_closed','tickets.is_assigned as is_assigned','tickets.is_accepted as is_accepted',
                        'tickets.is_rejected as is_rejected','jobs.status as status','jobs.is_pending as is_pending','jobs.is_paused as is_paused','jobs.is_started as is_started','jobs.is_ended as is_ended','job_priorities.job_priority')
                        // ->where('jobs.status','=',6)
                        ->where('jobs.is_pending','=',1)
                        ->where('jobs.is_ended','=',0)
                        ->where('jobs.user_id',Auth::user()->id)
                        ->where('jobs.deleted_at',null)
                        ->latest()
                        ->get();
                        $totalJobStatus = $this->jobTotalStatusByUser(Auth::user()->id);

                    }
                    break;
                case 2:
                    if($user_role->name == 'Super Admin' || $user_role->name == 'Admin') {
                        $jobs=DB::table('jobs')
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
                        ->select('jobs.id as job_id','jobs.job_number as job_number','jobs.date as assigning_date','jobs.created_at as job_created_at','employees.name as employee_name','brand_models.model_name as model_name','brands.name as brand_name',
                        'categories.name as product_category','users.name as created_by','customers.name as customer_name', 'customers.mobile as customer_mobile','purchases.product_serial as product_serial',
                        'tickets.id as ticket_id','tickets.created_at as created_at','outlets.name as outlet_name','tickets.service_type_id as service_type_id','tickets.status as status',
                        'tickets.is_reopened as is_reopened','tickets.is_accepted as is_accepted','tickets.is_pending as is_pending','tickets.is_paused as is_paused','tickets.is_ended as is_ended',
                        'tickets.is_started as is_started','tickets.is_closed_by_teamleader as is_closed_by_teamleader','tickets.is_delivered_by_teamleader as is_delivered_by_teamleader',
                        'tickets.is_delivered_by_call_center as is_delivered_by_call_center','tickets.is_closed as is_closed','tickets.is_assigned as is_assigned','tickets.is_accepted as is_accepted',
                        'tickets.is_rejected as is_rejected','jobs.status as status','jobs.is_pending as is_pending','jobs.is_paused as is_paused','jobs.is_started as is_started','jobs.is_ended as is_ended','job_priorities.job_priority')
                        ->where('jobs.deleted_at',null)
                        ->where('jobs.status','=',1)
                        ->where('jobs.is_paused','=',1)
                        ->where('jobs.is_ended','=',0)
                        ->latest()
                        ->get();
                        $totalJobStatus = $this->jobTotalstatus();
                    }elseif($user_role->name == 'Team Leader') {
                        $jobs=DB::table('jobs')
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
                        ->select('jobs.id as job_id','jobs.job_number as job_number','jobs.date as assigning_date','jobs.created_at as job_created_at','employees.name as employee_name','brand_models.model_name as model_name','brands.name as brand_name',
                        'categories.name as product_category','users.name as created_by','customers.name as customer_name', 'customers.mobile as customer_mobile','purchases.product_serial as product_serial',
                        'tickets.id as ticket_id','tickets.created_at as created_at','outlets.name as outlet_name','tickets.service_type_id as service_type_id','tickets.status as status',
                        'tickets.is_reopened as is_reopened','tickets.is_accepted as is_accepted','tickets.is_pending as is_pending','tickets.is_paused as is_paused','tickets.is_ended as is_ended',
                        'tickets.is_started as is_started','tickets.is_closed_by_teamleader as is_closed_by_teamleader','tickets.is_delivered_by_teamleader as is_delivered_by_teamleader',
                        'tickets.is_delivered_by_call_center as is_delivered_by_call_center','tickets.is_closed as is_closed','tickets.is_assigned as is_assigned','tickets.is_accepted as is_accepted',
                        'tickets.is_rejected as is_rejected','jobs.status as status','jobs.is_pending as is_pending','jobs.is_paused as is_paused','jobs.is_started as is_started','jobs.is_ended as is_ended','job_priorities.job_priority')
                        ->where('jobs.status','=',1)
                        ->where('jobs.is_paused','=',1)
                        ->where('jobs.is_ended','=',0)
                        ->where('jobs.created_by',Auth::user()->id)
                        ->where('jobs.deleted_at',null)
                        ->latest()
                        ->get();
                        $totalJobStatus = $this->jobTotalStatusByTeam($auth->id);
                    }else { 
                        $jobs=DB::table('jobs')
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
                        ->select('jobs.id as job_id','jobs.job_number as job_number','jobs.date as assigning_date','jobs.created_at as job_created_at','employees.name as employee_name','brand_models.model_name as model_name','brands.name as brand_name',
                        'categories.name as product_category','users.name as created_by','customers.name as customer_name', 'customers.mobile as customer_mobile','purchases.product_serial as product_serial',
                        'tickets.id as ticket_id','tickets.created_at as created_at','outlets.name as outlet_name','tickets.service_type_id as service_type_id','tickets.status as status',
                        'tickets.is_reopened as is_reopened','tickets.is_accepted as is_accepted','tickets.is_pending as is_pending','tickets.is_paused as is_paused','tickets.is_ended as is_ended',
                        'tickets.is_started as is_started','tickets.is_closed_by_teamleader as is_closed_by_teamleader','tickets.is_delivered_by_teamleader as is_delivered_by_teamleader',
                        'tickets.is_delivered_by_call_center as is_delivered_by_call_center','tickets.is_closed as is_closed','tickets.is_assigned as is_assigned','tickets.is_accepted as is_accepted',
                        'tickets.is_rejected as is_rejected','jobs.status as status','jobs.is_pending as is_pending','jobs.is_paused as is_paused','jobs.is_started as is_started','jobs.is_ended as is_ended','job_priorities.job_priority')
                        ->where('jobs.status','=',1)
                        ->where('jobs.is_paused','=',1)
                        ->where('jobs.is_ended','=',0)
                        ->where('jobs.user_id',Auth::user()->id)
                        ->where('jobs.deleted_at',null)
                        ->latest()
                        ->get();
                        $totalJobStatus = $this->jobTotalStatusByUser(Auth::user()->id);
                    }
                    break;

                case 3:
                    if($user_role->name == 'Super Admin' || $user_role->name == 'Admin') {
                        $jobs=DB::table('jobs')
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
                        ->select('jobs.id as job_id','jobs.job_number as job_number','jobs.date as assigning_date','jobs.created_at as job_created_at','employees.name as employee_name','brand_models.model_name as model_name','brands.name as brand_name',
                        'categories.name as product_category','users.name as created_by','customers.name as customer_name', 'customers.mobile as customer_mobile','purchases.product_serial as product_serial',
                        'tickets.id as ticket_id','tickets.created_at as created_at','outlets.name as outlet_name','tickets.service_type_id as service_type_id','tickets.status as status',
                        'tickets.is_reopened as is_reopened','tickets.is_accepted as is_accepted','tickets.is_pending as is_pending','tickets.is_paused as is_paused','tickets.is_ended as is_ended',
                        'tickets.is_started as is_started','tickets.is_closed_by_teamleader as is_closed_by_teamleader','tickets.is_delivered_by_teamleader as is_delivered_by_teamleader',
                        'tickets.is_delivered_by_call_center as is_delivered_by_call_center','tickets.is_closed as is_closed','tickets.is_assigned as is_assigned','tickets.is_accepted as is_accepted',
                        'tickets.is_rejected as is_rejected','jobs.status as status','jobs.is_pending as is_pending','jobs.is_paused as is_paused','jobs.is_started as is_started','jobs.is_ended as is_ended','job_priorities.job_priority')
                        ->where('jobs.status','=',0)
                        ->where('jobs.deleted_at',null)
                        ->latest()
                        ->get();
                        $totalJobStatus = $this->jobTotalstatus();
                    }elseif($user_role->name == 'Team Leader') {
                        $jobs=DB::table('jobs')
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
                        ->select('jobs.id as job_id','jobs.job_number as job_number','jobs.date as assigning_date','jobs.created_at as job_created_at','employees.name as employee_name','brand_models.model_name as model_name','brands.name as brand_name',
                        'categories.name as product_category','users.name as created_by','customers.name as customer_name', 'customers.mobile as customer_mobile','purchases.product_serial as product_serial',
                        'tickets.id as ticket_id','tickets.created_at as created_at','outlets.name as outlet_name','tickets.service_type_id as service_type_id','tickets.status as status',
                        'tickets.is_reopened as is_reopened','tickets.is_accepted as is_accepted','tickets.is_pending as is_pending','tickets.is_paused as is_paused','tickets.is_ended as is_ended',
                        'tickets.is_started as is_started','tickets.is_closed_by_teamleader as is_closed_by_teamleader','tickets.is_delivered_by_teamleader as is_delivered_by_teamleader',
                        'tickets.is_delivered_by_call_center as is_delivered_by_call_center','tickets.is_closed as is_closed','tickets.is_assigned as is_assigned','tickets.is_accepted as is_accepted',
                        'tickets.is_rejected as is_rejected','jobs.status as status','jobs.is_pending as is_pending','jobs.is_paused as is_paused','jobs.is_started as is_started','jobs.is_ended as is_ended','job_priorities.job_priority')
                        ->where('jobs.status','=',0)
                        ->where('jobs.created_by',Auth::user()->id)
                        ->where('jobs.deleted_at',null)
                        ->latest()
                        ->get();
                        $totalJobStatus = $this->jobTotalStatusByTeam($auth->id);
                    }else {
                        $jobs=DB::table('jobs')
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
                        ->select('jobs.id as job_id','jobs.job_number as job_number','jobs.date as assigning_date','jobs.created_at as job_created_at','employees.name as employee_name','brand_models.model_name as model_name','brands.name as brand_name',
                        'categories.name as product_category','users.name as created_by','customers.name as customer_name', 'customers.mobile as customer_mobile','purchases.product_serial as product_serial',
                        'tickets.id as ticket_id','tickets.created_at as created_at','outlets.name as outlet_name','tickets.service_type_id as service_type_id','tickets.status as status',
                        'tickets.is_reopened as is_reopened','tickets.is_accepted as is_accepted','tickets.is_pending as is_pending','tickets.is_paused as is_paused','tickets.is_ended as is_ended',
                        'tickets.is_started as is_started','tickets.is_closed_by_teamleader as is_closed_by_teamleader','tickets.is_delivered_by_teamleader as is_delivered_by_teamleader',
                        'tickets.is_delivered_by_call_center as is_delivered_by_call_center','tickets.is_closed as is_closed','tickets.is_assigned as is_assigned','tickets.is_accepted as is_accepted',
                        'tickets.is_rejected as is_rejected','jobs.status as status','jobs.is_pending as is_pending','jobs.is_paused as is_paused','jobs.is_started as is_started','jobs.is_ended as is_ended','job_priorities.job_priority')
                        ->where('jobs.status','=',0)
                        ->where('jobs.user_id',Auth::user()->id)
                        ->where('jobs.deleted_at',null)
                        ->latest()
                        ->get();
                        $totalJobStatus = $this->jobTotalStatusByUser(Auth::user()->id);
                    }
                    break;

                case 4:
                    if($user_role->name == 'Super Admin' || $user_role->name == 'Admin') {
                        $jobs=DB::table('jobs')
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
                        ->select('jobs.id as job_id','jobs.job_number as job_number','jobs.date as assigning_date','jobs.created_at as job_created_at','employees.name as employee_name','brand_models.model_name as model_name','brands.name as brand_name',
                        'categories.name as product_category','users.name as created_by','customers.name as customer_name', 'customers.mobile as customer_mobile','purchases.product_serial as product_serial',
                        'tickets.id as ticket_id','tickets.created_at as created_at','outlets.name as outlet_name','tickets.service_type_id as service_type_id','tickets.status as status',
                        'tickets.is_reopened as is_reopened','tickets.is_accepted as is_accepted','tickets.is_pending as is_pending','tickets.is_paused as is_paused','tickets.is_ended as is_ended',
                        'tickets.is_started as is_started','tickets.is_closed_by_teamleader as is_closed_by_teamleader','tickets.is_delivered_by_teamleader as is_delivered_by_teamleader',
                        'tickets.is_delivered_by_call_center as is_delivered_by_call_center','tickets.is_closed as is_closed','tickets.is_assigned as is_assigned','tickets.is_accepted as is_accepted',
                        'tickets.is_rejected as is_rejected','jobs.status as status','jobs.is_pending as is_pending','jobs.is_paused as is_paused','jobs.is_started as is_started','jobs.is_ended as is_ended','job_priorities.job_priority')
                        // ->where('jobs.status','=',1)
                        // ->where('jobs.is_started','=',1)
                        ->where('jobs.is_ended','=',1)
                        ->where('jobs.deleted_at',null)
                        ->latest()
                        ->get();
                        $totalJobStatus = $this->jobTotalstatus();
                    }elseif($user_role->name == 'Team Leader') {
                        $jobs=DB::table('jobs')
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
                        ->select('jobs.id as job_id','jobs.job_number as job_number','jobs.date as assigning_date','jobs.created_at as job_created_at','employees.name as employee_name','brand_models.model_name as model_name','brands.name as brand_name',
                        'categories.name as product_category','users.name as created_by','customers.name as customer_name', 'customers.mobile as customer_mobile','purchases.product_serial as product_serial',
                        'tickets.id as ticket_id','tickets.created_at as created_at','outlets.name as outlet_name','tickets.service_type_id as service_type_id','tickets.status as status',
                        'tickets.is_reopened as is_reopened','tickets.is_accepted as is_accepted','tickets.is_pending as is_pending','tickets.is_paused as is_paused','tickets.is_ended as is_ended',
                        'tickets.is_started as is_started','tickets.is_closed_by_teamleader as is_closed_by_teamleader','tickets.is_delivered_by_teamleader as is_delivered_by_teamleader',
                        'tickets.is_delivered_by_call_center as is_delivered_by_call_center','tickets.is_closed as is_closed','tickets.is_assigned as is_assigned','tickets.is_accepted as is_accepted',
                        'tickets.is_rejected as is_rejected','jobs.status as status','jobs.is_pending as is_pending','jobs.is_paused as is_paused','jobs.is_started as is_started','jobs.is_ended as is_ended','job_priorities.job_priority')
                        // ->where('jobs.status','=',1)
                        // ->where('jobs.is_started','=',1)
                        ->where('jobs.is_ended','=',1)
                        ->where('jobs.created_by',Auth::user()->id)
                        ->where('jobs.deleted_at',null)
                        ->latest()
                        ->get();
                        $totalJobStatus = $this->jobTotalStatusByTeam($auth->id);
                    }else {
                        $jobs=DB::table('jobs')
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
                        ->select('jobs.id as job_id','jobs.job_number as job_number','jobs.date as assigning_date','jobs.created_at as job_created_at','employees.name as employee_name','brand_models.model_name as model_name','brands.name as brand_name',
                        'categories.name as product_category','users.name as created_by','customers.name as customer_name', 'customers.mobile as customer_mobile','purchases.product_serial as product_serial',
                        'tickets.id as ticket_id','tickets.created_at as created_at','outlets.name as outlet_name','tickets.service_type_id as service_type_id','tickets.status as status',
                        'tickets.is_reopened as is_reopened','tickets.is_accepted as is_accepted','tickets.is_pending as is_pending','tickets.is_paused as is_paused','tickets.is_ended as is_ended',
                        'tickets.is_started as is_started','tickets.is_closed_by_teamleader as is_closed_by_teamleader','tickets.is_delivered_by_teamleader as is_delivered_by_teamleader',
                        'tickets.is_delivered_by_call_center as is_delivered_by_call_center','tickets.is_closed as is_closed','tickets.is_assigned as is_assigned','tickets.is_accepted as is_accepted',
                        'tickets.is_rejected as is_rejected','jobs.status as status','jobs.is_pending as is_pending','jobs.is_paused as is_paused','jobs.is_started as is_started','jobs.is_ended as is_ended','job_priorities.job_priority')
                        // ->where('jobs.status','=',1)
                        // ->where('jobs.is_started','=',1)
                        ->where('jobs.is_ended','=',1)
                        ->where('jobs.user_id',Auth::user()->id)
                        ->where('jobs.deleted_at',null)
                        ->latest()
                        ->get();
                        $totalJobStatus = $this->jobTotalStatusByUser(Auth::user()->id);
                    }
                    break;
                    
                case 5:
                    if($user_role->name == 'Super Admin' || $user_role->name == 'Admin') {
                        $jobs=DB::table('jobs')
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
                        ->select('jobs.id as job_id','jobs.job_number as job_number','jobs.date as assigning_date','jobs.created_at as job_created_at','employees.name as employee_name','brand_models.model_name as model_name','brands.name as brand_name',
                        'categories.name as product_category','users.name as created_by','customers.name as customer_name', 'customers.mobile as customer_mobile','purchases.product_serial as product_serial',
                        'tickets.id as ticket_id','tickets.created_at as created_at','outlets.name as outlet_name','tickets.service_type_id as service_type_id','tickets.status as status',
                        'tickets.is_reopened as is_reopened','tickets.is_accepted as is_accepted','tickets.is_pending as is_pending','tickets.is_paused as is_paused','tickets.is_ended as is_ended',
                        'tickets.is_started as is_started','tickets.is_closed_by_teamleader as is_closed_by_teamleader','tickets.is_delivered_by_teamleader as is_delivered_by_teamleader',
                        'tickets.is_delivered_by_call_center as is_delivered_by_call_center','tickets.is_closed as is_closed','tickets.is_assigned as is_assigned','tickets.is_accepted as is_accepted',
                        'tickets.is_rejected as is_rejected','jobs.status as status','jobs.is_pending as is_pending','jobs.is_paused as is_paused','jobs.is_started as is_started','jobs.is_ended as is_ended','job_priorities.job_priority')
                        ->where('jobs.status','=', 1)
                        ->where('jobs.is_started','=', 1)
                        ->where('jobs.is_pending','=',0)
                        ->where('jobs.is_paused','=',0)
                        ->where('jobs.is_ended','=',0)
                        ->where('jobs.deleted_at',null)
                        ->latest()
                        ->get();
                        $totalJobStatus = $this->jobTotalstatus();
                    }elseif($user_role->name == 'Team Leader') {
                        $jobs=DB::table('jobs')
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
                        ->select('jobs.id as job_id','jobs.job_number as job_number','jobs.date as assigning_date','jobs.created_at as job_created_at','employees.name as employee_name','brand_models.model_name as model_name','brands.name as brand_name',
                        'categories.name as product_category','users.name as created_by','customers.name as customer_name', 'customers.mobile as customer_mobile','purchases.product_serial as product_serial',
                        'tickets.id as ticket_id','tickets.created_at as created_at','outlets.name as outlet_name','tickets.service_type_id as service_type_id','tickets.status as status',
                        'tickets.is_reopened as is_reopened','tickets.is_accepted as is_accepted','tickets.is_pending as is_pending','tickets.is_paused as is_paused','tickets.is_ended as is_ended',
                        'tickets.is_started as is_started','tickets.is_closed_by_teamleader as is_closed_by_teamleader','tickets.is_delivered_by_teamleader as is_delivered_by_teamleader',
                        'tickets.is_delivered_by_call_center as is_delivered_by_call_center','tickets.is_closed as is_closed','tickets.is_assigned as is_assigned','tickets.is_accepted as is_accepted',
                        'tickets.is_rejected as is_rejected','jobs.status as status','jobs.is_pending as is_pending','jobs.is_paused as is_paused','jobs.is_started as is_started','jobs.is_ended as is_ended','job_priorities.job_priority')
                        ->where('jobs.status','=', 1)
                        ->where('jobs.is_started','=', 1)
                        ->where('jobs.is_pending','=',0)
                        ->where('jobs.is_paused','=',0)
                        ->where('jobs.is_ended','=',0)
                        ->where('jobs.deleted_at',null)
                        ->where('jobs.created_by',Auth::user()->id)
                        ->latest()
                        ->get();
                        $totalJobStatus = $this->jobTotalStatusByTeam($auth->id);
                    }else {
                        $jobs=DB::table('jobs')
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
                        ->select('jobs.id as job_id','jobs.job_number as job_number','jobs.date as assigning_date','jobs.created_at as job_created_at','employees.name as employee_name','brand_models.model_name as model_name','brands.name as brand_name',
                        'categories.name as product_category','users.name as created_by','customers.name as customer_name', 'customers.mobile as customer_mobile','purchases.product_serial as product_serial',
                        'tickets.id as ticket_id','tickets.created_at as created_at','outlets.name as outlet_name','tickets.service_type_id as service_type_id','tickets.status as status',
                        'tickets.is_reopened as is_reopened','tickets.is_accepted as is_accepted','tickets.is_pending as is_pending','tickets.is_paused as is_paused','tickets.is_ended as is_ended',
                        'tickets.is_started as is_started','tickets.is_closed_by_teamleader as is_closed_by_teamleader','tickets.is_delivered_by_teamleader as is_delivered_by_teamleader',
                        'tickets.is_delivered_by_call_center as is_delivered_by_call_center','tickets.is_closed as is_closed','tickets.is_assigned as is_assigned','tickets.is_accepted as is_accepted',
                        'tickets.is_rejected as is_rejected','jobs.status as status','jobs.is_pending as is_pending','jobs.is_paused as is_paused','jobs.is_started as is_started','jobs.is_ended as is_ended','job_priorities.job_priority')
                        ->where('jobs.status','=', 1)
                        ->where('jobs.is_started','=', 1)
                        ->where('jobs.is_pending','=',0)
                        ->where('jobs.is_paused','=',0)
                        ->where('jobs.is_ended','=',0)
                        ->where('jobs.deleted_at',null)
                        ->where('jobs.user_id',Auth::user()->id)
                        ->latest()
                        ->get();
                        $totalJobStatus = $this->jobTotalStatusByUser(Auth::user()->id);
                    }
                break;
                //and is_started = 1 and is_pending = 0 and is_paused = 0 and is_ended = 0
                case 6:
                    if($user_role->name == 'Super Admin' || $user_role->name == 'Admin') {
                        $jobs=DB::table('jobs')
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
                        ->select('jobs.id as job_id','jobs.job_number as job_number','jobs.date as assigning_date','jobs.created_at as job_created_at','employees.name as employee_name','brand_models.model_name as model_name','brands.name as brand_name',
                        'categories.name as product_category','users.name as created_by','customers.name as customer_name', 'customers.mobile as customer_mobile','purchases.product_serial as product_serial',
                        'tickets.id as ticket_id','tickets.created_at as created_at','outlets.name as outlet_name','tickets.service_type_id as service_type_id','tickets.status as status',
                        'tickets.is_reopened as is_reopened','tickets.is_accepted as is_accepted','tickets.is_pending as is_pending','tickets.is_paused as is_paused','tickets.is_ended as is_ended',
                        'tickets.is_started as is_started','tickets.is_closed_by_teamleader as is_closed_by_teamleader','tickets.is_delivered_by_teamleader as is_delivered_by_teamleader',
                        'tickets.is_delivered_by_call_center as is_delivered_by_call_center','tickets.is_closed as is_closed','tickets.is_assigned as is_assigned','tickets.is_accepted as is_accepted',
                        'tickets.is_rejected as is_rejected','jobs.status as status','jobs.is_pending as is_pending','jobs.is_paused as is_paused','jobs.is_started as is_started','jobs.is_ended as is_ended','job_priorities.job_priority')
                        ->where('jobs.status','=', 1)
                        ->where('jobs.is_started','=', 0)
                        ->where('jobs.is_pending','=',0)
                        ->where('jobs.is_paused','=',0)
                        ->where('jobs.is_ended','=',0)
                        ->where('jobs.deleted_at',null)
                        ->latest()
                        ->get();
                        $totalJobStatus = $this->jobTotalstatus();
                    }elseif($user_role->name == 'Team Leader') {
                        $jobs=DB::table('jobs')
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
                        ->select('jobs.id as job_id','jobs.job_number as job_number','jobs.date as assigning_date','jobs.created_at as job_created_at','employees.name as employee_name','brand_models.model_name as model_name','brands.name as brand_name',
                        'categories.name as product_category','users.name as created_by','customers.name as customer_name', 'customers.mobile as customer_mobile','purchases.product_serial as product_serial',
                        'tickets.id as ticket_id','tickets.created_at as created_at','outlets.name as outlet_name','tickets.service_type_id as service_type_id','tickets.status as status',
                        'tickets.is_reopened as is_reopened','tickets.is_accepted as is_accepted','tickets.is_pending as is_pending','tickets.is_paused as is_paused','tickets.is_ended as is_ended',
                        'tickets.is_started as is_started','tickets.is_closed_by_teamleader as is_closed_by_teamleader','tickets.is_delivered_by_teamleader as is_delivered_by_teamleader',
                        'tickets.is_delivered_by_call_center as is_delivered_by_call_center','tickets.is_closed as is_closed','tickets.is_assigned as is_assigned','tickets.is_accepted as is_accepted',
                        'tickets.is_rejected as is_rejected','jobs.status as status','jobs.is_pending as is_pending','jobs.is_paused as is_paused','jobs.is_started as is_started','jobs.is_ended as is_ended','job_priorities.job_priority')
                        ->where('jobs.status','=', 1)
                        ->where('jobs.is_started','=', 0)
                        ->where('jobs.is_pending','=',0)
                        ->where('jobs.is_paused','=',0)
                        ->where('jobs.is_ended','=',0)
                        ->where('jobs.created_by',Auth::user()->id)
                        ->where('jobs.deleted_at',null)
                        ->latest()
                        ->get();
                        $totalJobStatus = $this->jobTotalStatusByTeam($auth->id);
                    }else {
                        $jobs=DB::table('jobs')
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
                        ->select('jobs.id as job_id','jobs.job_number as job_number','jobs.date as assigning_date','jobs.created_at as job_created_at','employees.name as employee_name','brand_models.model_name as model_name','brands.name as brand_name',
                        'categories.name as product_category','users.name as created_by','customers.name as customer_name', 'customers.mobile as customer_mobile','purchases.product_serial as product_serial',
                        'tickets.id as ticket_id','tickets.created_at as created_at','outlets.name as outlet_name','tickets.service_type_id as service_type_id','tickets.status as status',
                        'tickets.is_reopened as is_reopened','tickets.is_accepted as is_accepted','tickets.is_pending as is_pending','tickets.is_paused as is_paused','tickets.is_ended as is_ended',
                        'tickets.is_started as is_started','tickets.is_closed_by_teamleader as is_closed_by_teamleader','tickets.is_delivered_by_teamleader as is_delivered_by_teamleader',
                        'tickets.is_delivered_by_call_center as is_delivered_by_call_center','tickets.is_closed as is_closed','tickets.is_assigned as is_assigned','tickets.is_accepted as is_accepted',
                        'tickets.is_rejected as is_rejected','jobs.status as status','jobs.is_pending as is_pending','jobs.is_paused as is_paused','jobs.is_started as is_started','jobs.is_ended as is_ended','job_priorities.job_priority')
                        ->where('jobs.status','=', 1)
                        ->where('jobs.is_started','=', 0)
                        ->where('jobs.is_pending','=',0)
                        ->where('jobs.is_paused','=',0)
                        ->where('jobs.is_ended','=',0)
                        ->where('jobs.user_id',Auth::user()->id)
                        ->where('jobs.deleted_at',null)
                        ->latest()
                        ->get();
                        $totalJobStatus = $this->jobTotalStatusByUser(Auth::user()->id);
                    }
                break;

                case 7:
                   $totalJobStatus = $this->jobTotalStatusByUser(Auth::user()->id);
                    if($user_role->name == 'Super Admin' || $user_role->name == 'Admin') {
                        $jobs=DB::table('jobs')
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
                        ->select('jobs.id as job_id','jobs.job_number as job_number','jobs.date as assigning_date','jobs.created_at as job_created_at','employees.name as employee_name','brand_models.model_name as model_name','brands.name as brand_name',
                        'categories.name as product_category','users.name as created_by','customers.name as customer_name', 'customers.mobile as customer_mobile','purchases.product_serial as product_serial',
                        'tickets.id as ticket_id','tickets.created_at as created_at','outlets.name as outlet_name','tickets.service_type_id as service_type_id','tickets.status as status',
                        'tickets.is_reopened as is_reopened','tickets.is_accepted as is_accepted','tickets.is_pending as is_pending','tickets.is_paused as is_paused','tickets.is_ended as is_ended',
                        'tickets.is_started as is_started','tickets.is_closed_by_teamleader as is_closed_by_teamleader','tickets.is_delivered_by_teamleader as is_delivered_by_teamleader',
                        'tickets.is_delivered_by_call_center as is_delivered_by_call_center','tickets.is_closed as is_closed','tickets.is_assigned as is_assigned','tickets.is_accepted as is_accepted',
                        'tickets.is_rejected as is_rejected','jobs.status as status','jobs.is_pending as is_pending','jobs.is_paused as is_paused','jobs.is_started as is_started','jobs.is_ended as is_ended','job_priorities.job_priority')
                        ->where('jobs.status',2)
                        ->where('jobs.deleted_at',null)
                        ->latest()
                        ->get();
                        $totalJobStatus = $this->jobTotalstatus();
                    }elseif($user_role->name == 'Team Leader') {
                        $jobs=DB::table('jobs')
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
                        ->select('jobs.id as job_id','jobs.job_number as job_number','jobs.date as assigning_date','jobs.created_at as job_created_at','employees.name as employee_name','brand_models.model_name as model_name','brands.name as brand_name',
                        'categories.name as product_category','users.name as created_by','customers.name as customer_name', 'customers.mobile as customer_mobile','purchases.product_serial as product_serial',
                        'tickets.id as ticket_id','tickets.created_at as created_at','outlets.name as outlet_name','tickets.service_type_id as service_type_id','tickets.status as status',
                        'tickets.is_reopened as is_reopened','tickets.is_accepted as is_accepted','tickets.is_pending as is_pending','tickets.is_paused as is_paused','tickets.is_ended as is_ended',
                        'tickets.is_started as is_started','tickets.is_closed_by_teamleader as is_closed_by_teamleader','tickets.is_delivered_by_teamleader as is_delivered_by_teamleader',
                        'tickets.is_delivered_by_call_center as is_delivered_by_call_center','tickets.is_closed as is_closed','tickets.is_assigned as is_assigned','tickets.is_accepted as is_accepted',
                        'tickets.is_rejected as is_rejected','jobs.status as status','jobs.is_pending as is_pending','jobs.is_paused as is_paused','jobs.is_started as is_started','jobs.is_ended as is_ended','job_priorities.job_priority')
                        ->where('jobs.status',2)
                        ->where('jobs.created_by',Auth::user()->id)
                        ->where('jobs.deleted_at',null)
                        ->latest()
                        ->get();
                        $totalJobStatus = $this->jobTotalStatusByTeam($auth->id);
                    }else {
                        $jobs=DB::table('jobs')
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
                        ->select('jobs.id as job_id','jobs.job_number as job_number','jobs.date as assigning_date','jobs.created_at as job_created_at','employees.name as employee_name','brand_models.model_name as model_name','brands.name as brand_name',
                        'categories.name as product_category','users.name as created_by','customers.name as customer_name', 'customers.mobile as customer_mobile','purchases.product_serial as product_serial',
                        'tickets.id as ticket_id','tickets.created_at as created_at','outlets.name as outlet_name','tickets.service_type_id as service_type_id','tickets.status as status',
                        'tickets.is_reopened as is_reopened','tickets.is_accepted as is_accepted','tickets.is_pending as is_pending','tickets.is_paused as is_paused','tickets.is_ended as is_ended',
                        'tickets.is_started as is_started','tickets.is_closed_by_teamleader as is_closed_by_teamleader','tickets.is_delivered_by_teamleader as is_delivered_by_teamleader',
                        'tickets.is_delivered_by_call_center as is_delivered_by_call_center','tickets.is_closed as is_closed','tickets.is_assigned as is_assigned','tickets.is_accepted as is_accepted',
                        'tickets.is_rejected as is_rejected','jobs.status as status','jobs.is_pending as is_pending','jobs.is_paused as is_paused','jobs.is_started as is_started','jobs.is_ended as is_ended','job_priorities.job_priority')
                        ->where('jobs.status',2)
                        ->where('jobs.user_id',Auth::user()->id)
                        ->where('jobs.deleted_at',null)
                        ->latest()
                        ->get();
                        $totalJobStatus = $this->jobTotalStatusByUser(Auth::user()->id);
                    }
                break;

                case 8:
                    $totalJobStatus = $this->jobTotalStatusByUser(Auth::user()->id);
                     if($user_role->name == 'Super Admin' || $user_role->name == 'Admin') {
                         $jobs=DB::table('jobs')
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
                         ->select('jobs.id as job_id','jobs.job_number as job_number','jobs.date as assigning_date','jobs.created_at as job_created_at','employees.name as employee_name','brand_models.model_name as model_name','brands.name as brand_name',
                         'categories.name as product_category','users.name as created_by','customers.name as customer_name', 'customers.mobile as customer_mobile','purchases.product_serial as product_serial',
                         'tickets.id as ticket_id','tickets.created_at as created_at','outlets.name as outlet_name','tickets.service_type_id as service_type_id','tickets.status as status',
                         'tickets.is_reopened as is_reopened','tickets.is_accepted as is_accepted','tickets.is_pending as is_pending','tickets.is_paused as is_paused','tickets.is_ended as is_ended',
                         'tickets.is_started as is_started','tickets.is_closed_by_teamleader as is_closed_by_teamleader','tickets.is_delivered_by_teamleader as is_delivered_by_teamleader',
                         'tickets.is_delivered_by_call_center as is_delivered_by_call_center','tickets.is_closed as is_closed','tickets.is_assigned as is_assigned','tickets.is_accepted as is_accepted',
                         'tickets.is_rejected as is_rejected','jobs.status as status','jobs.is_pending as is_pending','jobs.is_paused as is_paused','jobs.is_started as is_started','jobs.is_ended as is_ended','job_priorities.job_priority')
                         ->where('jobs.deleted_at',null)
                         ->latest()
                         ->get();
                         $totalJobStatus = $this->jobTotalstatus();
                     }elseif($user_role->name == 'Team Leader') {
                         $jobs=DB::table('jobs')
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
                         ->select('jobs.id as job_id','jobs.job_number as job_number','jobs.date as assigning_date','jobs.created_at as job_created_at','employees.name as employee_name','brand_models.model_name as model_name','brands.name as brand_name',
                         'categories.name as product_category','users.name as created_by','customers.name as customer_name', 'customers.mobile as customer_mobile','purchases.product_serial as product_serial',
                         'tickets.id as ticket_id','tickets.created_at as created_at','outlets.name as outlet_name','tickets.service_type_id as service_type_id','tickets.status as status',
                         'tickets.is_reopened as is_reopened','tickets.is_accepted as is_accepted','tickets.is_pending as is_pending','tickets.is_paused as is_paused','tickets.is_ended as is_ended',
                         'tickets.is_started as is_started','tickets.is_closed_by_teamleader as is_closed_by_teamleader','tickets.is_delivered_by_teamleader as is_delivered_by_teamleader',
                         'tickets.is_delivered_by_call_center as is_delivered_by_call_center','tickets.is_closed as is_closed','tickets.is_assigned as is_assigned','tickets.is_accepted as is_accepted',
                         'tickets.is_rejected as is_rejected','jobs.status as status','jobs.is_pending as is_pending','jobs.is_paused as is_paused','jobs.is_started as is_started','jobs.is_ended as is_ended','job_priorities.job_priority')
                         ->where('jobs.created_by',Auth::user()->id)
                         ->where('jobs.deleted_at',null)
                         ->latest()
                         ->get();
                         $totalJobStatus = $this->jobTotalStatusByTeam($auth->id);
                     }else {
                         $jobs=DB::table('jobs')
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
                         ->select('jobs.id as job_id','jobs.job_number as job_number','jobs.date as assigning_date','jobs.created_at as job_created_at','employees.name as employee_name','brand_models.model_name as model_name','brands.name as brand_name',
                         'categories.name as product_category','users.name as created_by','customers.name as customer_name', 'customers.mobile as customer_mobile','purchases.product_serial as product_serial',
                         'tickets.id as ticket_id','tickets.created_at as created_at','outlets.name as outlet_name','tickets.service_type_id as service_type_id','tickets.status as status',
                         'tickets.is_reopened as is_reopened','tickets.is_accepted as is_accepted','tickets.is_pending as is_pending','tickets.is_paused as is_paused','tickets.is_ended as is_ended',
                         'tickets.is_started as is_started','tickets.is_closed_by_teamleader as is_closed_by_teamleader','tickets.is_delivered_by_teamleader as is_delivered_by_teamleader',
                         'tickets.is_delivered_by_call_center as is_delivered_by_call_center','tickets.is_closed as is_closed','tickets.is_assigned as is_assigned','tickets.is_accepted as is_accepted',
                         'tickets.is_rejected as is_rejected','jobs.status as status','jobs.is_pending as is_pending','jobs.is_paused as is_paused','jobs.is_started as is_started','jobs.is_ended as is_ended','job_priorities.job_priority')
                         ->where('jobs.user_id',Auth::user()->id)
                         ->where('jobs.deleted_at',null)
                         ->latest()
                         ->get();
                         $totalJobStatus = $this->jobTotalStatusByUser(Auth::user()->id);
                     }
                 break;
                default:
                    return redirect()->route('technician.jobs');
            }
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
                        // $ticket_created_at=0;
                            
                        return $ticket_created_at;
                    })
                    ->addColumn('job_number', function ($jobs) {
                        $job_number=$jobs->job_number; 
                        return $job_number;
                    })
                    
                    ->addColumn('assigning_date', function ($jobs) {
                        $assigning_date=$jobs->assigning_date; 
                        return $assigning_date;
                    })
                    ->addColumn('created_by', function ($jobs) {
                        $created_by=$jobs->created_by; 
                        return $created_by;
                    })
                    ->addColumn('job_priority', function($jobs){
                        $job_priority=$jobs->job_priority?? Null;
                        return $job_priority;
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

                    ->addColumn('status', function ($jobs) {

                        if ($jobs->status == 1 && $jobs->is_pending == 1 && $jobs->is_paused==1){
                            return '<span class="badge badge-lime">Paused</span>';
                        }
                        
                        elseif( $jobs->status==1 && $jobs->is_pending == 1 && $jobs->is_ended !=1){
                            return '<span class="badge badge-orange">Pending</span>';
                        }

                        
                        elseif($jobs->status==0)
                        {
                            return '<span class="badge badge-yellow">Created</span>';
                        }

                        elseif($jobs->status==1 && $jobs->is_started==1 && $jobs->is_ended==1)
                        {
                            return '<span class="badge badge-info">Job Completed</span>';
                        }

                        elseif($jobs->status==1 && $jobs->is_started==1)
                        {
                            return '<span class="badge badge-success">Job Started</span>';
                        }
                        elseif($jobs->status==1)
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
                            if (Auth::user()->can('edit') && Auth::user()->can('delete') && Auth::user()->can('show')) {
                                return '<div class="table-actions text-center" style="display: flex;">
                                            <a href=" '.route('job.job.show', $jobs->job_id). ' " title="View">
                                                <i class="ik ik-eye f-16 mr-15 text-green"></i>
                                            </a>
                                            <a href=" '.route('job.job.edit', $jobs->job_id). ' " title="View">
                                                <i class="ik ik-edit f-16 mr-15 text-green" title="Edit"></i>
                                            </a>
                                            <a type="submit" onclick="showDeleteConfirm(' . $jobs->job_id . ')" title="Delete"><i class="ik ik-trash-2 f-16 text-red"></i></a>
                                        </div>';
                            } elseif (Auth::user()->can('edit') && Auth::user()->can('show')) {
                                return '<div class="table-actions" style="display: flex;">
                                                <a href=" '.route('job.job.show', $jobs->job_id). ' " title="View">
                                                    <i class="ik ik-eye f-16 mr-15 text-green"></i>
                                                </a>
                                                <a href=" '.route('job.job.edit', $jobs->job_id). ' " title="View">
                                                    <i class="ik ik-edit f-16 mr-15 text-green" title="Edit"></i>
                                                </a>
                                                </div>';
                            } elseif (Auth::user()->can('delete')) {
                                return '<div class="table-actions">
                                            <a type="submit" onclick="showDeleteConfirm(' . $jobs->job_id . ')" title="Delete"><i class="ik ik-trash-2 f-16 text-red"></i></a>
                                        </div>';
                            } elseif (Auth::user()->can('show')) {
                                return '<div class="table-actions">
                                            <a href=" '.route('job.job.show', $jobs->job_id). ' " title="View">
                                                <i class="ik ik-eye f-16 mr-15 text-green"></i>
                                            </a>
                                        </div>';
                            } 
                    })
                    ->addIndexColumn()
                    ->rawColumns(['ticket_sl','status','job_pending_remark','action'])
                    ->make(true);
            }
            return view('job.job_status', compact('totalJobStatus', 'id'));
        }catch (\Exception $e) {
            $bug = $e->getMessage();
            return redirect()->back()->with('error', $bug);
        }
    }
}
