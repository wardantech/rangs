<?php

namespace App\Http\Controllers\Ticket;

use Session;
use Redirect;
use Validator;
use DataTables;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\Ticket\Ticket;
use App\Models\Inventory\Fault;
use App\Models\Inventory\Thana;
use App\Models\Customer\Customer;
use App\Models\Inventory\District;
use App\Models\Ticket\Accessories;
use App\Models\Ticket\JobPriority;
use App\Models\Ticket\ServiceType;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use App\Models\Employee\TeamLeader;
use App\Models\Ticket\WarrantyType;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\Customer\CustomerGrade;
use App\Models\Ticket\PurchaseHistory;
use App\Models\Ticket\ProductCondition;
use App\Models\ProductPurchase\Purchase;
use App\Http\Requests\storeTicketRequest;
use App\Models\Customer\FeedbackQuestion;


class PurchaseHistoryController extends Controller
{
    public function index()
    {
        $purchaseHistoryArr = [];
        return view('ticket.purchaseHistory.index',compact('purchaseHistoryArr'));

    }

    public function callapi(Request $request) {

            $target =[];
            $customerBasicInfo = view('ticket.purchaseHistory.getCustomerInfo', compact('target'))->render();
            $purchaseHistory = view('ticket.purchaseHistory.getPurchaseHistory', compact('target'))->render();
            $serviceHistory = view('ticket.purchaseHistory.getServiceHistory', compact('target'))->render();


            return response()->json(['purchaseHistory'=>$purchaseHistory,'serviceHistory'=>$serviceHistory
            ,'customerBasicInfo'=>$customerBasicInfo]);

    }

    public function ticketIndex(){
        $auth = Auth::user();
        $serviceTypes = ServiceType::where('status', 1)->get();
        $user_role = $auth->roles->first();
        if ($user_role->name == 'Super Admin') {
            $tickets=Ticket::latest()->get();
            // Count total by status
            $totals = $this->totalTicketStatus();
        } elseif ($user_role->name == 'Admin') {
            $tickets=Ticket::latest()->get();
            // Count total by status
            $totals = $this->totalTicketStatus();
        } else {
            $teamleader=TeamLeader::where('user_id',Auth::user()->id)->first();
            $district_id = json_decode($teamleader->group->region->district_id, true);
            $thana_id  = json_decode($teamleader->group->region->thana_id , true);
            $product_category_id = json_decode($teamleader->group->category_id, true);

            $tickets=Ticket::whereIn('district_id', $district_id)
                        ->whereIn('thana_id', $thana_id )
                        ->where('product_category_id',$product_category_id)
                        ->latest()
                        ->get();

            $totals = $this->teamleaderTotalTicketStatus($district_id, $product_category_id);
        }

        return view('ticket.purchaseHistory.ticket_index', compact('tickets', 'totals', 'serviceTypes'));
    }

    public function ticketcreate(Request $request,$id)
    {
        try{
            $purchaseHistoryArr =[];
            $purchase=Purchase::with('customer')->where('id',$id)->first();
            $purchase_list=Ticket::latest()->first();
            if(!empty($purchase_list)){
                $trim=trim($purchase_list->sl_number,"TSL-");
                $sl=$trim + 1;
                $ticket_sl="TSL-".$sl;
            }else{
                $ticket_sl="TSL-"."1";
            }


            $purchaseHistoryArr =DB::table('purchases')
                                ->leftjoin('customers', 'customers.id', '=', 'purchases.customer_id')
                                ->leftjoin('product_categories', 'product_categories.id', '=', 'purchases.product_category_id')
                                ->leftjoin('brands', 'brands.id', '=', 'purchases.brand_id')
                                ->leftjoin('brand_models', 'brand_models.id', '=', 'purchases.brand_model_id')
                                ->where('purchases.id', 2)
                                ->select('purchases.*', 'customers.name as customer_name', 'customers.id as customer_id', 'customers.address as customer_address', 'customers.mobile as customer_mobile', 'product_categories.name as product_category_name', 'product_categories.id as product_category_id', 'brands.name as brand_name', 'brand_models.model_name as brand_model_name', )
                                ->first();
                                // dd($purchaseHistoryArr);
            $thanas = Thana::orderBy('name')->get();

            $productCategorys =  [];
            $warrantyTypes    = WarrantyType::where('status',1)->latest()->get();
            $job_priorities   = JobPriority::where('status',1)->latest()->get();
            $product_conditions = ProductCondition::where('status',1)->latest()->get();
            $districts        = District::orderBy('name')->get();

            $faults           = Fault::where('category_id',$purchase->product_category_id )
                                        ->where('status', 1)->pluck('name','id')->toArray();
            $serviceTypes     = ServiceType::where('category_id',$purchase->product_category_id)
                                        ->where('status',1)->get();

            $accessories_list = Accessories::where('product_id',$purchase->product_category_id)
                                            ->where('status', 1)
                                            ->latest()->get();
            $customerGrades   = CustomerGrade::where('status', 1)->get();

            $currentDate= Carbon::now('Asia/Dhaka');

            return view('ticket.purchaseHistory.create', compact('ticket_sl','purchase','purchaseHistoryArr', 'productCategorys','warrantyTypes','districts','faults', 'serviceTypes','accessories_list','job_priorities','product_conditions', 'customerGrades', 'currentDate'));
        } catch (\Exception $e) {
            $bug = $e->getMessage();
            return redirect()->back()->with('error', $bug);
        }
    }

    // storeTicketRequest
    public function storeTicket(storeTicketRequest $request)
    {
        DB::beginTransaction();
        try{
            if($request->carrier_own) {
                $customer = Customer::find($request->customer_id);
                $customer->update([
                    'name' => $request->customer,
                    'mobile' => $request->phone
                ]);
            }

            Ticket::create([
                'date' =>  $request->date,
                'sl_number' =>  $request->sl_number,
                'purchase_id' =>  $request->purchase_id,
                'customer_reference' => $request->customer_reference,
                'product_category_id' =>  $request->product_category_id,
                'district_id' =>  $request->district_id,
                'thana_id' => $request->thana_id,
                'warranty_type_id'=>$request->warranty_type_id,
                'job_priority_id'=>$request->job_priority_id,
                'service_type_id'=>json_encode($request->service_type_id),
                'service_charge' => $request->service_charge,
                'fault_description_id'=>json_encode($request->fault_description_id),
                'product_condition_id'=>json_encode($request->product_condition_id),
                'start_date'=>$request->start_date,
                'end_date'=>$request->end_date,
                'customer_note'=>$request->customer_note,
                'product_receive_mode_id' => $request->product_receive_mode_id,
                'expected_delivery_mode_id' => $request->expected_delivery_mode_id,
                'accessories_list_id' => json_encode($request->accessories_list_id),
                'created_by' => Auth::id(),
            ]);
            DB::commit();
            return redirect('tickets/ticket-index')
            ->with('success', __('label.NEW_TICKET_CREATED'));
        } catch (\Exception $e) {
            DB::rollback();
            $bug = $e->getMessage();
            return redirect()->back()->with('error', $bug);
        }
    }

    public function editTicket($id)
    {
        try{
            $ticket=Ticket::findOrFail($id);
            $purchase=Purchase::where('id',$ticket->purchase_id)->first();
            $job_priorities   = JobPriority::where('status', 1)->latest()->get();
            $productCategorys =  [];
            $product_conditions = ProductCondition::where('status', 1)->latest()->get();
            $accessories_list = Accessories::where('status', 1)
                    ->where('product_id',$purchase->product_category_id )
                    ->latest()->get();

            $warrantyTypes = WarrantyType::where('status', 1)->get();
            $districts = District::all();
            $thanas = Thana::all();
            $faults = Fault::where('category_id',$purchase->product_category_id )
                        ->where('status', 1)
                        ->pluck('name','id')->toArray();

            $serviceTypes     = ServiceType::where('category_id',$purchase->product_category_id )->latest()->get();
            return view('ticket.purchaseHistory.edit_ticket', compact('productCategorys','warrantyTypes','districts','faults', 'serviceTypes', 'ticket', 'purchase', 'job_priorities', 'thanas', 'product_conditions', 'accessories_list'));
        } catch (\Exception $e) {
            $bug = $e->getMessage();
            return redirect()->back()->with('error', $bug);
        }
    }

    public function updateTicket(storeTicketRequest $request, $id)
    {
        try{
            $ticket = Ticket::find($id);
            // $ticket->create($request->all());

            if($request->carrier_own) {
                $customer = Customer::find($request->customer_id);
                $customer->update([
                    'name' => $request->customer,
                    'mobile' => $request->phone
                ]);
            }

            $ticket->update([
                'date' => $request->date,
                'sl_number' => $request->sl_number,
                'product_category_id' => $request->product_category_id,
                'purchase_id' => $request->purchase_id,
                'warranty_type_id' => $request->warranty_type_id,
                'job_priority_id' => $request->job_priority_id,
                // 'service_type_id' => $request->service_type_id,
                'service_type_id'=>json_encode($request->service_type_id),
                'service_charge' => $request->service_charge,
                'product_condition_id' => $request->product_condition_id,
                'customer_id' => $request->customer_id,
                'district_id' => $request->district_id,
                'thana_id' => $request->thana_id,
                'fault_description_id' => $request->fault_description_id,
                'accessories_list_id' => $request->accessories_list_id,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'product_receive_mode_id' => $request->product_receive_mode_id,
                'expected_delivery_mode_id' => $request->expected_delivery_mode_id,
                'customer_note' => $request->customer_note,
                'updated_by' => Auth::id()
            ]);

            return back()->with('success', 'Ticket updated successfully');
        } catch (\Exception $e) {
            $bug = $e->getMessage();
            return redirect()->back()->with('error', $bug);
        }
    }

    public function showTicket($id)
    {
        try{
            $ticket = Ticket::find($id);
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
            // dd($customerFeedbacks);
            return view('ticket.purchaseHistory.show_ticket', compact('ticket', 'faults', 'warrantyTypes', 'product_conditions', 'accessories_lists', 'questions', 'ticketId', 'customerFeedbacks', 'serviceTypes'));
        } catch (\Exception $e) {
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
        } catch (\Exception $e) {
            $bug = $e->getMessage();
            return redirect()->back()->with('error', $bug);
        }
    }

    //Ticket CLosing By Team Leader
    public function closeByTeamleader($id)
    {
        try{
            $ticket = Ticket::find($id);
            $ticket->update([
                'is_closed_by_teamleader'=>1
            ]);
            return redirect()->back()->with('success', __('Ticket CLosed Successfully.'));
        } catch (\Exception $e) {
            $bug = $e->getMessage();
            return redirect()->back()->with('error', $bug);
        }
    }
    // Ticket Re-Open
    public function reOpen(Request $request){
        try {
            $ticket=Ticket::find($request->ticket_id);
            $ticket->update([
                'is_reopened'=> 1,
                'reopen_note'=> $request->note,
            ]);
            return redirect('tickets/ticket-index')->with('success', __('Ticket Re-Opened Successfully.'));
        } catch (\Exception $e) {
            $bug = $e->getMessage();
            return redirect()->back()->with('error', $bug);
        }
    }

    public function distroyTicket($id){
        try {
            Ticket::findOrFail($id)->delete();
            return back()->with('success', __('Ticket deleted successfully.'));
        } catch (\Exception $e) {
            $bug = $e->getMessage();
            return redirect()->back()->with('error', $bug);
        }
    }

    public function serviceAmount(Request $request)
    {
        // dd($request->id);
        $serviceTypes= $request->id;
        $sum=0;
        // $serviceTypes=json_decode($request->id);
        // dd($serviceTypes);
        foreach($serviceTypes as $serviceType){
            // dd($serviceType);
            $serviceTypeValue = ServiceType::where('id', $serviceType)->value('service_amount');
            $sum+= $serviceTypeValue;
            // dd($serviceTypeValue);
        }
        // dd($sum);
        // dd($serviceTypes);
        // $serviceType = ServiceType::where('id', $id)->value('service_amount');
        // return $serviceType;
        return $sum;
    }

    public function status($id)
    {
        $auth = Auth::user();
        $user_role = $auth->roles->first();

        try {
            switch($id) {
                case 1:
                    // pending
                    if($user_role->name == 'Super Admin') {
                        $tickets=Ticket::where('status', 0)->latest()->get();
                        // Count total by status
                        $totals = $this->totalTicketStatus();
                    }elseif($user_role->name == 'Admin') {
                        $tickets=Ticket::where('status', 0)->latest()->get();
                        // Count total by status
                        $totals = $this->totalTicketStatus();
                    }else {
                        $teamleader = TeamLeader::where('user_id', $auth->id)->first();
                        $district_id = json_decode($teamleader->group->region->district_id, true);
                        $product_category_id = json_decode($teamleader->group->category_id, true);
                        $tickets=Ticket::whereIn('district_id',[$district_id])
                                    ->where('status', 0)
                                    ->where('product_category_id',$product_category_id)
                                    ->latest()->get();

                        $totals = $this->teamleaderTotalTicketStatus($district_id, $product_category_id);
                    }
                    break;
                case 2:
                    // Canelled
                    if($user_role->name == 'Super Admin') {
                        $tickets=Ticket::where('status', 2)
                                ->where('is_rejected', 1)
                                ->latest()->get();
                        // Count total by status
                        $totals = $this->totalTicketStatus();
                    }elseif($user_role->name == 'Admin') {
                        $tickets=Ticket::where('status', 2)
                                ->where('is_rejected', 1)
                                ->latest()->get();
                        // Count total by status
                        $totals = $this->totalTicketStatus();
                    }else {
                        $teamleader = TeamLeader::where('user_id',$auth->id)->first();
                        $district_id = json_decode($teamleader->group->region->district_id, true);
                        $product_category_id = json_decode($teamleader->group->category_id, true);

                        $tickets=Ticket::whereIn('district_id',[$district_id])
                                    ->where('status', 2)
                                    ->where('is_rejected', 1)
                                    ->where('product_category_id',$product_category_id)
                                    ->latest()->get();

                        $totals = $this->teamleaderTotalTicketStatus($district_id, $product_category_id);
                    }
                    break;

                case 3:
                    // Ticket re-open
                    if($user_role->name == 'Super Admin') {
                        $tickets=Ticket::where('status', 1)
                                ->where('is_ended', 1)
                                ->where('is_closed_by_teamleader', 1)
                                ->where('is_reopened', 1)
                                ->latest()->get();
                        // Count total by status
                        $totals = $this->totalTicketStatus();
                    }elseif($user_role->name == 'Admin') {
                        $tickets=Ticket::where('status', 1)
                                ->where('is_ended', 1)
                                ->where('is_closed_by_teamleader', 1)
                                ->where('is_reopened', 1)
                                ->latest()->get();
                        // Count total by status
                        $totals = $this->totalTicketStatus();
                    }else {
                        $teamleader = TeamLeader::where('user_id',$auth->id)->first();
                        $district_id = json_decode($teamleader->group->region->district_id, true);
                        $product_category_id = json_decode($teamleader->group->category_id, true);

                        $tickets=Ticket::whereIn('district_id',[$district_id])
                                    ->where('status', 1)
                                    ->where('is_ended', 1)
                                    ->where('is_closed_by_teamleader', 1)
                                    ->where('is_reopened', 1)
                                    ->latest()->get();

                        $totals = $this->teamleaderTotalTicketStatus($district_id, $product_category_id);
                    }
                    break;

                case 4:
                    // Ticket Closed
                    if($user_role->name == 'Super Admin') {
                        $tickets=Ticket::where('status', 1)
                                ->where('is_ended', 1)
                                ->where('is_closed', 1)
                                ->latest()->get();
                        // Count total by status
                        $totals = $this->totalTicketStatus();
                    }elseif($user_role->name == 'Admin') {
                        $tickets=Ticket::where('status', 1)
                                ->where('is_ended', 1)
                                ->where('is_closed', 1)
                                ->latest()->get();
                        // Count total by status
                        $totals = $this->totalTicketStatus();
                    }else {
                        $teamleader = TeamLeader::where('user_id',$auth->id)->first();
                        $district_id = json_decode($teamleader->group->region->district_id, true);
                        $product_category_id = json_decode($teamleader->group->category_id, true);

                        $tickets=Ticket::whereIn('district_id',[$district_id])
                                    ->where('status', 1)
                                    ->where('is_ended', 1)
                                    ->where('is_closed', 1)
                                    ->latest()->get();

                        $totals = $this->teamleaderTotalTicketStatus($district_id, $product_category_id);
                    }
                    break;
                default:
                    return redirect()->route('ticket-index');
            }

            return view('ticket.purchaseHistory.ticket_status', compact('tickets', 'totals', 'id'));
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
        } catch (\Exception $e) {
            $bug = $e->getMessage();
            return redirect()->back()->with('error', $bug);
        }
    }

    protected function totalTicketStatus()
    {
        try {
            return DB::table('tickets')
                ->selectRaw('count(*) as total')
                ->selectRaw("count(case when status = 0 then 1 end) as pending")
                ->selectRaw("count(case when status = 1 and is_ended = 1 and is_closed_by_teamleader = 1 and is_reopened = 1 then 1 end) as ticketReOpened")
                ->selectRaw("count(case when status = 1 and is_ended = 1 and is_closed = 1 then 1 end) as ticketClosed")
                ->selectRaw("count(case when status = 1 and is_ended = 1 then 1 end) as jobCompleted")
                ->selectRaw("count(case when status = 1 and is_accepted = 1 then 1 end) as jobAccepted")
                ->selectRaw("count(case when status = 1 and is_assigned = 1 then 1 end) as assigned")
                ->selectRaw("count(case when status = 2 and is_rejected = 1 then 1 end) as rejected")
                ->first();
        }catch (\Exception $e) {
            $bug = $e->getMessage();
            return redirect()->back()->with('error', $bug);
        }

    }

    protected function teamleaderTotalTicketStatus($district_id, $product_category_id)
    {
        try {
            return DB::table('tickets')->whereIn('district_id',[$district_id])
                ->where('product_category_id',$product_category_id)
                ->selectRaw('count(*) as total')
                ->selectRaw("count(case when status = 0 then 1 end) as pending")
                ->selectRaw("count(case when status = 1 and is_ended = 1 and is_closed_by_teamleader = 1 and is_reopened = 1 then 1 end) as ticketReOpened")
                ->selectRaw("count(case when status = 1 and is_ended = 1 and is_closed = 1 then 1 end) as ticketClosed")
                ->selectRaw("count(case when status = 1 and is_ended = 1 then 1 end) as jobCompleted")
                ->selectRaw("count(case when status = 1 and is_accepted = 1 then 1 end) as jobAccepted")
                ->selectRaw("count(case when status = 1 and is_assigned = 1 then 1 end) as assigned")
                ->selectRaw("count(case when status = 2 and is_rejected = 1 then 1 end) as rejected")
                ->first();
        }catch (\Exception $e) {
            $bug = $e->getMessage();
            return redirect()->back()->with('error', $bug);
        }
    }
}
