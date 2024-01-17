<?php

namespace App\Exports;

use Illuminate\Support\Facades\Auth;
// use App\Models\Ticket;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use App\Models\Ticket\ServiceType;
use App\Models\Employee\Employee;
use App\Models\Employee\TeamLeader;
use App\Models\Outlet\Outlet;
use App\Models\Ticket\Ticket;
use Illuminate\Support\Facades\DB;


class ExportTicket implements FromView, ShouldAutoSize
{
    /**
    * @return \Illuminate\Support\Collection
    */
    protected $id;

	public function __construct($id,$status) {
	    $this->id = $id;
	    $this->status = $status;
	}

    // public function collection()
    // {
    //     return Ticket::all();
    // }
    public function view(): View
    {
        $auth = Auth::user();
        $user_role = $auth->roles->first();
        $serviceTypes = ServiceType::where('status', 1)->get();
        $employee=Employee::where('user_id',Auth::user()->id)->first();
        $status=$this->status;
        try {
            $data=DB::table('tickets')
            ->leftJoin('warranty_types','tickets.warranty_type_id','=','warranty_types.id')
            ->leftJoin('outlets','tickets.outlet_id','=','outlets.id')
            ->leftJoin('purchases','tickets.purchase_id','=','purchases.id')
            ->leftJoin('categories','tickets.product_category_id','=','categories.id')
            ->leftJoin('brand_models','purchases.brand_model_id', '=', 'brand_models.id')
            ->leftJoin('customers','purchases.customer_id', '=', 'customers.id')
            ->leftJoin('districts','tickets.district_id', '=','districts.id' )
            ->leftJoin('thanas','thanas.id', '=', 'tickets.thana_id')
            ->leftJoin('users', 'tickets.created_by', '=', 'users.id')
            ->select('users.name as created_by','brand_models.model_name as product_name','categories.name as product_category',
            'customers.name as customer_name', 'customers.mobile as customer_mobile', 'districts.name as district','thanas.name as thana',
            'purchases.product_serial as product_serial','purchases.invoice_number as invoice_number','tickets.id as ticket_id','tickets.created_at as created_at','outlets.name as outlet_name',
            'tickets.service_type_id as service_type_id','tickets.status as status','tickets.is_reopened as is_reopened','tickets.is_accepted as is_accepted','tickets.is_pending as is_pending',
            'tickets.is_paused as is_paused','tickets.is_ended as is_ended','tickets.is_started as is_started','tickets.is_closed_by_teamleader as is_closed_by_teamleader',
            'tickets.is_delivered_by_teamleader as is_delivered_by_teamleader','tickets.is_delivered_by_call_center as is_delivered_by_call_center','tickets.is_closed as is_closed',
            'tickets.is_assigned as is_assigned','tickets.is_rejected as is_rejected','tickets.delivery_date_by_call_center as delivery_date_by_call_center','purchases.outlet_id as outletid','warranty_types.warranty_type')
            ->where('tickets.deleted_at',null);

            if ($user_role->name == 'Team Leader') {
                $teamleader=TeamLeader::where('user_id', Auth::user()->id)->first();
                if(empty($teamleader)){
                    return redirect()->back()->with('error',"Whoops! You don't have the access"); 
                }
                $district_id = json_decode($teamleader->group->region->district_id, true);
                $thana_id  = json_decode($teamleader->group->region->thana_id , true);
                $product_category_id = json_decode($teamleader->group->category_id, true);

                $data->whereIn('tickets.district_id', $district_id)
                        ->whereIn('tickets.thana_id', $thana_id )
                        ->whereIn('tickets.product_category_id',$product_category_id);

            } elseif ($user_role->name == 'Admin' || $user_role->name == 'Super Admin' || $user_role->name =='Call Center Admin') {
                $data;
            } else {
                $data->where('tickets.outlet_id', $employee->outlet_id);
            }

            switch($this->id) {
                case 0:
                    $data->where('tickets.status', 0);
                    break;
                    
                    case 1:
                        $data->where('tickets.status', 1)
                        ->where('tickets.is_assigned',1);
                        break;
                        
                    case 2:
                        $data->where('tickets.status', 2)
                        ->where('tickets.is_rejected',1);
                        break;
                    case 3:
                        $data->where('tickets.status', 3)
                        ->where('tickets.is_accepted',1);
                        break;
                    case 4:
                        $data->where('tickets.status', 4)
                        ->where('tickets.is_started',1);
                        break;    
                    case 5:
                        $data->where('tickets.status', 5)
                        ->where('tickets.is_paused',1);
                        break;  
                    
                    case 6:
                        $data->where('tickets.status', 6)
                        ->where('tickets.is_pending',1);
                        break; 
                    case 7:
                        $data->where('tickets.status', 8)
                        ->where('tickets.is_delivered_by_teamleader',1);
                            break;
                    case 8:
                        $data->where('tickets.status', 9)
                        ->where('tickets.is_reopened',1);
                            break;
                    case 9:
                        $data->where('tickets.status', 10)
                        ->where('tickets.is_delivered_by_call_center',1);
                            break;
                    case 10:
                        $data->where('tickets.status', 11)
                        ->where('tickets.is_ended',1);
                            break;
                    case 11:
                        $data->where('tickets.status', 12)
                        ->where('tickets.is_delivered_by_call_center',1)
                        ->where('tickets.is_closed',1);
                            break;
                    case 12:
                        $data;
                            break;
                    case 13:
                        $data->where('tickets.status', 12)
                        ->where('tickets.is_delivered_by_call_center',0)
                        ->where('tickets.is_closed',1);
                            break;
                default:
                    return redirect()->route('ticket-index');
            }
            $tickets=$data->latest()->get();
            $date = Carbon::now()->format('m/d/Y');
            return view('ticket.purchaseHistory.ticket_status_excel',compact('serviceTypes','tickets','status','date'));
        }catch (\Exception $e) {
            $bug = $e->getMessage();
            dd($bug);
            return redirect()->back()->with('error', $bug);
        }

    }
}
