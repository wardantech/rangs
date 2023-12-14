<?php

namespace App\Http\Controllers\Job;

use Session;
use Redirect;
use Validator;
use DataTables;
use Carbon\Carbon;
use Excel;
use App\Traits\OTPTraits;
use App\Exports\JobExport;
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
use App\Models\Ticket\ServiceType;
use Illuminate\Support\Facades\DB;
use App\Models\Employee\TeamLeader;
use App\Http\Controllers\Controller;
use App\Models\Job\JobPendingRemark;
use Illuminate\Support\Facades\Auth;
use App\Models\ProductPurchase\Purchase;
use App\Models\Job\CustomerAdvancedPayment;
use App\Models\Ticket\ProductCondition;
use App\Models\User;
use App\Models\Outlet\Outlet;
use App\Models\Job\SpecialComponent;

class JobController extends Controller
{
    use OTPTraits;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        try{
            $auth = Auth::user();
            $user_role = $auth->roles->first();
            if ($user_role->name == 'Super Admin' || $user_role->name == 'Admin' || $user_role->name =='Team Leader Admin') {
                $totalJobStatus = $this->jobTotalstatus();
            } elseif ($user_role->name == 'Team Leader') {
                $totalJobStatus = $this->jobTotalStatusByTeam($auth->id);
            } else {
                $totalJobStatus = $this->jobTotalStatusByUser(Auth::user()->id);
            }
            
            if (request()->ajax()) {
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
                ->whereIn('jobs.status',[0,1,3,5])
                ->where('jobs.deleted_at',null);

                if ($user_role->name == 'Super Admin' || $user_role->name == 'Admin' || $user_role->name =='Team Leader Admin') {
                    $data;
                } elseif ($user_role->name == 'Team Leader') {
                    $data->where('jobs.created_by',Auth::user()->id);
                } elseif ($user_role->name == 'Technician') {
                    $data->where('jobs.user_id',Auth::user()->id);
                } else {
                    $data->where('tickets.outlet_id', $employee->outlet_id);
                }

                if(!empty($request->start_date && $request->end_date))
                {
                    $startDate=Carbon::parse($request->get('start_date'))->format('Y-m-d');
                    $endDate=Carbon::parse($request->get('end_date'))->addDay()->format('Y-m-d');
                    $jobs=$data->whereBetween('jobs.created_at',[$startDate, $endDate])->latest()->get();
                } 
                else{
                    $jobs=$data->latest()->get();
                }
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
                        switch ($jobs->status) {
                            case 6:
                                $badgeClass = 'badge-red';
                                $statusText = 'Paused';
                                break;
                    
                            case 5:
                                $badgeClass = 'badge-orange';
                                $statusText = 'Pending';
                                break;
                    
                            case 0:
                                $badgeClass = 'badge-yellow';
                                $statusText = 'Created';
                                break;
                    
                            case 4:
                                $badgeClass = 'badge-info';
                                $statusText = 'Job Completed';
                                break;
                    
                            case 3:
                                $badgeClass = 'badge-success';
                                $statusText = 'Job Started';
                                break;
                    
                            case 1:
                                $badgeClass = 'badge-success';
                                $statusText = 'Accepted';
                                break;
                    
                            case 2:
                                $badgeClass = 'badge-danger';
                                $statusText = 'Rejected';
                                break;
                    
                            default:
                                $badgeClass = '';
                                $statusText = 'Unknown';
                        }
                    
                        return $badgeClass ? "<span class=\"badge $badgeClass\">$statusText</span>" : '';
                    })
                    
                    ->addColumn('job_created_at', function ($jobs) {
                        $job_created_at=Carbon::parse($jobs->job_created_at)->format('m/d/Y');
                        return $job_created_at;
                    })

                    ->addColumn('job_pending_remark', function ($jobs) {
                        $pendingNotes = DB::table('job_pending_notes')->where('job_id', $jobs->job_id)->get();
                    
                        $data = collect($pendingNotes)->map(function ($item) {
                            return '<ol style="font-weight: bold; color:red">' . $item->job_pending_remark . '-' . $item->job_pending_note . '</ol>';
                        })->implode('');
                    
                        return $data ?: 'Unavailable.';
                    })
                    ->addColumn('pending_for_special_components', function ($jobs) {
                        $pendingNotes = DB::table('job_pending_notes')->where('job_id', $jobs->job_id)->get();
                    
                        $data = collect($pendingNotes)->flatMap(function ($item) {
                            $specialComponents = json_decode($item->special_components, true);
                    
                            return $specialComponents ? array_map(function ($special_component) {
                                return '<li>' . $special_component . '</li>';
                            }, $specialComponents) : [];
                        })->implode('');
                    
                        return $data ? '<ul>' . $data . '</ul>' : 'Unavailable.';
                    })
                    

                    ->addColumn('action', function ($jobs) use ($user_role) {
                        $html = '<div class="table-actions';
                        
                        if (($user_role->name == 'Super Admin' || $user_role->name == 'Admin' || $user_role->name == 'Team Leader') && Auth::user()->can('show')) {
                            $html .= ' text-center" style="display: flex;">';
                            $html .= '<a href="'.route('job.job.show', $jobs->job_id).'" title="View"><i class="ik ik-eye f-16 mr-15 text-green"></i></a>';
                    
                            if (Auth::user()->can('edit')) {
                                $html .= '<a href="'.route('job.job.edit', $jobs->job_id).'" title="Edit"><i class="ik ik-edit f-16 mr-15 text-blue"></i></a>';
                            }
                    
                            if (Auth::user()->can('delete')) {
                                $html .= '<a type="submit" onclick="showDeleteConfirm('.$jobs->job_id.')" title="Delete"><i class="ik ik-trash-2 f-16 text-red"></i></a>';
                            }
                        } elseif (Auth::user()->can('access_to_technician_jobs_list') && Auth::user()->can('show')) {
                            $html .= '">';
                            $html .= '<a href="'.route('technician.jobs.show', $jobs->job_id).'" title="View"><i class="ik ik-eye f-16 mr-15 text-green"></i></a>';
                        }
                    
                        $html .= '</div>';
                        return $html;
                    })
                    
                    
                    ->addIndexColumn()
                    ->rawColumns(['ticket_sl','job_number','service_type','warranty_type','status','job_pending_remark','pending_for_special_components','action'])
                    ->make(true);
            }
            return view('job.index', compact('totalJobStatus'));
        } catch (\Exception $e) {
            $bug = $e->getMessage();
            return redirect()->back()->with('error', $bug);
        }
    }
    // public function index(Request $request)
    // {
    //     try{
    //         $auth = Auth::user();
    //         $user_role = $auth->roles->first();

    //         if ($user_role->name == 'Team Leader') {
    //             $totalJobStatus = $this->jobTotalStatusByTeam($auth->id);
    //         } else {
    //             $totalJobStatus = $this->jobTotalstatus();
    //         }

    //         if (request()->ajax()) {

    //             $employee=Employee::where('user_id', Auth::user()->id)->first();
    //             $serviceTypes = ServiceType::where('status', 1)->get();

    //             $data=DB::table('jobs')
    //             ->join('employees', 'jobs.employee_id', '=', 'employees.id')
    //             ->join('users', 'jobs.created_by', '=', 'users.id')
    //             ->join('tickets', 'jobs.ticket_id', '=', 'tickets.id')
    //             ->join('job_priorities', 'tickets.job_priority_id', '=', 'job_priorities.id')
    //             ->join('outlets','tickets.outlet_id','=','outlets.id')
    //             ->join('purchases','tickets.purchase_id','=','purchases.id')
    //             ->join('categories','tickets.product_category_id','=','categories.id')
    //             ->join('brand_models','purchases.brand_model_id', '=', 'brand_models.id')
    //             ->join('brands','purchases.brand_id', '=', 'brands.id')
    //             ->join('customers','purchases.customer_id', '=', 'customers.id')
    //             ->leftjoin('warranty_types','tickets.warranty_type_id', '=', 'warranty_types.id')
    //             ->select('jobs.id as job_id','jobs.job_number as job_number','jobs.date as assigning_date','jobs.created_at as job_created_at','employees.name as employee_name','employees.vendor_id as vendor_id','brand_models.model_name as model_name','brands.name as brand_name',
    //             'categories.name as product_category','users.name as created_by','customers.name as customer_name', 'customers.mobile as customer_mobile','purchases.product_serial as product_serial','purchases.invoice_number as invoice_number','purchases.purchase_date as purchase_date',
    //             'tickets.id as ticket_id','tickets.created_at as created_at','outlets.name as outlet_name','tickets.service_type_id as service_type_id','tickets.status as ticket_status',
    //             'tickets.is_reopened as is_reopened','tickets.is_accepted as is_accepted','tickets.is_pending as ticket_is_pending','tickets.is_paused as ticket_is_paused','tickets.is_ended as ticket_is_ended',
    //             'tickets.is_started as ticket_is_started','tickets.is_closed_by_teamleader as is_closed_by_teamleader','tickets.is_delivered_by_teamleader as is_delivered_by_teamleader',
    //             'tickets.is_delivered_by_call_center as is_delivered_by_call_center','tickets.is_closed as is_closed','tickets.is_assigned as is_assigned',
    //             'tickets.is_rejected as is_rejected','jobs.status as status','jobs.is_pending as is_pending','jobs.is_paused as is_paused','jobs.is_started as is_started','jobs.is_ended as is_ended','job_priorities.job_priority','tickets.outlet_id as outlet_id',
    //             'warranty_types.warranty_type as warranty_type','purchases.outlet_id as outletid')
    //             ->whereIn('jobs.status',[0,1,3,5])
    //             ->where('jobs.deleted_at',null);
    
    //             if ($user_role->name == 'Super Admin' || $user_role->name == 'Admin' || $user_role->name =='Team Leader Admin') {
    //                 $data;
    //             } elseif ($user_role->name == 'Team Leader') {
    //                 $data->where('jobs.created_by',Auth::user()->id);
    //             } else {
    //                 $data->where('tickets.outlet_id', $employee->outlet_id);
    //             }

    //             if(!empty($request->start_date && $request->end_date))
    //             {
    //                 $startDate=Carbon::parse($request->get('start_date'))->format('Y-m-d');
    //                 $endDate=Carbon::parse($request->get('end_date'))->addDay()->format('Y-m-d');
    //                 $jobs=$data->whereBetween('jobs.created_at',[$startDate, $endDate])->latest('jobs.id')->get();
    //             } 
    //             else{
    //                 $jobs=$data->latest('jobs.id')->get();
    //             }
    //             return DataTables::of($jobs)

    //                 ->addColumn('emplyee_name', function ($jobs) {
    //                     $employee_name=$jobs->employee_name ?? null;
    //                     return $employee_name;
    //                 })

    //                 ->addColumn('outlet_name', function ($jobs) {
    //                     $outlet_name=$jobs->outlet_name ?? Null;
    //                     return $outlet_name;
    //                 })

    //                 ->addColumn('ticket_sl', function ($jobs) {
    //                     return '<a href="'.route('show-ticket-details', $jobs->ticket_id).'" class="badge badge-primary" title="Ticket Details">'.'TSL-'.''. $jobs->ticket_id.'</a>';
    //                 })
                    
    //                 ->addColumn('ticket_created_at', function ($jobs) {
    //                     $ticket_created_at=Carbon::parse($jobs->created_at)->format('m/d/Y');   
    //                     return $ticket_created_at;
    //                 })
    //                 ->addColumn('purchase_date', function ($jobs) {
    //                     $purchase_date=Carbon::parse($jobs->purchase_date)->format('m/d/Y');                        
    //                     return $purchase_date;
    //                 })
    //                 ->addColumn('job_number', function ($jobs) {
    //                     $job_number='JSL-'.$jobs->job_id; 
    //                     return $job_number;
    //                 })
                    
    //                 ->addColumn('service_type', function($jobs) use($serviceTypes){
    //                     $selectedServiceTypeIds=json_decode($jobs->service_type_id);
    //                     $data='';
    //                     foreach ($serviceTypes as $key => $serviceType) {
    //                        if (in_array($serviceType->id, $selectedServiceTypeIds)) {
    //                            $data=$serviceType->service_type;
    //                        }
    //                     }
    //                     return $data;
    //                })
    //                ->addColumn('warranty_type', function ($jobs) {
    //                     $warranty_type=$jobs->warranty_type ?? null; 
    //                     return $warranty_type;
    //                 })
    //                 ->addColumn('assigning_date', function ($jobs) {
    //                     $assigning_date=Carbon::parse($jobs->assigning_date)->format('m/d/Y'); 
    //                     return $assigning_date;
    //                 })
    //                 ->addColumn('created_by', function ($jobs) {
    //                     $created_by=$jobs->created_by; 
    //                     return $created_by;
    //                 })
    //                 ->addColumn('job_priority', function($jobs){
    //                     $job_priority=$jobs->job_priority?? Null;
    //                     return $job_priority;
    //                 })
    //                 ->addColumn('product_category', function ($jobs) {
    //                     $product_category=$jobs->product_category ?? Null;
    //                     return $product_category;
    //                 })
    //                 ->addColumn('brand_name', function ($jobs) {
    //                     $brand_name=$jobs->brand_name ?? Null;
    //                     return $brand_name;
    //                 })
    //                 ->addColumn('model_name', function ($jobs) {
    //                     $model_name=$jobs->model_name ?? Null;
    //                     return $model_name;
    //                 })
    //                 ->addColumn('product_serial', function ($jobs) {
    //                     $product_serial=$jobs->product_serial ?? Null;
    //                     return $product_serial;
    //                 })
    //                 ->addColumn('point_of_purchase', function($tickets){
    //                     $point_of_purchase=Outlet::where('id', '=', $tickets->outletid)->first();
    //                         return $point_of_purchase->name ?? null;
    //                 })
    //                 ->addColumn('invoice_number', function ($jobs) {
    //                     $invoice_number=$jobs->invoice_number;
    //                     return $invoice_number;
    //                 })
    //                 ->addColumn('customer_name', function ($jobs) {
    //                     $invoice_number=$jobs->customer_name;
    //                     return $invoice_number;
    //                 })
    //                 ->addColumn('customer_mobile', function ($jobs) {
    //                     $invoice_number=$jobs->customer_mobile;
    //                     return $invoice_number;
    //                 })
    //                 ->addColumn('technician_type', function ($jobs) {
    //                     $tech_type='';
    //                     if ($jobs->vendor_id != null) {
    //                         $tech_type='Vendor';
    //                     }else{
    //                         $tech_type='Own';
    //                     }
    //                     return $tech_type;
    //                 })
    //                 ->addColumn('status', function ($jobs) {
    //                     switch ($jobs->status) {
    //                         case 6:
    //                             $badgeClass = 'badge-red';
    //                             $statusText = 'Paused';
    //                             break;
                    
    //                         case 5:
    //                             $badgeClass = 'badge-orange';
    //                             $statusText = 'Pending';
    //                             break;
                    
    //                         case 0:
    //                             $badgeClass = 'badge-yellow';
    //                             $statusText = 'Created';
    //                             break;
                    
    //                         case 4:
    //                             $badgeClass = 'badge-info';
    //                             $statusText = 'Job Completed';
    //                             break;
                    
    //                         case 3:
    //                             $badgeClass = 'badge-success';
    //                             $statusText = 'Job Started';
    //                             break;
                    
    //                         case 1:
    //                             $badgeClass = 'badge-success';
    //                             $statusText = 'Accepted';
    //                             break;
                    
    //                         case 2:
    //                             $badgeClass = 'badge-danger';
    //                             $statusText = 'Rejected';
    //                             break;
                    
    //                         default:
    //                             $badgeClass = '';
    //                             $statusText = 'Unknown';
    //                     }
                    
    //                     return $badgeClass ? "<span class=\"badge $badgeClass\">$statusText</span>" : '';
    //                 })
                    
    //                 ->addColumn('job_created_at', function ($jobs) {
    //                     $job_created_at=Carbon::parse($jobs->job_created_at)->format('m/d/Y');
    //                     return $job_created_at;
    //                 })

    //                 ->addColumn('job_pending_remark', function ($jobs) {
    //                     $pendingNotes = DB::table('job_pending_notes')->where('job_id', $jobs->job_id)->get();
                    
    //                     $data = collect($pendingNotes)->map(function ($item) {
    //                         return '<ol style="font-weight: bold; color:red">' . $item->job_pending_remark . '-' . $item->job_pending_note . '</ol>';
    //                     })->implode('');
                    
    //                     return $data ?: 'Unavailable.';
    //                 })
    //                 ->addColumn('pending_for_special_components', function ($jobs) {
    //                     $pendingNotes = DB::table('job_pending_notes')->where('job_id', $jobs->job_id)->get();
                    
    //                     $data = collect($pendingNotes)->flatMap(function ($item) {
    //                         $specialComponents = json_decode($item->special_components, true);
                    
    //                         return $specialComponents ? array_map(function ($special_component) {
    //                             return '<li>' . $special_component . '</li>';
    //                         }, $specialComponents) : [];
    //                     })->implode('');
                    
    //                     return $data ? '<ul>' . $data . '</ul>' : 'Unavailable.';
    //                 })

    //                 ->addColumn('action', function ($jobs) {
    //                         if (Auth::user()->can('edit') && Auth::user()->can('delete') && Auth::user()->can('show')) {
    //                             return '<div class="table-actions text-center" style="display: flex;">
    //                                         <a href=" '.route('job.job.show', $jobs->job_id). ' " title="View">
    //                                             <i class="ik ik-eye f-16 mr-15 text-blue"></i>
    //                                         </a>
    //                                         <a href=" '.route('job.job.edit', $jobs->job_id). ' " title="View">
    //                                             <i class="ik ik-edit f-16 mr-15 text-green" title="Edit"></i>
    //                                         </a>
    //                                         <a type="submit" onclick="showDeleteConfirm(' . $jobs->job_id . ')" title="Delete"><i class="ik ik-trash-2 f-16 text-red"></i></a>
    //                                     </div>';
    //                         } elseif (Auth::user()->can('edit') && Auth::user()->can('show')) {
    //                             return '<div class="table-actions" style="display: flex;">
    //                                             <a href=" '.route('job.job.show', $jobs->job_id). ' " title="View">
    //                                                 <i class="ik ik-eye f-16 mr-15 text-blue"></i>
    //                                             </a>
    //                                             <a href=" '.route('job.job.edit', $jobs->job_id). ' " title="View">
    //                                                 <i class="ik ik-edit f-16 mr-15 text-green" title="Edit"></i>
    //                                             </a>
    //                                             </div>';
    //                         } elseif (Auth::user()->can('delete')) {
    //                             return '<div class="table-actions">
    //                                         <a type="submit" onclick="showDeleteConfirm(' . $jobs->job_id . ')" title="Delete"><i class="ik ik-trash-2 f-16 text-red"></i></a>
    //                                     </div>';
    //                         } elseif (Auth::user()->can('show')) {
    //                             return '<div class="table-actions">
    //                                         <a href=" '.route('job.job.show', $jobs->job_id). ' " title="View">
    //                                             <i class="ik ik-eye f-16 mr-15 text-blue"></i>
    //                                         </a>
    //                                     </div>';
    //                         } 
    //                 })
    //                 ->addIndexColumn()
    //                 ->rawColumns(['ticket_sl','job_number','service_type','warranty_type','status','job_pending_remark','pending_for_special_components','action'])
    //                 ->make(true);
    //         }
    //         return view('job.index', compact('totalJobStatus'));
    //     } catch (\Exception $e) {
    //         $bug = $e->getMessage();
    //         return redirect()->back()->with('error', $bug);
    //     }
    // }

    //Technician's Job
    // public function employeeJobs(Request $request)
    // {
    //     try{
    //         $auth = Auth::user();
    //         $user_role = $auth->roles->first();
    //         if ($user_role->name == 'Super Admin' || $user_role->name == 'Admin' || $user_role->name =='Team Leader Admin') {
    //             $totalJobStatus = $this->jobTotalstatus();
    //         } elseif ($user_role->name == 'Team Leader') {
    //             $totalJobStatus = $this->jobTotalStatusByTeam($auth->id);
    //         } else {
    //             $totalJobStatus = $this->jobTotalStatusByUser(Auth::user()->id);
    //         }
            
    //         if (request()->ajax()) {
    //             $employee = Employee::where('user_id', Auth::user()->id)->first();
    //             $serviceTypes = ServiceType::where('status', 1)->get();
    //             $data=DB::table('jobs')
    //             ->join('employees', 'jobs.employee_id', '=', 'employees.id')
    //             ->join('users', 'jobs.created_by', '=', 'users.id')
    //             ->join('tickets', 'jobs.ticket_id', '=', 'tickets.id')
    //             ->join('job_priorities', 'tickets.job_priority_id', '=', 'job_priorities.id')
    //             ->join('outlets','tickets.outlet_id','=','outlets.id')
    //             ->join('purchases','tickets.purchase_id','=','purchases.id')
    //             ->join('categories','tickets.product_category_id','=','categories.id')
    //             ->join('brand_models','purchases.brand_model_id', '=', 'brand_models.id')
    //             ->join('brands','purchases.brand_id', '=', 'brands.id')
    //             ->join('customers','purchases.customer_id', '=', 'customers.id')
    //             ->leftjoin('warranty_types','tickets.warranty_type_id', '=', 'warranty_types.id')
    //             ->select('jobs.id as job_id','jobs.job_number as job_number','jobs.date as assigning_date','jobs.created_at as job_created_at','employees.name as employee_name','employees.vendor_id as vendor_id','brand_models.model_name as model_name','brands.name as brand_name',
    //             'categories.name as product_category','users.name as created_by','customers.name as customer_name', 'customers.mobile as customer_mobile','purchases.product_serial as product_serial','purchases.invoice_number as invoice_number','purchases.purchase_date as purchase_date',
    //             'tickets.id as ticket_id','tickets.created_at as created_at','outlets.name as outlet_name','tickets.service_type_id as service_type_id','tickets.status as ticket_status',
    //             'tickets.is_reopened as is_reopened','tickets.is_accepted as is_accepted','tickets.is_pending as ticket_is_pending','tickets.is_paused as ticket_is_paused','tickets.is_ended as ticket_is_ended',
    //             'tickets.is_started as ticket_is_started','tickets.is_closed_by_teamleader as is_closed_by_teamleader','tickets.is_delivered_by_teamleader as is_delivered_by_teamleader',
    //             'tickets.is_delivered_by_call_center as is_delivered_by_call_center','tickets.is_closed as is_closed','tickets.is_assigned as is_assigned',
    //             'tickets.is_rejected as is_rejected','jobs.status as status','jobs.is_pending as is_pending','jobs.is_paused as is_paused','jobs.is_started as is_started','jobs.is_ended as is_ended','job_priorities.job_priority','tickets.outlet_id as outlet_id',
    //             'warranty_types.warranty_type as warranty_type','purchases.outlet_id as outletid')
    //             ->whereIn('jobs.status',[0,1,3,5])
    //             ->where('jobs.deleted_at',null);

    //             if ($user_role->name == 'Super Admin' || $user_role->name == 'Admin' || $user_role->name =='Team Leader Admin') {
    //                 $data;
    //             } elseif ($user_role->name == 'Team Leader') {
    //                 $data->where('jobs.created_by',Auth::user()->id);
    //             } else {
    //                 $data->where('jobs.user_id',Auth::user()->id);
    //             }

    //             if(!empty($request->start_date && $request->end_date))
    //             {
    //                 $startDate=Carbon::parse($request->get('start_date'))->format('Y-m-d');
    //                 $endDate=Carbon::parse($request->get('end_date'))->addDay()->format('Y-m-d');
    //                 $jobs=$data->whereBetween('jobs.created_at',[$startDate, $endDate])->latest()->get();
    //             } 
    //             else{
    //                 $jobs=$data->latest()->get();
    //             }
    //             return DataTables::of($jobs)

    //                 ->addColumn('emplyee_name', function ($jobs) {
    //                     $employee_name=$jobs->employee_name ?? null;
    //                     return $employee_name;
    //                 })

    //                 ->addColumn('outlet_name', function ($jobs) {
    //                     $outlet_name=$jobs->outlet_name ?? Null;
    //                     return $outlet_name;
    //                 })

    //                 ->addColumn('ticket_sl', function ($jobs) {
    //                     return '<a href="'.route('show-ticket-details', $jobs->ticket_id).'" class="badge badge-primary" title="Ticket Details">'.'TSL-'.''. $jobs->ticket_id.'</a>';
    //                 })
                    
    //                 ->addColumn('ticket_created_at', function ($jobs) {
    //                     $ticket_created_at=Carbon::parse($jobs->created_at)->format('m/d/Y');  
                         
    //                     return $ticket_created_at;
    //                 })
    //                 ->addColumn('purchase_date', function ($jobs) {
    //                     $purchase_date=Carbon::parse($jobs->purchase_date)->format('m/d/Y');                        
    //                     return $purchase_date;
    //                 })
    //                 ->addColumn('job_number', function ($jobs) {
    //                     $job_number='JSL-'.$jobs->job_id; 
    //                     return $job_number;
    //                 })
    //                 ->addColumn('service_type', function($jobs) use($serviceTypes){
    //                     $selectedServiceTypeIds=json_decode($jobs->service_type_id);
    //                     $data='';
    //                     foreach ($serviceTypes as $key => $serviceType) {
    //                        if (in_array($serviceType->id, $selectedServiceTypeIds)) {
    //                            $data=$serviceType->service_type;
    //                        }
    //                     }
    //                     return $data;
    //                })
    //                ->addColumn('warranty_type', function ($jobs) {
    //                     $warranty_type=$jobs->warranty_type ?? null; 
    //                     return $warranty_type;
    //                 })
    //                 ->addColumn('assigning_date', function ($jobs) {
    //                     $assigning_date=Carbon::parse($jobs->assigning_date)->format('m/d/Y');    
    //                     return $assigning_date;
    //                 })
    //                 ->addColumn('created_by', function ($jobs) {
    //                     $created_by=$jobs->created_by; 
    //                     return $created_by;
    //                 })
    //                 ->addColumn('product_category', function ($jobs) {
    //                     $product_category=$jobs->product_category ?? Null;
    //                     return $product_category;
    //                 })
    //                 ->addColumn('brand_name', function ($jobs) {
    //                     $brand_name=$jobs->brand_name ?? Null;
    //                     return $brand_name;
    //                 })
    //                 ->addColumn('model_name', function ($jobs) {
    //                     $model_name=$jobs->model_name ?? Null;
    //                     return $model_name;
    //                 })
    //                 ->addColumn('product_serial', function ($jobs) {
    //                     $product_serial=$jobs->product_serial ?? Null;
    //                     return $product_serial;
    //                 })
    //                 ->addColumn('point_of_purchase', function($tickets){
    //                     $point_of_purchase=Outlet::where('id', '=', $tickets->outletid)->first();
    //                         return $point_of_purchase->name ?? null;
    //                 })
    //                 ->addColumn('invoice_number', function ($jobs) {
    //                     $invoice_number=$jobs->invoice_number;
    //                     return $invoice_number;
    //                 })
    //                 ->addColumn('customer_name', function ($jobs) {
    //                     $invoice_number=$jobs->customer_name;
    //                     return $invoice_number;
    //                 })
    //                 ->addColumn('customer_mobile', function ($jobs) {
    //                     $invoice_number=$jobs->customer_mobile;
    //                     return $invoice_number;
    //                 })
    //                 ->addColumn('technician_type', function ($jobs) {
    //                     $tech_type='';
    //                     if ($jobs->vendor_id != null) {
    //                         $tech_type='Vendor';
    //                     }else{
    //                         $tech_type='Own';
    //                     }
    //                     return $tech_type;
    //                 })
    //                 ->addColumn('job_priority', function($jobs){
    //                     $job_priority=$jobs->job_priority?? Null;
    //                     return $job_priority;
    //                 })
    //                 ->addColumn('status', function ($jobs) {
    //                     switch ($jobs->status) {
    //                         case 6:
    //                             $badgeClass = 'badge-red';
    //                             $statusText = 'Paused';
    //                             break;
                    
    //                         case 5:
    //                             $badgeClass = 'badge-orange';
    //                             $statusText = 'Pending';
    //                             break;
                    
    //                         case 0:
    //                             $badgeClass = 'badge-yellow';
    //                             $statusText = 'Created';
    //                             break;
                    
    //                         case 4:
    //                             $badgeClass = 'badge-info';
    //                             $statusText = 'Job Completed';
    //                             break;
                    
    //                         case 3:
    //                             $badgeClass = 'badge-success';
    //                             $statusText = 'Job Started';
    //                             break;
                    
    //                         case 1:
    //                             $badgeClass = 'badge-success';
    //                             $statusText = 'Accepted';
    //                             break;
                    
    //                         case 2:
    //                             $badgeClass = 'badge-danger';
    //                             $statusText = 'Rejected';
    //                             break;
                    
    //                         default:
    //                             $badgeClass = '';
    //                             $statusText = 'Unknown';
    //                     }
                    
    //                     return $badgeClass ? "<span class=\"badge $badgeClass\">$statusText</span>" : '';
    //                 })
                    
    //                 ->addColumn('job_created_at', function ($jobs) {
    //                     $job_created_at=Carbon::parse($jobs->job_created_at)->format('m/d/Y');
    //                     return $job_created_at;
    //                 })

    //                 ->addColumn('job_pending_remark', function ($jobs) {
    //                     $pendingNotes = DB::table('job_pending_notes')->where('job_id', $jobs->job_id)->get();
                    
    //                     $data = collect($pendingNotes)->map(function ($item) {
    //                         return '<ol style="font-weight: bold; color:red">' . $item->job_pending_remark . '-' . $item->job_pending_note . '</ol>';
    //                     })->implode('');
                    
    //                     return $data ?: 'Unavailable.';
    //                 })
    //                 ->addColumn('pending_for_special_components', function ($jobs) {
    //                     $pendingNotes = DB::table('job_pending_notes')->where('job_id', $jobs->job_id)->get();
                    
    //                     $data = collect($pendingNotes)->flatMap(function ($item) {
    //                         $specialComponents = json_decode($item->special_components, true);
                    
    //                         return $specialComponents ? array_map(function ($special_component) {
    //                             return '<li>' . $special_component . '</li>';
    //                         }, $specialComponents) : [];
    //                     })->implode('');
                    
    //                     return $data ? '<ul>' . $data . '</ul>' : 'Unavailable.';
    //                 })
                    

    //                 ->addColumn('action', function ($jobs) {
    //                         if (Auth::user()->can('show')) {
    //                             return '<div class="table-actions text-center" style="display: flex;">
    //                                         <a href=" '.route('technician.jobs.show', $jobs->job_id). ' " title="View">
    //                                             <i class="ik ik-eye f-16 mr-15 text-blue"></i>
    //                                             </a>
    //                                     </div>';
    //                         }
    //                 })
    //                 ->addIndexColumn()
    //                 ->rawColumns(['ticket_sl','job_number','service_type','warranty_type','status','job_pending_remark','pending_for_special_components','action'])
    //                 ->make(true);
    //         }
    //         return view('job.technician-index', compact('totalJobStatus'));
    //     } catch (\Exception $e) {
    //         $bug = $e->getMessage();
    //         return redirect()->back()->with('error', $bug);
    //     }
    // }
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
            if ($user_role->name == 'Super Admin' || $user_role->name == 'Admin' || $user_role->name =='Call Center Admin') {
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
                $trim=trim($job_list->id,"JSL-");
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
            'note' => 'required',
            // 'job_number' => 'required|unique:jobs,job_number,NULL,id,deleted_at,NULL',
            'user_id' => 'required',
        ]);

        try{
            DB::beginTransaction();
            $job_number = $this->generateUniqueJobSl();
            $ticket = Ticket::findOrFail($request->ticket_id);
            $employee=Employee::where('user_id',$request->user_id)->first();


          $job=Job::create([
                'purchase_id' =>  $request->purchase_id,
                'employee_id' =>  $employee->id,
                'user_id' =>  $request->user_id,
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
            if ($job) {
                $sma= "TSL and JSL : TSL-77777, JSL-13423, 
                 Customer Name Address and Contact: Md Jashim uddin, Mirpur, 01726259906, 
                 Product Model: KHV-635NF, 
                 Service Type: Home call, 
                 Fault Description: No cooling, 
                 Warranty Type: Full warranty, 
                 Job Creation Remarks: 14/8/23-4pm.";
                 
                 $serviceTypes = ServiceType::where('status', 1)->get();
                 $faults = Fault::where('status', 1)->get();
                 
                 $selectedServiceTypeIds=json_decode($ticket->service_type_id);
                 $service_types=null;
                 foreach ($serviceTypes as $key => $serviceType) {
                    if (in_array($serviceType->id, $selectedServiceTypeIds)) {
                        $service_types=$serviceType->service_type;
                    }
                 }
 
 
                 $faultId = json_decode($ticket->fault_description_id);
                 $fault_data=null;
                 foreach ($faults as $fault){
                     if ($faultId !=null){
                         if (in_array($fault->id, $faultId)){
                             $fault_data= $fault->name;
                         }
                        
                     }
                 }
                 $tsl_no ='TSL'.'-'.$ticket->id;
                 $jsl_no ='JSL'.'-'.$job->id;
                 $customer_name=$ticket->purchase->customer->name?? "Not Found";
                 $customer_phone=$ticket->purchase->customer->mobile?? "Not Found";
                 $customer_address=$ticket->purchase->customer->address?? "Not Found";
                 $product_model=$ticket->purchase->modelname->model_name ?? "Not Found";
                 $service_type=$service_types ?? null;
                 $fault_description=$fault_data ?? null;
                 $warranty_type=$ticket->warrantytype->warranty_type ?? null;
                 $job_remark=$job->note ?? null;
 
                 $text=$tsl_no.",". $jsl_no.",".$customer_name.",".$customer_address.",".$customer_phone.",".$product_model.",".$service_type.",".$fault_description.",".$warranty_type.",".$job_remark.".";
                 $phone = $employee->mobile;
                 $sms = $this->sendSms($phone, $text);     
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
            $specialComponents = SpecialComponent::orderBy('id', 'DESC')->get();
            $JobAttachment = JobAttachment::where('job_id',$job->id)->get();
            $submittedJobs=JobSubmission::where('job_id',$job->id)->latest()->get();
            return view('job.technician-show', compact(
                'job','faults','accessories_lists', 'allAccessories','allFaults', 'jobCloseRemarks', 'jobpendingRemarks' ,'customerAdvancedPayment','submittedJobs','JobAttachment','specialComponents'
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
            //'job_number' => 'required|unique:jobs,job_number,' . $id,
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
            DB::beginTransaction();
            $job=Job::find($id);
            $job->update([
                'status' => 1
            ]);
            $ticket = Ticket::where('id',$job->ticket_id)
            ->update([
                'status'=> 3,
                'is_accepted' => 1,
            ]);
            DB::commit();
            return redirect()->back()->with('success','Job Accepted Successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            $bug = $e->getMessage();
            return redirect()->back()->with('error', $bug);
        } 
    }
    public function startJob($id){
        try {
            DB::beginTransaction();
            $current = Carbon::now('Asia/Dhaka');
            $job=Job::find($id);
            $ticket = Ticket::where('id',$job->ticket_id)->first();
            $message='';
            if ($job->is_started == 1 && $job->is_paused == 1) {
                $job->update([
                    'is_paused' => 0,
                    'status' => 3,
                ]);
                $ticket->update([
                    'is_paused' => 0,
                    'status' => 4,
                ]);
                
                $message='Job is re-started successfully';
            }
            elseif($job->is_started == 1 && $job->is_paused == 0)
            {
                $job->update([
                    'is_paused' => 1,
                    'status' => 6,
                ]); 
                $ticket->update([
                    'status' => 5,
                    'is_paused' => 1,
                ]);
                $message='Job is paused successfully';
            }else{
                $job->update([
                    'status' => 3,
                    'is_started' => 1,
                    'job_start_time' => $current,
                ]);                
                $ticket->update([
                    'status' => 4,
                    'is_started' => 1,
                ]);
                $message='Job is started successfully';
            }

            DB::commit();
            return redirect()->back()->with('success',$message);
        } catch (\Exception $e) {
            DB::rollBack();
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
            DB::beginTransaction();
            $current = Carbon::now('Asia/Dhaka');
            $job=Job::find($id);

            $job->update([
                'status' => 4,
                'is_ended' => 1,
                'job_end_time' => $current,
                'job_ending_remark' => $request->remark,
                'job_close_remark' => $request->job_close_remark,
            ]);
            $ticket = Ticket::where('id',$job->ticket_id)
            ->update([
                'status' => 11,
                'is_ended' => 1,
            ]);
            DB::commit();
        return redirect()->back()->with('success','Job End Successfully');
        } catch (\Exception $e) {
            DB::rollback();
            $bug = $e->getMessage();
            return redirect()->back()->with('error', $bug);
        }
    }

    public function pendingJob($id, Request $request)
    {
        $this->validate($request, [
            'job_pending_remark' => 'required',
        ]);
        // dd($request->all());
        try {
            DB::beginTransaction();
            
            $job=Job::find($id);
            $job->update([
                'is_pending' => 1,
                'status' => 5,
            ]);

            $specialComponents = $request->input('special_components', []);
            $jsonSpecialComponents = json_encode($specialComponents);

            JobPendingNote::create([
                'job_id' => $id,
                'job_pending_note' => $request->remark,
                'job_pending_remark' => $request->job_pending_remark, 
                'special_components' => $jsonSpecialComponents, 
            ]);

            $ticket = Ticket::where('id',$job->ticket_id)
            ->update([
                'status' => 6,
                'is_pending' => 1,
            ]);
            DB::commit();
        return redirect()->back()->with('success','Pending Note Added Successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            $bug = $e->getMessage();
            return redirect()->back()->with('error', $bug);
        }
    }
    public function denyJob(Request $request){
        $this->validate($request, [
            'reject_note' => 'required',
        ]);

        try {
            DB::beginTransaction();

            $job=Job::find($request->job_id);
            $job->status = 2;
            $job->save();

            $ticket = Ticket::where('id',$job->ticket_id)
            ->update([
                'status' => 2,
                'is_assigned'=>0,
                'is_rejected' => 1,
            ]);

            $jobNote=new JobNote();
            $jobNote->job_id       = $request->job_id;
            $jobNote->decline_note = $request->reject_note;
            $jobNote->save();
            DB::commit();
            return redirect()->back()->with('error','Job Rejected');
        } catch (\Exception $e) {
            DB::rollBack();
            $bug = $e->getMessage();
            return redirect()->back()->with('error', $bug);
        }
    }

    protected function jobTotalstatus()
    {
        return DB::table('jobs')
            ->selectRaw("count(case when status = 5 and deleted_at IS NULL then 1 end) as pending")
            ->selectRaw("count(case when status = 6 and deleted_at IS NULL then 1 end) as jobPaused")
            ->selectRaw("count(case when status = 0 and deleted_at IS NULL then 1 end) as jobCreated")
            ->selectRaw("count(case when status = 4 and deleted_at IS NULL then 1 end) as jobCompleted")
            ->selectRaw("count(case when status = 3 and deleted_at IS NULL then 1 end) as jobStrated")
            ->selectRaw("count(case when status = 1 and deleted_at IS NULL then 1 end) as jobAccepted")
            ->selectRaw("count(case when status = 2 and deleted_at IS NULL then 1 end) as jobRejected")
            ->selectRaw("count(case when deleted_at IS NULL then 1 end) as totalJob")
            ->first();
    }
    // For Teamleader
    protected function jobTotalStatusByTeam($authId)
    {
        return DB::table('jobs')
            ->selectRaw("count(case when created_by = $authId and status = 5 and deleted_at IS NULL then 1 end) as pending")
            ->selectRaw("count(case when created_by = $authId and status = 6 and deleted_at IS NULL then 1 end) as jobPaused")
            ->selectRaw("count(case when created_by = $authId and status = 0 and deleted_at IS NULL then 1 end) as jobCreated")
            ->selectRaw("count(case when created_by = $authId and status = 4 and deleted_at IS NULL then 1 end) as jobCompleted")
            ->selectRaw("count(case when created_by = $authId and status = 3 and deleted_at IS NULL then 1 end) as jobStrated")
            ->selectRaw("count(case when created_by = $authId and status = 1 and deleted_at IS NULL then 1 end) as jobAccepted")
            ->selectRaw("count(case when created_by = $authId and status = 2 and deleted_at IS NULL then 1 end) as jobRejected")
            ->selectRaw("count(case when created_by = $authId and deleted_at IS NULL then 1 end) as totalJob")
            ->first();
    }

    // For Technician
    protected function jobTotalStatusByUser($userId)
    {
        return DB::table('jobs')
            ->selectRaw("count(case when user_id = $userId and status = 5 and deleted_at IS NULL then 1 end) as pending")
            ->selectRaw("count(case when user_id = $userId and status = 6 and deleted_at IS NULL then 1 end) as jobPaused")
            ->selectRaw("count(case when user_id = $userId and status = 0 and deleted_at IS NULL then 1 end) as jobCreated")
            ->selectRaw("count(case when user_id = $userId and status = 4 and deleted_at IS NULL then 1 end) as jobCompleted")
            ->selectRaw("count(case when user_id = $userId and status = 3 and deleted_at IS NULL then 1 end) as jobStrated")
            ->selectRaw("count(case when user_id = $userId and status = 1 and deleted_at IS NULL then 1 end) as jobAccepted")
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
    public function status(Request $request, $id)
    {
        try {
            $auth = Auth::user();
            $user_role = $auth->roles->first();

            if ($user_role->name == 'Team Leader') {
                $totalJobStatus = $this->jobTotalStatusByTeam($auth->id);
            } elseif ($user_role->name == 'Technician') {
                $totalJobStatus = $this->jobTotalStatusByUser(Auth::user()->id);
            }else{
                $totalJobStatus = $this->jobTotalstatus();
            }

            if (request()->ajax()) {
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
                ->where('jobs.deleted_at',null);
                if ($user_role->name == 'Team Leader') {
                    $data->where('jobs.created_by',Auth::user()->id);
                } elseif ($user_role->name == 'Technician') {
                    $data->where('jobs.user_id',Auth::user()->id);
                }else{
                    $data;
                }
                switch($id) {
                    case 1:
                        $data->where('jobs.status','=',5);
                        break;
                    case 2:
                        $data->where('jobs.status','=',6);
                        break;
    
                    case 3:
                        $data->where('jobs.status','=',0);
                        break;
    
                    case 4:
                        $data->where('jobs.status','=',4);
                        break;
                        
                    case 5:
                        $data->where('jobs.status','=',3);
                        break;
                    case 6:
                        $data->where('jobs.status','=',1);
                        break;
    
                    case 7:
                        $data->where('jobs.status','=',2);
                        break;
    
                    case 8:
                        $data;
                     break;
                     
                    default:
                        return redirect()->route('technician.jobs');
                }
                if(!empty($request->start_date && $request->end_date))
                {
                    $startDate=Carbon::parse($request->get('start_date'))->format('Y-m-d');
                    $endDate=Carbon::parse($request->get('end_date'))->addDay()->format('Y-m-d');
                    $jobs=$data->whereBetween('jobs.created_at',[$startDate, $endDate])->latest()->get();
                } 
                else{
                    $jobs=$data->latest()->get();
                }
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
                    ->addColumn('status', function ($jobs) {
                        switch ($jobs->status) {
                            case 6:
                                $badgeClass = 'badge-red';
                                $statusText = 'Paused';
                                break;
                    
                            case 5:
                                $badgeClass = 'badge-orange';
                                $statusText = 'Pending';
                                break;
                    
                            case 0:
                                $badgeClass = 'badge-yellow';
                                $statusText = 'Created';
                                break;
                    
                            case 4:
                                $badgeClass = 'badge-info';
                                $statusText = 'Job Completed';
                                break;
                    
                            case 3:
                                $badgeClass = 'badge-success';
                                $statusText = 'Job Started';
                                break;
                    
                            case 1:
                                $badgeClass = 'badge-success';
                                $statusText = 'Accepted';
                                break;
                    
                            case 2:
                                $badgeClass = 'badge-danger';
                                $statusText = 'Rejected';
                                break;
                    
                            default:
                                $badgeClass = '';
                                $statusText = 'Unknown';
                        }
                    
                        return $badgeClass ? "<span class=\"badge $badgeClass\">$statusText</span>" : '';
                    })
                    
                    ->addColumn('job_created_at', function ($jobs) {
                        $job_created_at=Carbon::parse($jobs->job_created_at)->format('m/d/Y');
                        return $job_created_at;
                    })

                    ->addColumn('job_pending_remark', function ($jobs) {
                        $pendingNotes = DB::table('job_pending_notes')->where('job_id', $jobs->job_id)->get();
                    
                        $data = collect($pendingNotes)->map(function ($item) {
                            return '<ol style="font-weight: bold; color:red">' . $item->job_pending_remark . '-' . $item->job_pending_note . '</ol>';
                        })->implode('');
                    
                        return $data ?: 'Unavailable.';
                    })
                    ->addColumn('pending_for_special_components', function ($jobs) {
                        $pendingNotes = DB::table('job_pending_notes')->where('job_id', $jobs->job_id)->get();
                    
                        $data = collect($pendingNotes)->flatMap(function ($item) {
                            $specialComponents = json_decode($item->special_components, true);
                    
                            return $specialComponents ? array_map(function ($special_component) {
                                return '<li>' . $special_component . '</li>';
                            }, $specialComponents) : [];
                        })->implode('');
                    
                        return $data ? '<ul>' . $data . '</ul>' : 'Unavailable.';
                    })
                    ->addColumn('action', function ($jobs) use ($user_role) {
                        $html = '<div class="table-actions';
                        
                        if (($user_role->name == 'Super Admin' || $user_role->name == 'Admin' || $user_role->name == 'Team Leader') && Auth::user()->can('show')) {
                            $html .= ' text-center" style="display: flex;">';
                            $html .= '<a href="'.route('job.job.show', $jobs->job_id).'" title="View"><i class="ik ik-eye f-16 mr-15 text-green"></i></a>';
                    
                            if (Auth::user()->can('edit')) {
                                $html .= '<a href="'.route('job.job.edit', $jobs->job_id).'" title="Edit"><i class="ik ik-edit f-16 mr-15 text-blue"></i></a>';
                            }
                    
                            if (Auth::user()->can('delete') && $jobs->status != 0) {
                                $html .= '<a type="submit" onclick="showDeleteConfirm('.$jobs->job_id.')" title="Delete"><i class="ik ik-trash-2 f-16 text-red"></i></a>';
                            }
                        } elseif (Auth::user()->can('show')) {
                            $html .= '">';
                            $html .= '<a href="'.route('technician.jobs.show', $jobs->job_id).'" title="View"><i class="ik ik-eye f-16 mr-15 text-green"></i></a>';
                        }
                    
                        $html .= '</div>';
                        return $html;
                    })
                    
                    // ->addColumn('action', function ($jobs) use ($user_role) {
                    //         if (($user_role->name == 'Super Admin' || $user_role->name == 'Admin' || $user_role->name == 'Team Leader') && Auth::user()->can('edit') && Auth::user()->can('delete') && Auth::user()->can('show')) {
                    //             return '<div class="table-actions text-center" style="display: flex;">
                    //                         <a href=" '.route('job.job.show', $jobs->job_id). ' " title="View">
                    //                             <i class="ik ik-eye f-16 mr-15 text-green"></i>
                    //                         </a>
                    //                         <a href=" '.route('job.job.edit', $jobs->job_id). ' " title="View">
                    //                             <i class="ik ik-edit f-16 mr-15 text-blue" title="Edit"></i>
                    //                         </a>
                    //                         <a type="submit" onclick="showDeleteConfirm(' . $jobs->job_id . ')" title="Delete"><i class="ik ik-trash-2 f-16 text-red"></i></a>
                    //                     </div>';
                    //         } elseif (($user_role->name == 'Super Admin' || $user_role->name == 'Admin' || $user_role->name == 'Team Leader') && Auth::user()->can('edit') && Auth::user()->can('show')) {
                    //             return '<div class="table-actions" style="display: flex;">
                    //                             <a href=" '.route('job.job.show', $jobs->job_id). ' " title="View">
                    //                                 <i class="ik ik-eye f-16 mr-15 text-green"></i>
                    //                             </a>
                    //                             <a href=" '.route('job.job.edit', $jobs->job_id). ' " title="View">
                    //                                 <i class="ik ik-edit f-16 mr-15 text-blue" title="Edit"></i>
                    //                             </a>
                    //                             </div>';
                    //         } elseif (($user_role->name == 'Super Admin' || $user_role->name == 'Admin' || $user_role->name == 'Team Leader') && Auth::user()->can('delete') && $jobs->status !=0) {
                    //             return '<div class="table-actions">
                    //                         <a type="submit" onclick="showDeleteConfirm(' . $jobs->job_id . ')" title="Delete"><i class="ik ik-trash-2 f-16 text-red"></i></a>
                    //                     </div>';
                    //         } elseif (Auth::user()->can('show')) {
                    //             return '<div class="table-actions">
                    //                     <a href=" '.route('technician.jobs.show', $jobs->job_id). ' " title="View">
                    //                             <i class="ik ik-eye f-16 mr-15 text-green"></i>
                    //                         </a>
                    //                     </div>';
                    //         } 
                    // })
                    ->addIndexColumn()
                    ->rawColumns(['ticket_sl','job_number','service_type','status','job_pending_remark','pending_for_special_components','action'])
                    ->make(true);
            }
            return view('job.job_status', compact('totalJobStatus', 'id'));
        }catch (\Exception $e) {
            $bug = $e->getMessage();
            return redirect()->back()->with('error', $bug);
        }
    }
    //Print
    public function claim($id)
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
            $serviceTypes= ServiceType::where('status', 1)->get();
            $product_conditions = ProductCondition::where('status', 1)->get();
            return view('job.claim', compact(
                'job','faults','accessories_lists', 'allAccessories','allFaults', 'serviceTypes','product_conditions'
            ));
        } catch (\Exception $e) {
            $bug = $e->getMessage();
            return redirect()->back()->with('error', $bug);
        }
    }
    //
    public function slip($id)
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
            $serviceTypes= ServiceType::where('status', 1)->get();
            $product_conditions = ProductCondition::where('status', 1)->get();
            return view('job.slip', compact(
                'job','faults','accessories_lists', 'allAccessories','allFaults', 'serviceTypes','product_conditions'
            ));
        } catch (\Exception $e) {
            $bug = $e->getMessage();
            return redirect()->back()->with('error', $bug);
        }
    }
    //Excel Download
    public function jobExcelDownload($id)
    {
        try {
            $status='';
            if ($id==1)
            {
                $status='Pending';
            }
			else if($id == 2){
                $status='Paused';
            }
			elseif($id == 3){
                $status='Created';
            }
			else if($id == 4){
                $status='Completed';
            }
			else if($id == 5){
                $status='Started';
            }
			else if($id == 6){
                $status='Accepted';
            }
			else if($id == 7){
                $status='Rejected';
            }
			else if($id == 8){
                $status='All';
            }
            return Excel::download(new JobExport($id,$status), 'Jobs'.'-'.$status .'.xlsx');
        } catch (\Exception $e) {
            $bug = $e->getMessage();
            return redirect()->back()->with('error', $bug);
        }
    }

    //Technician Job Status
    public function techNicianJobStatus($employee)
    {
        $jobs=DB::table('jobs')->where('user_id',$employee)->where('status','!=', 4)->where('deleted_at',null)
        ->select( DB::raw('count(status) as count, status') )->
        groupBy('status')->get();
        // ->get();
        return response()->json($jobs);
    }

    public function csvTest()
    {
        try {
            $id=8;
            $status='';
            if ($id==1)
            {
                $status='Pending';
            }
			else if($id == 2){
                $status='Paused';
            }
			elseif($id == 3){
                $status='Created';
            }
			else if($id == 4){
                $status='Completed';
            }
			else if($id == 5){
                $status='Started';
            }
			else if($id == 6){
                $status='Accepted';
            }
			else if($id == 7){
                $status='Rejected';
            }
			else if($id == 8){
                $status='All';
            }
            // get the request value
            $input = request()->all();

            // set header
            $columns = [
                'Sl',
                'Technician',
                'Technician Type',
                'Branch',
                'Ticket SL',
                'Ticket Created At',
                'Customer Name',
                'Customer Phone',
                'Purchase Date',
                'Job Number',
                'Service Type',
                'Warranty Type',
                'Assigned Date',
                'Assigned By',
                'Job Priorty',
                'Product Category',
                'Brand Name',
                'Product Name',
                'Product Serial',
                'Invoice Number',
                'Job Status',
                'Created At',
                'Job Pending Note',
            ];

            // create csv
            return response()->streamDownload(function() use($columns, $input, $id) {
                $file = fopen('php://output', 'w+');
                fputcsv($file, $columns);

                $auth = Auth::user();
                $user_role = $auth->roles->first();
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
                    'tickets.is_rejected as is_rejected','jobs.status as status','jobs.is_pending as is_pending','jobs.is_paused as is_paused','jobs.is_started as is_started','jobs.is_ended as is_ended','job_priorities.job_priority','tickets.outlet_id as outlet_id','warranty_types.warranty_type as warranty_type')
                ->where('jobs.deleted_at',null);
                if ($user_role->name == 'Team Leader') {
                    $data->where('jobs.created_by',Auth::user()->id);
                } elseif ($user_role->name == 'Technician') {
                    $data->where('jobs.user_id',Auth::user()->id);
                }else{
                    $data;
                }
                switch($id) {
                    case 1:
                        $data->where('jobs.status','=',5);
                        break;
                    case 2:
                        $data->where('jobs.status','=',6);
                        break;

                    case 3:
                        $data->where('jobs.status','=',0);
                        break;

                    case 4:
                        $data->where('jobs.status','=',4);
                        break;
                        
                    case 5:
                        $data->where('jobs.status','=',3);
                        break;
                    case 6:
                        $data->where('jobs.status','=',1);
                        break;

                    case 7:
                        $data->where('jobs.status','=',2);
                        break;

                    case 8:
                        $data;
                    break;
                    
                    default:
                        return redirect()->route('technician.jobs');
                };

                $data->orderBy('job_id')->chunk(500, function($data) use($file,$serviceTypes) {
                    foreach ($data as $job) {
                        // Add a new row with data
                        $selectedServiceTypeIds=json_decode($job->service_type_id);
                        $service_type_data='N/A';
                        $pending_notes=null;
                        $status=null;
                        $pendingNotes=DB::table('job_pending_notes')->where('job_id',$job->job_id)->get();
                        foreach ($serviceTypes as $key => $serviceType) {
                            if (in_array($serviceType->id, $selectedServiceTypeIds)) {
                                $service_type_data=$serviceType->service_type;
                                }
                            }
                        foreach ($pendingNotes as $key => $item) {
                            $pending_notes.= $item->job_pending_remark.'-'.$item->job_pending_note;
                        }
                       
                        if ($job->status == 6 )
                        {
                            $status='Paused';
                        }
                            
                        else if($job->status == 5)
                        {
                            $status='Pending';
                        }
                        
                        else if($job->status == 0)
                        {
                            $status='Created';
                        }
                        
                        else if($job->status == 4)
                        {
                            $status='Completed';
                        }
                       
                        else if($job->status == 3)
                        {
                            $status='Started';
                        }
                        
                        else if($job->status == 1)
                        {
                            $status='Accepted';
                        }
                       
                        else if($job->status == 2)
                        {
                            $status='Rejected';
                        }
                        $sl=1;
                        fputcsv($file, [
                        $sl,
                        $job->employee_name,
                        $job->vendor_id ? 'Vendor':'Own',
                        $job->outlet_name ?? null,
                        'TSL-'.$job->ticket_id ?? null,
                        Carbon::parse($job->created_at)->format('m/d/Y')  ?? null,
                        $job->customer_name ?? null,
                        $job->customer_mobile ?? null,
                        Carbon::parse($job->purchase_date)->format('m/d/Y')  ?? null,
                        'JSL-'.$job->job_id,
                        $service_type_data,
                        $job->warranty_type,
                        Carbon::parse($job->assigning_date)->format('m/d/Y'),
                        $job->created_by,
                        $job->job_priority,
                        $job->product_category,
                        $job->brand_name,
                        $job->model_name,
                        $job->product_serial,
                        $job->invoice_number,

                        $status,
                        Carbon::parse($job->job_created_at)->format('m/d/Y'),
                        $pending_notes
                        ]);
                    }
                });

                fclose($file);
            }, $status.'-Job'.date('d-m-Y').'.csv');
        } catch (\Exception $e) {
            $bug = $e->getMessage();
            return redirect()->back()->with('error', $bug);
        }
    }
    
}
