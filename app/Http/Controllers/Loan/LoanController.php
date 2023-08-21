<?php

namespace App\Http\Controllers\Loan;

use Auth;
use Carbon\Carbon;
use App\Models\Loan\Loan;
use Illuminate\Http\Request;
use App\Models\Outlet\Outlet;
use App\Models\Inventory\Rack;
use App\Models\Inventory\Parts;
use App\Models\Inventory\Store;
use App\Models\Loan\LoanDetails;
use App\Models\Employee\Employee;
use App\Models\Loan\ReceivedLoan;
use App\Http\Controllers\Controller;
use App\Models\Loan\AcceptLoanRequest;
use App\Models\Inventory\InventoryStock;
use App\Models\Loan\ReceivedLoanDetails;
use App\Models\Inventory\PriceManagement;
use App\Models\Inventory\RackBinManagement;
use App\Models\Loan\AcceptLoanRequestDetails;

class LoanController extends Controller
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
            if ($user_role->name == 'Super Admin' || $user_role->name == 'Admin') {
                $loans=Loan::where('status', 1)->latest()->get();
                return view('loan_views.index', compact('loans'));
            }else {
                $employee=Employee::where('user_id',Auth::user()->id)->first();
                // $mystore=Store::where('outlet_id',$employee->outlet_id)->first();
                $mystore=Store::where('id',$employee->store_id )->first();

                if ($mystore != null) {
                    $loans=Loan::where('status', 1)->where('from_store_id', $mystore->id)->latest()->get();
                    return view('loan_views.index', compact('loans'));
                }else{
                    return redirect()->back()->with('error', __("Sorry! you don't have the access."));
                }            
            }
        } catch (\Exception $e) {
            $bug = $e->getMessage();
            return redirect()->back()->with('error', $bug);
        }   
    }

    public function allocatedList()
    {
        try{
            $auth = Auth::user();
            $user_role = $auth->roles->first();
            if ($user_role->name == 'Super Admin' || $user_role->name == 'Admin') {
                // $acceptedLoans=Loan::where('status', 2)->latest()->get();
                $acceptedLoans=AcceptLoanRequest::with('loan')->latest()->get();
                return view('loan_views.allocated_index', compact('acceptedLoans'));
            }else {
                $employee=Employee::where('user_id',Auth::user()->id)->first();
                // $mystore=Store::where('outlet_id',$employee->outlet_id)->first();
                $mystore=Store::where('id',$employee->store_id )->first();
                if ($mystore != null) {
                    $acceptedLoans=AcceptLoanRequest::with('loan')->where('to_store_id', $mystore->id)->latest()->get();
                    return view('loan_views.allocated_index', compact('acceptedLoans'));
                }else{
                    return redirect()->back()->with('error', __("Sorry! you don't have the access."));
                }             
            }
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
        try{
            $auth = Auth::user();
            $user_role = $auth->roles->first();
            if ($user_role->name == 'Super Admin' || $user_role->name == 'Admin') {
                $mystore='';
            } else {
                $employee=Employee::where('user_id',Auth::user()->id)->first();
                // $mystore=Store::where('outlet_id',$employee->outlet_id)->first();
                $mystore=Store::where('id',$employee->store_id )->first();
            }

            $outlates = Outlet::where('status', 1)->latest()->get();
            $stores = Store::where('status', 1)->where('user_id',Null)->where('name', 'not like', "%central warehouse%")->orderBy('name')->get();

            $loan=Loan::latest()->first();
            if(!empty($loan)){
                $trim=trim($loan->loan_no,"PT-NO-");
                $sl=$trim + 1;
                $sl_number="PT-NO-".$sl;
            }else{
                $sl_number="PT-NO-"."1";
            }

            return view('loan_views.create', compact('outlates','stores', 'sl_number', 'mystore', 'user_role'));
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
            'loan_no'           => 'required|string',
            'date'              => 'required',
            'from_store_id'     => 'nullable|numeric',
            'store_id'          => 'required|numeric',
            'parts_id'          => 'nullable|numeric',
            'parts_model_id'    => 'nullable',
            'stock_in_hand'     => 'nullable|array',
            'model_id'          => 'nullable|array',
            'required_quantity' => 'nullable|array',
            'part_id'           => 'nullable|array',
        ]);

        $total_quantity = array_sum($request->required_quantity);
        try {
            $loan = Loan::create([
                'loan_no' => $request->loan_no,
                'date' => $request->date,
                'store_id' => $request->store_id,
                'from_store_id' => $request->from_store_id,
                // 'belong_to' => 1,
                'status' => 1,
                'total_quantity' => $total_quantity,
                'created_by' => Auth::id(),
            ]);
            
            if($loan){
                foreach($request->part_id as $key => $id){
                    if($id != null &&  $id > 0){
                        $details['loan_id'] = $loan->id;
                        $details['parts_id'] = $id;
                        $details['stock_in_hand'] = $request->stock_in_hand[$key];
                        $details['required_quantity'] = $request->required_quantity[$key];
                        LoanDetails::create($details);
                    }
                }
            }
            return redirect()->route('loan.loan-request.index')->with('success', __('New loan created successfully.'));
        } catch (\Exception $e) {
            $bug = $e->getMessage();
            return redirect()->back()->with('error', $bug);
        }
    }

    public function loanReceive($id)
    {
        try{
            $acceptLoanRequest = AcceptLoanRequest::find($id);
            $acceptLoanRequestDetails = AcceptLoanRequestDetails::where('accept_loan_request_id', $id)->with('part')->get();

            $rackbinInfo = [];
            $stock_collect = [];
            foreach($acceptLoanRequestDetails as $key=>$detail){
                $rackbin=RackBinManagement::where('parts_id',$detail->parts_id)->where('store_id',$acceptLoanRequest->to_store_id)->first();
                $stock_in = InventoryStock::where('part_id',$detail->parts_id)->where('store_id', $acceptLoanRequest->to_store_id)->sum('stock_in');
                $stock_out = InventoryStock::where('part_id',$detail->parts_id)->where('store_id', $acceptLoanRequest->to_store_id)->sum('stock_out');
                $stock_in_hand = $stock_in - $stock_out;
                array_push($rackbinInfo, $rackbin);
                array_push($stock_collect,$stock_in_hand);
            }

            return view('loan_views.receive_loan', compact('acceptLoanRequest', 'acceptLoanRequestDetails', 'stock_collect','rackbinInfo'));
        } catch (\Exception $e) {
            $bug = $e->getMessage();
            return redirect()->back()->with('error', $bug);
        }
    }

    public function loanReceiveStore(Request $request)
    {
        try {
            $acceptLoanRequest = AcceptLoanRequest::find($request->allocation_id);
            $loan = Loan::find($acceptLoanRequest->loan_id);
            $received_quantity = array_sum($request->receiving_quantity);

        if($acceptLoanRequest != null){
            $acceptLoanRequest->update([
                'status' => 3,
                'total_received_quantity' => $received_quantity
            ]);

            $receivedLoan=ReceivedLoan::create([
                        'loan_id'                 => $loan->id,
                        'accept_loan_requests_id'    => $acceptLoanRequest->id,
                        'date'                    => $request->date,
                        'to_store_id'             => $loan->store_id,
                        'from_store_id'           => $loan->from_store_id,
                        'total_received_quantity' => $received_quantity,
                        'total_issued_quantity'   => $acceptLoanRequest->issue_quantity,
                    ]);
        }

        if($loan != null){
            $loan->update([
                'status' => 3,
                'received_quantity' => $received_quantity,
            ]);
        }

        foreach($request->receiving_quantity as $key=> $value){
            // $priceManagement=PriceManagement::where('part_id',$request->part_id)->first();

            $inventoryStock= InventoryStock::where('accept_loan_request_id', $acceptLoanRequest->id)->where('part_id',$request->part_id[$key])->first();

            $acceptLoanRequestDetails= AcceptLoanRequestDetails::where('accept_loan_request_id', $acceptLoanRequest->id)->where('parts_id',$request->part_id[$key])->first();

            LoanDetails::where('loan_id',$acceptLoanRequest->loan_id)
            ->where('parts_id',$request->part_id[$key])
            ->update([
                'received_quantity' => $request->receiving_quantity[$key]
            ]);

            $acceptLoanRequestDetails->update([
                'received_quantity' => $request->receiving_quantity[$key]
            ]);
                //stock out from central wirehouse
                if($request->receiving_quantity[$key] > 0){
                    $received_details['received_loan_id'] = $receivedLoan->id;
                    $received_details['accept_loan_request_details_id'] = $acceptLoanRequestDetails->id;
                    $received_details['part_id'] = $request->part_id[$key];
                    $received_details['rack_id'] = $request->rack_id[$key];
                    $received_details['bin_id'] = $request->bin_id[$key];;
                    $received_details['received_quantity'] = $request->receiving_quantity[$key];
                    $received_details['created_by'] = Auth::id();

                    $receivedLoan_details=ReceivedLoanDetails::create($received_details);

                    $inventoryStock->update([
                        'stock_out' => $request->receiving_quantity[$key],
                        'updated_by' => Auth::id(),
                    ]);
                    $inventoryStock->update([
                        'stock_out' => $request->receiving_quantity[$key],
                        'updated_by' => Auth::id(),
                    ]);
                    InventoryStock::create([
                        'received_loan_id' => $receivedLoan->id,
                        // 'received_loan_details_id' => $receivedLoan_details->id,
                        'belong_to' =>  2, // 2 = Branch
                        // 'price_management_id' => $priceManagement->id,
                        'store_id' =>  $loan->from_store_id,
                        'part_category_id' => $request->part_category_id[$key],
                        'part_id' => $request->part_id[$key],
                        'stock_in' => $request->receiving_quantity[$key],
                        'type' => 2,
                        'created_by' => Auth::id(),
                    ]);
                }
        }
        return redirect()->route('loan.received-loans')->with('success', __('Allocated parts received successfully.'));
        } catch (\Exception $e) {
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
            $loan= Loan::find($id);
            $loanDetails= LoanDetails::where('loan_id', $id)->get();
            $receivedLoan= ReceivedLoan::where('loan_id', $id)->first();

            return view('loan_views.show-details', compact('loan', 'loanDetails', 'receivedLoan'));
        } catch (\Exception $e) {
            $bug = $e->getMessage();
            return redirect()->back()->with('error', $bug);
        }
    }

    public function loanDetails(Request $request){
        $loanId= $request->id;
        $loanDetails= LoanDetails::where('loan_id', $loanId)->with('parts')->get();

        return response()->json([
            'detail' => $loanDetails
        ]);
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
            $parts=Parts::where('status', 1)->get();
            $loan= Loan::find($id);
            $loanDetails= LoanDetails::where('loan_id', $loan->id)->get();
            $partIds= [];
            $stock_collect= [];

            foreach($loanDetails as $loanDetail){
                $stock_in = InventoryStock::where('part_id',$loanDetail->parts_id)->where('store_id', $loan->store_id)->sum('stock_in');
                $stock_out = InventoryStock::where('part_id',$loanDetail->parts_id)->where('store_id', $loan->store_id)->sum('stock_out');
                $stock_in_hand = $stock_in - $stock_out;
                array_push($stock_collect, $stock_in_hand);
                $partIds[]= $loanDetail->parts_id;
            }
            return view('loan_views.edit', compact('parts','loan', 'loanDetails', 'partIds','stock_collect'));
        } catch (\Exception $e) {
            $bug = $e->getMessage();
            return redirect()->back()->with('error', $bug);
        }
    }

    public function getPartsRows(Request $request){
        $part_id = $request->parts_id;
        $loan_id = $request->loan_id;
        $store_id = $request->from_store_id;

        $old_parts_id = [];
        $previous_parts_id = LoanDetails::where('loan_id', $loan_id)->get();

        foreach($previous_parts_id as $key=>$parts_id){
            $id = $parts_id->parts_id;
            array_push($old_parts_id, $id);
        }

        $collectRequiredQuantity = [];
        foreach($part_id as $key => $id){

            if(in_array($id, $old_parts_id)){
                $required = LoanDetails::where('loan_id', $loan_id)->where('parts_id', $id)->select('required_quantity')->first();
                $data = $required->required_quantity;
            }else{
                $data = '';
            }
            array_push($collectRequiredQuantity, $data);
        }

        $part_id_array = [];
        $model_id_array = [];
        $stock_collect = [];
        $partInfo_collect = [];

        foreach($part_id as $key=>$pr_id){
            $stock_in = InventoryStock::where('part_id',$pr_id)->where('store_id',$store_id)->sum('stock_in');
            $stock_out = InventoryStock::where('part_id',$pr_id)->where('store_id',$store_id)->sum('stock_out');
            $partsInfo=Parts::where('id', $pr_id)->first();

            $stock_in_hand = $stock_in - $stock_out;
            array_push($stock_collect, $stock_in_hand);
            array_push($partInfo_collect, $partsInfo);
        }

        $html = view('loan_views.edit_parts_info', compact('partInfo_collect','stock_collect', 'collectRequiredQuantity'))->render();
        return response()->json(compact('html'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function updateLoan(Request $request, $id)
    {

        $this->validate($request, [
            'loan_no'           => 'required|string',
            'date'              => 'required',
            'from_store_id'     => 'nullable|numeric',
            'store_id'          => 'required|numeric',
            'parts_id'          => 'nullable|numeric',
            'stock_in_hand'     => 'nullable|array',
            'required_quantity' => 'nullable|array',
            'part_id'           => 'nullable|array',
        ]);

        $total_quantity = array_sum($request->required_quantity);
        $loan=Loan::find($id);

        try {
            $loan->update([
                'loan_no'        => $request->loan_no,
                'date'           => $request->date,
                'store_id'       => $request->store_id,
                'from_store_id'  => $request->from_store_id,
                'status'         => 1,
                'total_quantity' => $total_quantity,
                'updated_by'     => Auth::id(),
            ]);
            if($loan){
                foreach($request->part_id as $key => $id){
                    if($id != null){
                        $details = LoanDetails::where('loan_id', $loan->id)->where('parts_id',$id)->first();
                        $details->update([
                            'required_quantity' => $request->required_quantity[$key],
                            'updated_by'     => Auth::id(),
                        ]);

                    }
                }
            }
            return redirect()->route('loan.loan-request.index')->with('success', __('New loan created successfully.'));
        } catch (\Exception $e) {
            $bug = $e->getMessage();
            return redirect()->back()->with('error', $bug);
        }
    }

    public function allReceivedLoans()
    {
        try{
            $auth = Auth::user();
            $user_role = $auth->roles->first();
            $mystore='';

            if ($user_role->name == 'Super Admin' || $user_role->name == 'Admin') {
                $receivedLoans = ReceivedLoan::latest()->get();
                return view('loan_views.received-loans.index', compact('receivedLoans'));
            }else {
                $employee=Employee::where('user_id',Auth::user()->id)->first();
                // $mystore=Store::where('outlet_id',$employee->outlet_id)->first();
                $mystore=Store::where('id',$employee->store_id )->first();

                if ($mystore != null) {
                    $receivedLoans = ReceivedLoan::where('from_store_id', $mystore->id)->latest()->get();
                    return view('loan_views.received-loans.index', compact('receivedLoans'));
                }else{
                    return redirect()->back()->with('error', __("Sorry! you don't have the access."));
                }             
            }
        } catch (\Exception $e) {
            $bug = $e->getMessage();
            return redirect()->back()->with('error', $bug);
        }
    }

    public function editReceivedLoan($id)
    {
        try{
            $receivedloan = ReceivedLoan::find($id);
            $receivedLoanDetails= ReceivedLoanDetails::where('received_loan_id', $receivedloan->id)->get();

            $stock_collect = [];

            foreach($receivedLoanDetails as $key=>$detail){
                $stock_in = InventoryStock::where('part_id',$detail->part_id)->where('store_id', $receivedloan->from_store_id)->sum('stock_in');
                $stock_out = InventoryStock::where('part_id',$detail->part_id)->where('store_id', $receivedloan->from_store_id)->sum('stock_out');
                
                $stock_in_hand = $stock_in - $stock_out;
                array_push($stock_collect,$stock_in_hand);
            }

            return view('loan_views.received-loans.edit', compact('receivedloan', 'receivedLoanDetails', 'stock_collect'));
        } catch (\Exception $e) {
            $bug = $e->getMessage();
            return redirect()->back()->with('error', $bug);
        }
    }

    public function updateReceivedLoan(Request $request)
    {
        try {
        $loan = Loan::find($request->loan_id);
        $receivedLoan= ReceivedLoan::find($request->received_loan_id);

        $received_quantity = array_sum($request->receiving_quantity);

        $acceptLoanRequest = AcceptLoanRequest::where('loan_id',$loan->id)->first();

        if($acceptLoanRequest != null){
            $acceptLoanRequest->update([
                'total_received_quantity' => $received_quantity,
            ]);
        }

        if($loan != null){
            $loan->update([
                'received_quantity' => $received_quantity,
            ]);
        }

        
        if($receivedLoan != null){
            $receivedLoan->update([
                'total_received_quantity' => $received_quantity,
            ]);
        }
        foreach($request->receiving_quantity as $key=> $value){
            
            LoanDetails::where('loan_id', $request->loan_id)
            ->where('parts_id',$request->part_id[$key])
            ->update([
                'received_quantity' => $request->receiving_quantity[$key]
            ]);
            
            $acceptLoanRequestDetails= AcceptLoanRequestDetails::where('accept_loan_request_id', $acceptLoanRequest->id)->where('parts_id',$request->part_id[$key])
            ->update([
                'received_quantity' => $request->receiving_quantity[$key]
            ]);
            
            $acceptloan_inventoryStocks= InventoryStock::where('accept_loan_request_id', $acceptLoanRequest->id)->where('part_id',$request->part_id[$key])
            ->update([
                'stock_out' => $request->receiving_quantity[$key],
                'updated_by' => Auth::id(),
                ]);
            $receivedloan_inventoryStocks= InventoryStock::where('received_loan_id', $receivedLoan->id)->where('part_id',$request->part_id[$key])
            ->update([
                'stock_in' => $request->receiving_quantity[$key],
                'updated_by' => Auth::id(),
                ]);
    
            $receivedLoanDetails= ReceivedLoanDetails::where('received_loan_id', $receivedLoan->id)->where('part_id',$request->part_id[$key])->first(); 

            $receivedLoanDetails->update([
                'received_quantity' => $request->receiving_quantity[$key],
            ]);
        }
        return redirect()->route('loan.received-loans')->with('success', __('Received parts transfer updated successfully.'));
        } catch (\Exception $e) {
            $bug = $e->getMessage();
            return redirect()->back()->with('error', $bug);
        }
    }

    //Destroy Received Loan
    public function destroyReceivedLoan(Request $request, $id)
    {
        try {
        $receivedLoan= ReceivedLoan::find($id);
        $acceptLoanRequest = AcceptLoanRequest::find($receivedLoan->accept_loan_requests_id);

        $loan = Loan::find($acceptLoanRequest->loan_id);

        $receivedLoanDetails=ReceivedLoanDetails::where('received_loan_id',$receivedLoan->id)->get();


        if($acceptLoanRequest != null){
            $acceptLoanRequest->update([
                'total_received_quantity' => 0,
                'status' => 2,
            ]);
        }

        if($loan != null){
            $loan->update([
                'received_quantity' => 0,
                'status' => 2,
            ]);
        }

        foreach($receivedLoanDetails as $key=> $value){
            
            LoanDetails::where('loan_id', $loan->id)
            ->where('parts_id',$value->part_id)
            ->update([
                'received_quantity' => 0,
            ]);
            
            $acceptLoanRequestDetails= AcceptLoanRequestDetails::where('accept_loan_request_id', $acceptLoanRequest->id)->where('parts_id',$value->part_id)->first();
            if ($acceptLoanRequestDetails) {
                $acceptLoanRequestDetails->update([
                    'received_quantity' => 0,
                ]);
            }    

            $acceptloan_inventoryStocks= InventoryStock::where('accept_loan_request_id', $acceptLoanRequest->id)->where('part_id',$value->part_id)
            ->update([
                'stock_out' => $acceptLoanRequestDetails->issued_quantity,
                'updated_by' => Auth::id(),
                ]);
            $receivedloan_inventoryStocks= InventoryStock::where('received_loan_id', $receivedLoan->id)->where('part_id',$value->part_id)->first();
            if ($receivedloan_inventoryStocks != null) {
                $receivedloan_inventoryStocks->delete();
            }
    
            $receivedLoanDetails= ReceivedLoanDetails::where('received_loan_id', $receivedLoan->id)->where('part_id',$value->part_id)->first(); 
            if ($receivedLoanDetails != null) {
                $receivedLoanDetails->delete();
            }
            
        }

        if($receivedLoan != null){
            $receivedLoan->delete();
        }

        return redirect()->route('loan.received-loans')->with('success', __('Received Parts Transfer Deleted Successfully.'));
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
            Loan::findOrFail($id)->delete();
            return redirect()->route('loan.loan-request.index')->with('success', __('Part Transfer Request deleted successfully.'));
        } catch (\Exception $e) {
            $bug = $e->getMessage();
            return redirect()->back()->with('error', $bug);
        }
    }
}
