<?php

namespace App\Http\Controllers\Ticket;

use Session;
use Redirect;
use Validator;
use DataTables;
use Excel;
use App\Exports\ExportTicket;
use Carbon\Carbon;
use App\Traits\OTPTraits;
use App\Models\JobModel\Job;
use Illuminate\Http\Request;
use App\Models\Outlet\Outlet;
use App\Models\Ticket\Ticket;
use App\Models\Inventory\Fault;
use App\Models\Inventory\Thana;
use App\Models\Customer\Customer;
use App\Models\Employee\Employee;
use App\Models\Inventory\District;
use App\Models\Ticket\Accessories;
use App\Models\Ticket\JobPriority;
use App\Models\Ticket\ReceiveMode;
use App\Models\Ticket\ServiceType;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use App\Models\Employee\TeamLeader;
use App\Models\Ticket\DeliveryMode;
use App\Models\Ticket\WarrantyType;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\Customer\CustomerGrade;
use App\Models\Ticket\PurchaseHistory;
use App\Models\Ticket\ProductCondition;
use App\Models\ProductPurchase\Purchase;
use App\Http\Requests\storeTicketRequest;
use App\Models\Customer\FeedbackQuestion;
use App\Models\Job\JobAttachment;
use App\Services\ImageUploadService;
use App\Services\TicketStatusService;
use App\Services\TicketService;

class PurchaseHistoryController extends Controller
{
    use OTPTraits;

    protected $imageUploadService;
    protected $ticketStatusService;

    public function __construct(ImageUploadService $imageUploadService, TicketStatusService $ticketStatusService)
    {
        $this->imageUploadService = $imageUploadService;
        $this->ticketStatusService = $ticketStatusService;
    }

    public function index()
    {
        $purchaseHistoryArr = [];
        return view('ticket.purchaseHistory.index',compact('purchaseHistoryArr'));

    }

    public function ticketIndex(Request $request)
    {
        try{
            $employee=Employee::where('user_id',Auth::user()->id)->first();
            $auth = Auth::user();
            $user_role = $auth->roles->first();

            if ($user_role->name == 'Team Leader') {

                $teamLeader = TeamLeader::where('user_id', Auth::user()->id)->first();
                if (empty($teamLeader)) {
                    return redirect()->back()->with('error', "Whoops! You don't have the access");
                }
    
                $districtIds = json_decode($teamLeader->group->region->district_id, true);
                $thanaIds = json_decode($teamLeader->group->region->thana_id, true);
                $categoryIds = json_decode($teamLeader->group->category_id, true);
    
                $totalTicketStatus = $this->ticketStatusService->totalStatusByTeam($districtIds, $thanaIds, $categoryIds);

            } elseif ($user_role->name == 'Admin' || $user_role->name == 'Super Admin' || $user_role->name == 'Call Center Admin') {

                $totalTicketStatus = $this->ticketStatusService->totalStatus();

            } else {

                $totalTicketStatus = $this->ticketStatusService->totalStatusByOutlet($employee->outlet_id);

            }
            
            
            if (request()->ajax()) {  

                $serviceTypes = ServiceType::where('status', 1)->get(); 

                $data = TicketService::buildQuery(); 

                if ($user_role->name == 'Super Admin' || $user_role->name == 'Admin' || $user_role->name =='Team Leader Admin' || $user_role->name =='Call Center Admin') {

                    TicketService::admin($data);

                } elseif ($user_role->name == 'Team Leader') {

                    $teamLeader = TeamLeader::where('user_id', Auth::user()->id)->first();

                    if (empty($teamLeader)) {
                        return redirect()->back()->with('error', "Whoops! You don't have the access");
                    }
        
                    $districtIds = json_decode($teamLeader->group->region->district_id, true);
                    $thanaIds = json_decode($teamLeader->group->region->thana_id, true);
                    $categoryIds = json_decode($teamLeader->group->category_id, true);

                    TicketService::extendForTeamLeader($data, $districtIds, $thanaIds, $categoryIds);
                    
                } else {

                    TicketService::extendForOutlet($data, $employee->outlet_id);
                }

                $status=[0];
                TicketService::extendForStatus($data, $status);

                if(!empty($request->start_date && $request->end_date))
                {
                    $startDate = Carbon::parse($request->get('start_date'))->format('Y-m-d');
                    $endDate = Carbon::parse($request->get('end_date'))->addDay()->format('Y-m-d');
                    TicketService::extendForDateRange($data, $startDate, $endDate);
                } 

                $tickets = $data->latest()->get();

                return DataTables::of($tickets)

                    ->addColumn('ticket_sl', function ($ticket) {
                        $tsl='TSL-'.$ticket->ticket_id;
                        return $tsl;
                    })
                    ->addColumn('invoice_number', function ($ticket) {
                        $invoice_number=$ticket->invoice_number;
                        return $invoice_number;
                    })
                    ->addColumn('customer_name', function ($ticket) {
                        $customer_name=$ticket->customer_name ?? Null;
                        return $customer_name;
                    })

                    ->addColumn('customer_phone', function ($ticket) {
                        $customer_phone=$ticket->customer_mobile ?? Null;
                        return $customer_phone;
                    })

                    ->addColumn('district_thana', function ($ticket) {
                        $district=$ticket->district ?? Null;
                        $thana=$ticket->thana ?? Null;
                        $data=$district.','.$thana;
                        return $data;
                    })
                    
                    ->addColumn('product_category', function ($ticket) {
                        $product_category=$ticket->product_category ?? Null;
                        return $product_category;
                    })
                    
                    ->addColumn('product_name', function ($ticket) {
                        $product_name=$ticket->product_name ?? Null;
                        return $product_name;
                    })
                    
                    ->addColumn('product_sl', function ($ticket) {
                        $product_sl=$ticket->product_serial ?? Null;
                        return $product_sl;
                    })
                    
                    ->addColumn('service_type', function($ticket) use($serviceTypes){
                         $selectedServiceTypeIds=json_decode($ticket->service_type_id);
                         $data='';
                         foreach ($serviceTypes as $key => $serviceType) {
                            if (in_array($serviceType->id, $selectedServiceTypeIds)) {
                                $data=$serviceType->service_type;
                            }
                         }
                         return $data;
                    })
                    
                    ->addColumn('warranty_type', function($ticket){

                            return $ticket->warranty_type ?? null;
                    })
                    ->addColumn('branch', function($ticket){

                            return $ticket->outlet_name;
                    })
                    ->addColumn('point_of_purchase', function($ticket){
                        $point_of_purchase=Outlet::where('id', '=', $ticket->outletid)->first();
                            return $point_of_purchase->name ?? null;
                    })
                    ->addColumn('created_by', function ($ticket) {
                        $created_by=$ticket->created_by; 
                        return $created_by;
                    })
                    ->addColumn('created_at', function($ticket){
                            $created_at=Carbon::parse($ticket->created_at)->format('m/d/Y');
                            
                            return $created_at;
                    })
                    ->addColumn('status', function ($ticket) {

                        if ($ticket->status == 9 && $ticket->is_reopened == 1){
                            return '<span class="badge bg-red">Ticket Re-Opened</span>';
                        }
                        
                        elseif( $ticket->status == 0 ){
                            return '<span class="badge bg-yellow">Created</span>';
                        }

                        
                        elseif($ticket->status == 6 && $ticket->is_pending==1 )
                        {
                            return '<span class="badge bg-orange">Pending</span>';
                        }

                        elseif($ticket->status == 5 && $ticket->is_paused == 1 )
                        {
                            return '<span class="badge bg-red">Paused</span>';
                        }

                        elseif($ticket->status == 7  && $ticket->is_closed_by_teamleader == 1)
                        {
                            return '<span class="badge bg-green">Forwarded to CC</span>';
                        }
                        elseif($ticket->status == 10 && $ticket->is_delivered_by_call_center == 1 )
                        {
                            return '<span class="badge bg-green">Delivered by CC</span>';
                        }
                        elseif($ticket->status == 8 && $ticket->is_delivered_by_teamleader == 1 )
                        {
                            return '<span class="badge bg-green">Delivered by TL</span>';
                        }

                        elseif($ticket->status == 12  && $ticket->is_delivered_by_call_center == 1 && $ticket->is_closed == 1)
                        {
                            return '<span class="badge badge-danger">Tticket is Closed</span>';
                        }
                        elseif($ticket->status == 12 && $ticket->is_delivered_by_call_center == 0 && $ticket->is_closed == 1)
                        {
                            return '<span class="badge badge-danger">Ticket is Undelivered Closed</span>';
                        }
                        elseif($ticket->status == 11 && $ticket->is_ended == 1)
                        {
                            return '<span class="badge badge-success">Job Completed</span>';
                        }

                        elseif($ticket->status == 4 && $ticket->is_started == 1)
                        {
                            return '<span class="badge badge-info">Job Started</span>';
                        }
                        elseif($ticket->status == 3 && $ticket->is_accepted == 1)
                        {
                            return '<span class="badge badge-primary">Job Accepted</span>';
                        }
                        elseif($ticket->status == 1 && $ticket->is_assigned == 1)
                        {
                            return '<span class="badge bg-blue">Assigned</span>';
                        }
                        elseif ($ticket->status == 2 && $ticket->is_rejected == 1)
                        {
                            return '<span class="badge bg-red">Rejected</span>';
                        }
                        
                    })
                    
                    ->addColumn('delivery_date_by_call_center', function($ticket){
                        $delivery_date_by_call_center=null;
                        if ($ticket->delivery_date_by_call_center != null) {
                            $delivery_date_by_call_center=Carbon::parse($ticket->delivery_date_by_call_center)->format('m/d/Y');
                        }
                        return $delivery_date_by_call_center;
                    })

                    ->addColumn('action', function ($ticket) {
                        if ($ticket->status == 1 && $ticket->is_ended == 1 && $ticket->is_closed == 1 ) {
                            if (Auth::user()->can('show')) {
                                return '<div class="table-actions text-center" style="display: flex;>
                                    <i class="ik ik-edit f-16 mr-15 text-yellow" title="You can not edit"></i>
                                    <a href=" '.route('show-ticket-details', $ticket->ticket_id). ' ">
                                        <i class="ik ik-eye f-16 mr-15 text-info"></i>
                                    </a>
                                    <i class="ik ik-trash-2 f-16 text-yellow" title="You can not delete"></i>
                                    </div>';
                                }
                        }else{
                            if (Auth::user()->can('edit') && Auth::user()->can('delete') && Auth::user()->can('show')) {
                                return '<div class="table-actions text-center" style="display: flex;">
                                
                                                <a href=" ' .route('edit-ticket-details', $ticket->ticket_id) . ' " title="Edit">
                                                <i class="ik ik-edit-2 f-16 mr-15 text-green"></i>
                                                </a>
                                                
                                                <a href=" '.route('show-ticket-details', $ticket->ticket_id). ' " title="View">
                                                <i class="ik ik-eye f-16 mr-15 text-info"></i>
                                                </a>
                                                
                                                <a type="submit" onclick="showDeleteConfirm(' . $ticket->ticket_id . ')" title="Delete"><i class="ik ik-trash-2 f-16 text-red"></i></a>
                                        </div>';
                            } elseif (Auth::user()->can('edit') && Auth::user()->can('show')) {
                                return '<div class="table-actions" style="display: flex;">
                                                <a href=" ' . route('edit-ticket-details', $ticket->ticket_id) . ' " title="Edit"><i class="ik ik-edit-2 f-16 mr-15 text-green"></i></a>
                                                <a href=" '.route('show-ticket-details', $ticket->ticket_id). ' ">
                                                    <i class="ik ik-eye f-16 mr-15 text-info"></i>
                                                </a>
                                                </div>';
                            } elseif (Auth::user()->can('delete')) {
                                return '<div class="table-actions">
                                                <a type="submit" onclick="showDeleteConfirm(' . $ticket->ticket_id . ')" title="Delete"><i class="ik ik-trash-2 f-16 text-red"></i></a>
                                        </div>';
                            } elseif (Auth::user()->can('show')) {
                                return '<div class="table-actions">
                                                <a href=" '.route('show-ticket-details', $ticket->ticket_id). ' ">
                                                    <i class="ik ik-eye f-16 mr-15 text-info"></i>
                                                </a>
                                        </div>';
                            } 
                        }

                    })
                    ->addIndexColumn()
                    ->rawColumns(['ticket_sl', 'customer_name', 'customer_phone', 'district_thana', 'product_category', 'product_sl','created_by', 'status', 'action'])
                    ->make(true);
            }
            return view('ticket.purchaseHistory.ticket_index', compact('totalTicketStatus'));
        }catch(\Exception $e){
            $bug = $e->getMessage();
            return redirect()->back()->with('error', $bug);
        }
    }

    public function ticketcreate(Request $request,$id)
    {
        try{
            
            $purchaseHistoryArr =[];
            $purchase=Purchase::with('customer')->where('id',$id)->first();
            $purchase_list=Ticket::latest('id')->first();
            $receiveModes= ReceiveMode::where('status',1)->latest()->get();
            $deliveryModes= DeliveryMode::where('status',1)->latest()->get();
            $ticket_sl = $purchase_list->id+1;
            // $ticket_sl = $this->generateUniqueTicketSl();
            $thanas = Thana::orderBy('name')->get();

            $productCategorys =  [];
            $warrantyTypes    = WarrantyType::where('status',1)->latest()->get();
            $job_priorities   = JobPriority::where('status',1)->latest()->get();
            $product_conditions = ProductCondition::where('status',1)->latest()->get();
            $districts        = District::orderBy('name')->get();

            $faults           = Fault::where('category_id',$purchase->product_category_id )
                                        ->where('status', 1)->pluck('name','id')->toArray();
            $serviceTypes     = ServiceType::where('category_id',$purchase->product_category_id)
                                        ->where('status', 1)->get();

            $accessories_list = Accessories::where('product_id',$purchase->product_category_id)
                                            ->where('status', 1)
                                            ->latest()->get();
            $customerGrades   = CustomerGrade::where('status', 1)->get();

            $currentDate= Carbon::now('Asia/Dhaka');
            $outlets = Outlet::where('status', 1)->orderBy('name')->get();
            return view('ticket.purchaseHistory.create', compact('ticket_sl','purchase','purchaseHistoryArr', 'productCategorys','warrantyTypes','districts','faults', 'serviceTypes','accessories_list','job_priorities','product_conditions', 'customerGrades', 'currentDate', 'receiveModes', 'deliveryModes','outlets'));
        }catch(\Exception $e){
            $bug = $e->getMessage();
            return redirect()->back()->with('error', $bug);
        }
    }

    // storeTicketRequest
    public function storeTicket(Request $request)
    {
        Session::put('tahanaId', $request->thana_id);

        $request->validate([
            'date' => 'required|date',
            'product_category_id' => 'required|numeric',
            'purchase_id' => 'required|numeric',
            'job_priority_id' => 'required|numeric',
            'service_type_id' => 'required',
            'warranty_type_id' => 'required',
            'outlet_id' => 'required',
            'carrier_phone' => 'nullable|min:11|max:11|regex:/(01)[0-9]{9}/',
            'customer_id' => 'required|numeric',
            'district_id' => 'required|numeric',
            'thana_id' => 'required|numeric',
            'start_date' => 'required|date',
            'end_date' => 'required|date',
            'product_receive_mode_id' => 'required|numeric',
            'expected_delivery_mode_id' => 'required|numeric',
            'image.*' => 'mimes:jpeg,jpg,png|required|max:10000' ,// max 10000kb
        ]);

        DB::beginTransaction();
        try{
            $customer = Customer::findOrFail($request->customer_id);
            if($request->carrier_own) {
                $customer->update([
                    'name' => $request->carrier,
                    'mobile' => $request->carrier_phone
                ]);
            }

            $ticket_sl = $this->generateUniqueTicketSl();

            $tickt=Ticket::create([
                'date' =>  $request->date,
                'outlet_id' =>  $request->outlet_id,
                'sl_number' =>  $ticket_sl,
                'purchase_id' =>  $request->purchase_id,
                'customer_reference' => $request->customer_reference,
                'product_category_id' =>  $request->product_category_id,
                'district_id' =>  $request->district_id,
                'thana_id' => $request->thana_id,
                'carrier_phone'=>$request->carrier_phone,
                'carrier'=>$request->carrier,
                'warranty_type_id'=>$request->warranty_type_id,
                'job_priority_id'=>$request->job_priority_id,
                'service_type_id'=>json_encode($request->service_type_id),
                'service_charge' => $request->service_charge,
                'fault_description_id'=>json_encode($request->fault_description_id),
                'fault_description_note'=>$request->fault_description_note,
                'product_condition_id'=>json_encode($request->product_condition_id),
                'start_date'=>$request->start_date,
                'end_date'=>$request->end_date,
                'customer_note'=>$request->customer_note,
                'product_receive_mode_id' => $request->product_receive_mode_id,
                'expected_delivery_mode_id' => $request->expected_delivery_mode_id,
                'accessories_list_id' => json_encode($request->accessories_list_id),
                'created_by' => Auth::id(),
            ]);

            if ($request->hasfile('image')) {
                $destinationPath = public_path('attachments/');
                $uploadedFiles = $this->imageUploadService->uploadImages($request->file('image'), $destinationPath);
                $attachments = new JobAttachment();
                $attachments->name = json_encode($uploadedFiles);
                $attachments->ticket_id = $tickt->id;
                $attachments->save();
            }
            
            DB::commit();
            Session::forget('tahanaId');
            
            //Sms notification 
            if ($request->send_sms != null) {
                $tsl_no ='TSL'.'-'.$tickt->id;
                $text = "Dear Valued Customer, Your Product is registered."."Ticket No.".$tsl_no."."." We will get back to you within 48 hours. PH: 09612 244244 Ex:3 (9 AM-6 PM/Sat-Thu) RANGS SERVICE";
                $phone = null;
                if($request->carrier_own){
                    $phone=$request->carrier_phone;
                }else{
                    $phone=$customer->mobile;
                }
                $sms = $this->sendSms($phone, $text);
            }
            
            return redirect('tickets/ticket-index')
                    ->with('success',$tickt->id.'-'.'Number Ticket Created');
        }catch(\Exception $e){
            $bug = $e->getMessage();
            DB::rollback();
            return redirect()->back()->with('error', $bug);
        }
    }

    public function editTicket($id)
    {
        try{
            $ticket = Ticket::findOrFail($id);
            $purchase = Purchase::where('id',$ticket->purchase_id)->first();
            $job_priorities = JobPriority::where('status', 1)->latest()->get();
            $receiveModes= ReceiveMode::where('status', 1)->latest()->get();
            $deliveryModes= DeliveryMode::where('status', 1)->latest()->get();
            $productCategorys =  [];

            // Json Decode Some Id
            $accessoriesListId = json_decode($ticket->accessories_list_id);
            $productConditionId = json_decode($ticket->product_condition_id);
            $faultDescriptionId = json_decode($ticket->fault_description_id);
            $serviceTypeId = json_decode($ticket->service_type_id);

            $product_conditions = ProductCondition::where('status', 1)->latest()->get();
            $accessories_list = Accessories::where('status', 1)
                    ->where('product_id',$purchase->product_category_id )
                    ->latest()->get();

            $warrantyTypes = WarrantyType::where('status', 1)->get();
            $districts = District::all();
            $thanas = Thana::all();
            $faults = Fault::where('category_id', $purchase->product_category_id )
                        ->where('status', 1)
                        ->pluck('name','id')->toArray();

            $serviceTypes = ServiceType::where('status', 1)
                            ->where('category_id', $purchase->product_category_id )
                            ->latest()->get();

            $currentDate = Carbon::now('Asia/Dhaka');

            $customerGrades = CustomerGrade::where('status', 1)->get();
            $outlets = Outlet::where('status', 1)->orderBy('name')->get();

            return view('ticket.purchaseHistory.edit_ticket', compact('productCategorys','warrantyTypes','districts','faults', 'serviceTypes', 'ticket', 'purchase', 'job_priorities', 'thanas', 'product_conditions', 'accessories_list' , 'customerGrades', 'accessoriesListId', 'productConditionId', 'faultDescriptionId', 'currentDate', 'serviceTypeId', 'receiveModes', 'deliveryModes','outlets'));
        }catch(\Exception $e){
            $bug = $e->getMessage();
            return redirect()->back()->with('error', $bug);
        }
    }

    public function updateTicket(storeTicketRequest $request, $id)
    {
        $request->validate([
            'date' => 'required|date',
            'sl_number' => 'required|string',
            'outlet_id' => 'required',
            'product_category_id' => 'required|numeric',
            'purchase_id' => 'required|numeric',
            'job_priority_id' => 'required|numeric',
            'service_type_id' => 'required',
            'warranty_type_id' => 'required',
            // 'sl_number' => 'required|unique:tickets,sl_number,' . $id,
            'carrier_phone' => 'nullable|min:11|max:11|regex:/(01)[0-9]{9}/',
            'customer_id' => 'required|numeric',
            'district_id' => 'required|numeric',
            'thana_id' => 'required|numeric',
            'start_date' => 'required|date',
            'end_date' => 'required|date',
            'product_receive_mode_id' => 'required|numeric',
            'expected_delivery_mode_id' => 'required|numeric'
        ]);

        try{
            DB::beginTransaction();
            $ticket = Ticket::find($id);

            if($request->carrier_own) {
                $customer = Customer::find($request->customer_id);
                $customer->update([
                    'name' => $request->carrier,
                    'mobile' => $request->carrier_phone
                ]);
            }

            $ticket->update([
                'date' =>  $request->date,
                'outlet_id' =>  $request->outlet_id,
                // 'sl_number' =>  $request->sl_number,
                'purchase_id' =>  $request->purchase_id,
                'customer_reference' => $request->customer_reference,
                'product_category_id' =>  $request->product_category_id,
                'district_id' =>  $request->district_id,
                'thana_id' => $request->thana_id,
                'carrier_phone'=>$request->carrier_phone,
                'carrier'=>$request->carrier,
                'warranty_type_id'=>$request->warranty_type_id,
                'job_priority_id'=>$request->job_priority_id,
                'service_type_id'=>json_encode($request->service_type_id),
                'service_charge' => $request->service_charge,
                'fault_description_id'=>json_encode($request->fault_description_id),
                'fault_description_note'=>$request->fault_description_note,
                'product_condition_id'=>json_encode($request->product_condition_id),
                'start_date'=>$request->start_date,
                'end_date'=>$request->end_date,
                'customer_note'=>$request->customer_note,
                'product_receive_mode_id' => $request->product_receive_mode_id,
                'expected_delivery_mode_id' => $request->expected_delivery_mode_id,
                'accessories_list_id' => json_encode($request->accessories_list_id),
                'updated_by' => Auth::id(),
            ]);
            DB::commit();
            return redirect()->route('ticket-index')->with('success', 'Ticket updated successfully');
        }catch(\Exception $e){
            DB::rollback();
            $bug = $e->getMessage();
            return redirect()->back()->with('error', $bug);
        }
    }

    public function showTicket($id)
    {
        try{
            $auth = Auth::user();
            $user_role = $auth->roles->first();

            $is_teamleader='';
            $ticket = Ticket::find($id);
            $jobs= Job::where('ticket_id', $ticket->id)->get();
            $teamleader=TeamLeader::where('user_id',Auth::user()->id)->first();
            if ($teamleader!=null) {
                $is_teamleader=$teamleader;
            }
            $faults = Fault::where('status', 1)->get();
            $product_conditions = ProductCondition::where('status', 1)->get();
            $accessories_lists = Accessories::where('status', 1)->get();
            $warrantyTypes = DB::table('warranty_types')->where('status', 1)->get();
            $ticketId=$id;
            $serviceTypes= ServiceType::where('status', 1)->get();
            $questions=FeedbackQuestion::where('status', 1)->get();
            $customerFeedbacks= DB::table('customer_feedback')
                                ->join('feedback_questions', 'feedback_questions.id', '=', 'customer_feedback.question_id')
                                ->where('ticket_id', $id)
                                ->select('customer_feedback.*', 'feedback_questions.question')
                                ->get();

            return view('ticket.purchaseHistory.show_ticket', compact('ticket', 'faults', 'warrantyTypes', 'product_conditions', 'accessories_lists', 'questions', 'ticketId', 'customerFeedbacks', 'serviceTypes', 'jobs','is_teamleader','user_role'));
        }catch(\Exception $e){
            $bug = $e->getMessage();
            return redirect()->back()->with('error', $bug);
        }
    }

    //Close Ticket
    public function close($id)
    {
        try{
            $ticketId=$id;
            $questions=FeedbackQuestion::where('status', 1)->get();
            return view('customer.customer_feedback.create', compact('questions', 'ticketId'));
        }catch(\Exception $e){
            $bug = $e->getMessage();
            return redirect()->back()->with('error', $bug);
        }
    }
    
    // Product delivery
    public function deliveryByTeamLeader($id)
    {
        try{
            $currentDate = Carbon::now('Asia/Dhaka');
            $formattedCurrentDate=$currentDate->toDateString();
            DB::beginTransaction();
            $ticket = Ticket::find($id);
            $ticket->update([
                'status' => 8,
                'is_delivered_by_teamleader' => 1,
                'delivery_date_by_team_leader' => $formattedCurrentDate,
                'updated_by' => Auth::id(),
            ]);
            DB::commit();
            return redirect()->back()->with('success', __('Product Delivered Successfully.'));
        }catch(\Exception $e){
            DB::rollback();
            $bug = $e->getMessage();
            return redirect()->back()->with('error', $bug);
        }
    }
    //Product Delivery by call center
    public function deliveryByCallCenter(Request $request)
    {
        $request->validate([
            'ticket_id' => 'required',
            'sl_number' => 'required',
        ]);
        try{
            $currentDate = Carbon::now('Asia/Dhaka');
            $formattedCurrentDate=$currentDate->toDateString();
            DB::beginTransaction();
            $ticket = Ticket::find($request->ticket_id);
            if ($ticket != null) {
                $ticket->update([
                    'status' => 10,
                    'is_delivered_by_call_center' => 1,
                    'delivery_date_by_call_center' => $formattedCurrentDate,
                    'updated_by' => Auth::id(),
                ]);
            DB::commit();                //Sms notification 
                if ($request->send_sms == 1) {
                    if ($ticket->purchase->customer->mobile !=null) {
                        $tsl_no ='TSL'.'-'.$ticket->id;
                        $text = "Dear Valued Customer, Your product is delivered."."Ticket No.".$tsl_no."."." All the best to you. PH: 09612 244244 Ex:3 (9 AM-6 PM/Sat-Thu) RANGS SERVICE";
                        $phone = $ticket->purchase->customer->mobile;
                        $sms = $this->sendSms($phone, $text);
                        
                    }
                }
                return redirect()->back()->with('success', __('Product Delivered Successfully.'));
            }
            return redirect()->back()->with('error', __('Something went wrong'));
            
        }catch(\Exception $e){
            DB::rollback();
            $bug = $e->getMessage();
            return redirect()->back()->with('error', $bug);
        }
    }
    //Ticket CLosing By Team Leader
    public function closeByTeamleader($id)
    {
        try{
            DB::beginTransaction();
            $ticket = Ticket::find($id);
            $ticket->update([
                'status'=>7,
                'is_closed_by_teamleader'=>1,
                'updated_by' => Auth::id(),
            ]);
            DB::commit();
            return redirect()->back()->with('success', __('Ticket CLosed Successfully.'));
        }catch(\Exception $e){
            DB::rollback();
            $bug = $e->getMessage();
            return redirect()->back()->with('error', $bug);
        }
    }
    // Ticket Re-Open
    public function reOpen(Request $request){
        try {
            DB::beginTransaction();
            $ticket=Ticket::find($request->ticket_id);
            $ticket->update([
                'status' => 9,
                'is_assigned' => 0,
                'is_accepted' => 0,
                'is_started' => 0,
                'is_ended' => 0,
                'is_paused' => 0,
                'is_pending' => 0,
                'is_rejected' => 0,
                'is_delivered_by_teamleader' => 0,
                'is_delivered_by_call_center' => 0,
                'is_closed_by_teamleader' => 0,
                'delivery_date_by_team_leader' => NULL,
                'delivery_date_by_call_center' => NULL,
                'is_reopened'=> 1,
                'reopen_note'=> $request->note,
                'reopen_date'=> Carbon::now(),
                'updated_by' => Auth::id(),
            ]);
            DB::commit();
            return redirect()->back()->with('success', __('Ticket Re-Opened Successfully.'));
        } catch (\Exception $e) {
            DB::rollback();
            $bug = $e->getMessage();
            return redirect()->back()->with('error', $bug);
        }
    }

    public function destroyTicket($id){
        try {
            $ticket=Ticket::findOrFail($id);
            if($ticket){
                $jobs= Job::where('ticket_id', $ticket->id)->get();
                if(count($jobs) > 0){
                    return response()->json([
                        'success' => false,
                        'message' => "Sorry! Can't Delete. This Ticket is Used in Job Already",
                    ]);
                } else {
                    $ticket->delete();
                    return response()->json([
                    'success' => true,
                    'message' => 'Ticket Deleted Successfully.',
                ]);

                }
            }
        } catch (\Exception $e) {
            $bug = $e->getMessage();
            return response()->json([
                    'success' => false,
                    'message' => $bug
            ]);
        }
    }

    public function serviceAmount(Request $request)
    {
        $serviceTypes= $request->id;
        $sum=0;
        foreach($serviceTypes as $serviceType){
            $serviceTypeValue = ServiceType::where('id', $serviceType)->value('service_amount');
            $sum+= $serviceTypeValue;
        }
        return $sum;
    }

    public function status(Request $request, $id)
    {
        try {
            $auth = Auth::user();
            $user_role = $auth->roles->first();
            $employee=Employee::where('user_id',Auth::user()->id)->first();

            if ($user_role->name == 'Team Leader') {

                $teamLeader = TeamLeader::where('user_id', Auth::user()->id)->first();
                if (empty($teamLeader)) {
                    return redirect()->back()->with('error', "Whoops! You don't have the access");
                }
    
                $districtIds = json_decode($teamLeader->group->region->district_id, true);
                $thanaIds = json_decode($teamLeader->group->region->thana_id, true);
                $categoryIds = json_decode($teamLeader->group->category_id, true);
    
                $totalTicketStatus = $this->ticketStatusService->totalStatusByTeam($districtIds, $thanaIds, $categoryIds);

            } elseif ($user_role->name == 'Admin' || $user_role->name == 'Super Admin' || $user_role->name == 'Call Center Admin') {

                $totalTicketStatus = $this->ticketStatusService->totalStatus();

            } else {

                $totalTicketStatus = $this->ticketStatusService->totalStatusByOutlet($employee->outlet_id);

            }
           

            if (request()->ajax()) {
                $serviceTypes = ServiceType::where('status', 1)->get(); 
                $data = TicketService::buildQuery(); 

                if ($user_role->name == 'Super Admin' || $user_role->name == 'Admin' || $user_role->name =='Team Leader Admin' || $user_role->name =='Call Center Admin') {

                    TicketService::admin($data);

                } elseif ($user_role->name == 'Team Leader') {

                    $teamLeader = TeamLeader::where('user_id', Auth::user()->id)->first();

                    if (empty($teamLeader)) {
                        return redirect()->back()->with('error', "Whoops! You don't have the access");
                    }
        
                    $districtIds = json_decode($teamLeader->group->region->district_id, true);
                    $thanaIds = json_decode($teamLeader->group->region->thana_id, true);
                    $categoryIds = json_decode($teamLeader->group->category_id, true);

                    TicketService::extendForTeamLeader($data, $districtIds, $thanaIds, $categoryIds);

                } else {

                    TicketService::extendForOutlet($data, $employee->outlet_id);

                }

                switch($id) {
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
                        return false;
                }

                if(!empty($request->start_date && $request->end_date))
                {
                    $startDate = Carbon::parse($request->get('start_date'))->format('Y-m-d');
                    $endDate = Carbon::parse($request->get('end_date'))->addDay()->format('Y-m-d');
                    TicketService::extendForDateRange($data, $startDate, $endDate);
                } 
                
                $tickets=$data->latest()->get();

                return DataTables::of($tickets)

                    ->addColumn('ticket_sl', function ($tickets) {
                        $tsl='TSL-'.$tickets->ticket_id;
                        return $tsl;
                    })
                    ->addColumn('invoice_number', function ($tickets) {
                        $invoice_number=$tickets->invoice_number;
                        return $invoice_number;
                    })
                    ->addColumn('customer_name', function ($tickets) {
                        $customer_name=$tickets->customer_name ?? Null;
                        return $customer_name;
                    })

                    ->addColumn('customer_phone', function ($tickets) {
                        $customer_phone=$tickets->customer_mobile ?? Null;
                        return $customer_phone;
                    })

                    ->addColumn('district_thana', function ($tickets) {
                        $district=$tickets->district ?? Null;
                        $thana=$tickets->thana ?? Null;
                        $data=$district.','.$thana;
                        return $data;
                    })
                    
                    ->addColumn('product_category', function ($tickets) {
                        $product_category=$tickets->product_category ?? Null;
                        return $product_category;
                    })
                    
                    ->addColumn('product_name', function ($tickets) {
                        $product_name=$tickets->product_name ?? Null;
                        return $product_name;
                    })
                    
                    ->addColumn('product_sl', function ($tickets) {
                        $product_sl=$tickets->product_serial ?? Null;
                        return $product_sl;
                    })
                    
                    ->addColumn('service_type', function($tickets) use($serviceTypes){
                         $selectedServiceTypeIds=json_decode($tickets->service_type_id);
                         $data='';
                         foreach ($serviceTypes as $key => $serviceType) {
                            if (in_array($serviceType->id, $selectedServiceTypeIds)) {
                                $data=$serviceType->service_type;
                            }
                         }
                         return $data;
                    })
                    
                    ->addColumn('warranty_type', function($tickets){

                            return $tickets->warranty_type ?? null;
                    })
                    ->addColumn('branch', function($tickets){

                            return $tickets->outlet_name;
                    })
                    ->addColumn('point_of_purchase', function($tickets){
                        $point_of_purchase=Outlet::where('id', '=', $tickets->outletid)->first();
                            return $point_of_purchase->name ?? null;
                    })
                    ->addColumn('created_by', function ($tickets) {
                        $created_by=$tickets->created_by; 
                        return $created_by;
                    })
                    
                    ->addColumn('created_at', function($tickets){
                            $created_at=Carbon::parse($tickets->created_at)->format('m/d/Y');
                            
                            return $created_at;
                    })
                    ->addColumn('status', function ($tickets) {

                        if ($tickets->status == 9 && $tickets->is_reopened == 1){
                            return '<span class="badge bg-red">Ticket Re-Opened</span>';
                        }
                        
                        elseif( $tickets->status == 0 ){
                            return '<span class="badge bg-yellow">Created</span>';
                        }

                        
                        elseif($tickets->status == 6 && $tickets->is_pending==1 )
                        {
                            return '<span class="badge bg-orange">Pending</span>';
                        }

                        elseif($tickets->status == 5 && $tickets->is_paused == 1 )
                        {
                            return '<span class="badge bg-red">Paused</span>';
                        }

                        elseif($tickets->status == 7  && $tickets->is_closed_by_teamleader == 1)
                        {
                            return '<span class="badge bg-green">Forwarded to CC</span>';
                        }
                        elseif($tickets->status == 10 && $tickets->is_delivered_by_call_center == 1 )
                        {
                            return '<span class="badge bg-green">Delivered by CC</span>';
                        }
                        elseif($tickets->status == 8 && $tickets->is_delivered_by_teamleader == 1 )
                        {
                            return '<span class="badge bg-green">Delivered by TL</span>';
                        }

                        elseif($tickets->status == 12  && $tickets->is_delivered_by_call_center == 1 && $tickets->is_closed == 1)
                        {
                            return '<span class="badge badge-danger">Tticket is Closed</span>';
                        }
                        elseif($tickets->status == 12 && $tickets->is_delivered_by_call_center == 0 && $tickets->is_closed == 1)
                        {
                            return '<span class="badge badge-danger">Ticket is Undelivered Closed</span>';
                        }
                        elseif($tickets->status == 11 && $tickets->is_ended == 1)
                        {
                            return '<span class="badge badge-success">Job Completed</span>';
                        }

                        elseif($tickets->status == 4 && $tickets->is_started == 1)
                        {
                            return '<span class="badge badge-info">Job Started</span>';
                        }
                        elseif($tickets->status == 3 && $tickets->is_accepted == 1)
                        {
                            return '<span class="badge badge-primary">Job Accepted</span>';
                        }
                        elseif($tickets->status == 1 && $tickets->is_assigned == 1)
                        {
                            return '<span class="badge bg-blue">Assigned</span>';
                        }
                        elseif ($tickets->status == 2 && $tickets->is_rejected == 1)
                        {
                            return '<span class="badge bg-red">Rejected</span>';
                        }
                        
                    })
                    ->addColumn('delivery_date_by_call_center', function($tickets){
                        $delivery_date_by_call_center=null;
                        if ($tickets->delivery_date_by_call_center != null) {
                            $delivery_date_by_call_center=Carbon::parse($tickets->delivery_date_by_call_center)->format('m/d/Y');
                        }
                        return $delivery_date_by_call_center;
                    })

                    ->addColumn('action', function ($tickets) {
                        if ($tickets->status == 1 && $tickets->is_ended == 1 && $tickets->is_closed == 1 ) {
                            if (Auth::user()->can('show')) {
                                return '<div class="table-actions text-center" style="display: flex;>
                                    <i class="ik ik-edit f-16 mr-15 text-yellow" title="You can not edit"></i>
                                    <a href=" '.route('show-ticket-details', $tickets->ticket_id). ' ">
                                        <i class="ik ik-eye f-16 mr-15 text-info"></i>
                                    </a>
                                    <i class="ik ik-trash-2 f-16 text-yellow" title="You can not delete"></i>
                                    </div>';
                                }
                        }else{
                            if (Auth::user()->can('edit') && Auth::user()->can('delete') && Auth::user()->can('show')) {
                                return '<div class="table-actions text-center" style="display: flex;">
                                
                                                <a href=" ' .route('edit-ticket-details', $tickets->ticket_id) . ' " title="Edit">
                                                <i class="ik ik-edit-2 f-16 mr-15 text-green"></i>
                                                </a>
                                                
                                                <a href=" '.route('show-ticket-details', $tickets->ticket_id). ' " title="View">
                                                <i class="ik ik-eye f-16 mr-15 text-info"></i>
                                                </a>
                                                
                                                <a type="submit" onclick="showDeleteConfirm(' . $tickets->ticket_id . ')" title="Delete"><i class="ik ik-trash-2 f-16 text-red"></i></a>
                                        </div>';
                            } elseif (Auth::user()->can('edit') && Auth::user()->can('show')) {
                                return '<div class="table-actions" style="display: flex;">
                                                <a href=" ' . route('edit-ticket-details', $tickets->ticket_id) . ' " title="Edit"><i class="ik ik-edit-2 f-16 mr-15 text-green"></i></a>
                                                <a href=" '.route('show-ticket-details', $tickets->ticket_id). ' ">
                                                    <i class="ik ik-eye f-16 mr-15 text-info"></i>
                                                </a>
                                                </div>';
                            } elseif (Auth::user()->can('delete')) {
                                return '<div class="table-actions">
                                                <a type="submit" onclick="showDeleteConfirm(' . $tickets->ticket_id . ')" title="Delete"><i class="ik ik-trash-2 f-16 text-red"></i></a>
                                        </div>';
                            } elseif (Auth::user()->can('show')) {
                                return '<div class="table-actions">
                                                <a href=" '.route('show-ticket-details', $tickets->ticket_id). ' ">
                                                    <i class="ik ik-eye f-16 mr-15 text-info"></i>
                                                </a>
                                        </div>';
                            } 
                        }

                    })
                    ->addIndexColumn()
                    ->rawColumns(['ticket_sl', 'customer_name', 'customer_phone', 'district_thana', 'product_category', 'product_sl', 'status','created_by', 'action'])
                    ->make(true);
            }
            return view('ticket.purchaseHistory.ticket_status', compact('totalTicketStatus', 'id'));
        }catch (\Exception $e) {
            $bug = $e->getMessage();
            return redirect()->back()->with('error', $bug);
        }
    }

    public function purchaseShow($id)
    {
        try{
            $purchase = Purchase::findOrFail($id);
            return view('purchase.show',compact('purchase'));
        }catch (\Exception $e) {
            $bug = $e->getMessage();
            return redirect()->back()->with('error', $bug);
        }
    }
    
    // Unique Ticket SL
    protected function generateUniqueTicketSl()
    {
        do {
            $ticket=Ticket::latest('id')->first();
       
            if(!$ticket) {
                return "TSL-1";
            }

            $string = preg_replace("/[^0-9\.]/", '', $ticket->sl_number);
            
            $ticketSl = 'TSL-' . sprintf('%01d', $string+1);

        } while (Ticket::where('sl_number', '==', $ticketSl)->first());

        return $ticketSl;
    }

        //Print
        public function claim($id)
        {
            try{
                $ticket = Ticket::find($id);
    
                $faults = Fault::where('status', 1)
                            ->where('id',[$ticket->fault_description_id] )
                            ->pluck('name','id')->toArray();
                $product_conditions = ProductCondition::where('status', 1)->get();
                $accessories_lists = Accessories::where('status', 1)
                            ->where('id',[$ticket->accessories_list_id] )
                            ->pluck('accessories_name','id')
                            ->toArray();
                $serviceTypes= ServiceType::where('status', 1)->get();
                $allAccessories=Accessories::where('status', 1)->get();
                $allFaults=Fault::where('status', 1)->get();
                return view('ticket.purchaseHistory.claim', compact('ticket','faults','product_conditions','accessories_lists', 'allAccessories','allFaults','serviceTypes'));
            } catch (\Exception $e) {
                $bug = $e->getMessage();
                return redirect()->back()->with('error', $bug);
            }
        }
        //
        public function slip($id)
        {
            try{
                $ticket = Ticket::find($id);
    
                $faults = Fault::where('status', 1)
                            ->where('id',[$ticket->fault_description_id] )
                            ->pluck('name','id')->toArray();
                $product_conditions = ProductCondition::where('status', 1)->get();
                $accessories_lists = Accessories::where('status', 1)
                            ->where('id',[$ticket->accessories_list_id] )
                            ->pluck('accessories_name','id')
                            ->toArray();
                $serviceTypes= ServiceType::where('status', 1)->get();
                $allAccessories=Accessories::where('status', 1)->get();
                $allFaults=Fault::where('status', 1)->get();
                return view('ticket.purchaseHistory.slip', compact('ticket','faults','product_conditions','accessories_lists', 'allAccessories','allFaults','serviceTypes'));
            } catch (\Exception $e) {
                $bug = $e->getMessage();
                return redirect()->back()->with('error', $bug);
            }
        }
        //Excel Download
        public function excelDownload($id){
            $ticket = Ticket::where('status', $id)->first();
            $status='';
            if ($id==0)
            {
                $status='Created';
            }
			else if($id == 1){
                $status='Assigned';
            }
			elseif($id == 2){
                $status='Cancelled';
            }
			elseif($id == 3){
                $status='Accepted';
            }
			else if($id == 4){
                $status='Started';
            }
			else if($id == 5){
                $status='Paused';
            }
			else if($id == 6){
                $status='Pending';
            }
			else if($id == 7){
                $status='Delivered By TL';
            }
			else if($id == 8){
                $status='Re-opened';
            }
			else if($id == 9){
                $status='Delivered By CC';
            }
			else if($id == 10){
                $status='Completed';
            }
			else if($id == 11){
                $status='Closed';
            }
			else if($id == 12){
                $status='All';
            }
			else if($id == 13){
                $status='Undelivered Closed';
            }

            return Excel::download(new ExportTicket($id,$status), 'Ticket'.'-'.$status .'.xlsx');
        }
}
