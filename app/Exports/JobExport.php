<?php

namespace App\Exports;

use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Illuminate\Support\Facades\Auth;
use App\Models\Ticket\ServiceType;
use App\Models\JobModel\Job;
use Illuminate\Support\Facades\DB;

class JobExport implements FromView, ShouldAutoSize
{
    /**
    * @return \Illuminate\Support\Collection
    */
    protected $id;

	public function __construct($id,$status) {
	    $this->id = $id;
	    $this->status = $status;
	}
    public function view(): View
    {
        $auth = Auth::user();
        $user_role = $auth->roles->first();
        $serviceTypes = ServiceType::where('status', 1)->get();
        $status=$this->status;
        try {
            set_time_limit(0);
            $data=DB::table('jobs')
            ->leftjoin('employees', 'jobs.employee_id', '=', 'employees.id')
            ->leftjoin('users', 'jobs.created_by', '=', 'users.id')
            ->leftjoin('tickets', 'jobs.ticket_id', '=', 'tickets.id')
            ->leftjoin('job_priorities', 'tickets.job_priority_id', '=', 'job_priorities.id')
            ->leftjoin('outlets','tickets.outlet_id','=','outlets.id')
            ->leftjoin('purchases','tickets.purchase_id','=','purchases.id')
            ->leftjoin('categories','tickets.product_category_id','=','categories.id')
            ->leftjoin('brand_models','purchases.brand_model_id', '=', 'brand_models.id')
            ->leftjoin('brands','purchases.brand_id', '=', 'brands.id')
            ->leftjoin('customers','purchases.customer_id', '=', 'customers.id')
            ->leftjoin('warranty_types','tickets.warranty_type_id', '=', 'warranty_types.id')
            ->select('jobs.id as job_id','jobs.job_number as job_number','jobs.date as assigning_date','jobs.job_end_time as job_end_time','employees.name as employee_name','employees.vendor_id as vendor_id','brand_models.model_name as model_name','brands.name as brand_name',
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
            
            switch($this->id) {
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
            $jobs=$data->latest()->get();
            $date = Carbon::now()->format('m/d/Y');
            return view('job.job_status_excel', compact('serviceTypes','jobs','status','date'));
        }catch (\Exception $e) {
            $bug = $e->getMessage();
            dd($bug);
            return redirect()->back()->with('error', $bug);
        }
    }
}
