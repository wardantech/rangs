<?php

namespace App\Http\Controllers\Loan;

use DB;
use Auth;
use App\Models\Loan\Loan;
use Illuminate\Http\Request;
use App\Models\Inventory\Bin;
use App\Models\Inventory\Rack;
use App\Models\Inventory\Store;
use App\Models\Loan\LoanDetails;
use App\Models\Employee\Employee;
use App\Models\Loan\ReceivedLoan;
use App\Http\Controllers\Controller;
use App\Models\Loan\AcceptLoanRequest;
use App\Models\Inventory\InventoryStock;
use App\Models\Loan\ReceivedLoanDetails;
use App\Models\Inventory\RackBinManagement;
use App\Models\Loan\AcceptLoanRequestDetails;

class AcceptLoanRequestController extends Controller
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
                $requisitions=Loan::where('status', 1)->latest()->get();
                return view('loan_views.accept_loan.index', compact('requisitions'));
            }else {
                $employee=Employee::where('user_id',Auth::user()->id)->first();
                // $mystore=Store::where('outlet_id',$employee->outlet_id)->first();
                $mystore=Store::where('id',$employee->store_id )->first();
                if ($mystore != null) {
                    $requisitions=Loan::where('status', 1)->where('store_id', $mystore->id)->latest()->get();
                    return view('loan_views.accept_loan.index', compact('requisitions'));
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

    }

    public function issueLoan($id)
    {
        try{
            $loan = Loan::find($id);
            $loan_details = LoanDetails::where('loan_id', $loan->id)->with('part','part_model')->get();
            $rackbinInfo = [];
            $stock_collect = [];
            foreach($loan_details as $key=>$detail){
                $rackbin=RackBinManagement::where('parts_id',$detail->parts_id)->where('store_id',$loan->store_id)->first();
                $stock_in = InventoryStock::where('part_id',$detail->parts_id)->where('store_id', $loan->store_id)->sum('stock_in');
                $stock_out = InventoryStock::where('part_id',$detail->parts_id)->where('store_id', $loan->store_id)->sum('stock_out');
                $stock_in_hand = $stock_in - $stock_out;
                array_push($rackbinInfo, $rackbin);
                array_push($stock_collect,$stock_in_hand);
            }
            return view('loan_views.accept_loan.create', compact('loan','loan_details','stock_collect','rackbinInfo'));
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
        DB::beginTransaction();
        try {
            $loan = Loan::find($request->loan_id);
            $data = $request->all();
            $data['loan_id'] = $request->loan_id;
            $data['store_id'] = $loan->store_id;
            $data['date'] = $request->issue_date;
            $data['status'] = 2;
            $data['issue_quantity'] = array_sum($request->issue_quantity);
            $acceptLoanRequest = AcceptLoanRequest::create($data);

            $loan->update([
                'status'=> 2,
                'issued_quantity' => array_sum($request->issue_quantity),
            ]);

            foreach($request->issue_quantity as $key => $quantity){
                    
                    LoanDetails::where('loan_id',$request->loan_id)
                    ->where('parts_id',$request->part_id[$key])
                    ->update([
                        'issued_quantity' => $quantity
                    ]);
                    
                        $allo_details['accept_loan_request_id'] = $acceptLoanRequest->id;
                        $allo_details['parts_id'] = $request->part_id[$key];
                        $allo_details['rack_id'] = $request->rack_id[$key];
                        $allo_details['bin_id'] = $request->bin_id[$key];
                        $allo_details['requisition_quantity'] = $request->required_quantity[$key];
                        $allo_details['issued_quantity'] = $quantity;
                        AcceptLoanRequestDetails::create($allo_details);

                    //stock in for outlate
                    InventoryStock::create([
                        'accept_loan_request_id'=>$acceptLoanRequest->id,
                        'belong_to' =>  2, //2=Branch
                        'store_id' =>  $loan->store_id,
                        'part_id' => $request->part_id[$key],
                        'stock_out' => $request->issue_quantity[$key],
                        'created_by' => Auth::id(),
                    ]);

            }
            DB::commit();
            return redirect()->route('loan.accept-loan.index')->with('success', __('New loan allocation created successfully.'));
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
            $loan=Loan::find($id);
            $loanDetails= LoanDetails::where('loan_id', $id)->get();
            $acceptLoan= AcceptLoanRequest::where('loan_id', $id)->first();
            $acceptLoanDetails= '';
            $receivedLoanDetails= '';
            if($acceptLoan){
                $acceptLoanDetails= AcceptLoanRequestDetails::where('accept_loan_request_id', $acceptLoan->id)->get();
            }
            $receivedLoan= ReceivedLoan::where('loan_id', $id)->first();
            if($receivedLoan){
                $receivedLoanDetails= ReceivedLoanDetails::where('received_loan_id', $receivedLoan->id)->get();
            }

            return view('loan_views.accept_loan.show_details', compact('loan', 'loanDetails', 'acceptLoan', 'acceptLoanDetails', 'receivedLoanDetails'));
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
            $loan = Loan::find($id);
            $details = LoanDetails::where('loan_id', $loan->id)->with('part','part_model')->get();
            $stock_collect = [];
            foreach($details as $key=>$detail){

                $stock_in = InventoryStock::where('part_id',$detail->parts_id)->where('store_id', $loan->store_id)->sum('stock_in');
                $stock_out = InventoryStock::where('part_id',$detail->parts_id)->where('store_id', $loan->store_id)->sum('stock_out');
                $stock_in_hand = $stock_in - $stock_out;
                array_push($stock_collect,$stock_in_hand);
            }
            return view('loan_views.accept_loan.edit', compact('loan','details','stock_collect'));
        } catch (\Exception $e) {
            $bug = $e->getMessage();
            return redirect()->back()->with('error', $bug);
        }
    }

    public function acceptedLoans()
    {
        try{
            $auth = Auth::user();
            $user_role = $auth->roles->first();
            $mystore='';
            if ($user_role->name == 'Super Admin' || $user_role->name == 'Admin') {
                $acceptedLoans= AcceptLoanRequest::with('loan')->latest()->get();
            } else {
                $employee = Employee::where('user_id',Auth::user()->id)->first();
                // $mystore = Store::where('outlet_id',$employee->outlet_id)->first();
                $mystore=Store::where('id',$employee->store_id )->first();
                if ($mystore != null) {
                    $acceptedLoans= AcceptLoanRequest::with('loan')->where('store_id', $mystore->id)->latest()->get();
                }else{
                    return redirect()->back()->with('error', __("Sorry you don't have the permission."));
                }
            }
            return view('loan_views.accepted_loans.index', compact('acceptedLoans'));
        } catch (\Exception $e) {
            $bug = $e->getMessage();
            return redirect()->back()->with('error', $bug);
        }
    }

    public function showForAllAcceptedLoans($id)
    {
        try{
            $acceptedLoans= AcceptLoanRequest::find($id);
            $details= AcceptLoanRequestDetails::where('accept_loan_request_id', $acceptedLoans->id)->get();

            return view('loan_views.accept_loan.show_for_all_accepted_loans', compact('acceptedLoans', 'details'));
        } catch (\Exception $e) {
            $bug = $e->getMessage();
            return redirect()->back()->with('error', $bug);
        }
    }

    public function editAcceptedLoan($id)
    {
        try{
            $acceptedLoan= AcceptLoanRequest::find($id);
            $acceptLoanRequestDetails= AcceptLoanRequestDetails::where('accept_loan_request_id', $acceptedLoan->id)->get();

            
            $stock_collect = [];
            foreach($acceptLoanRequestDetails as $key=>$detail){
                $stock_in = InventoryStock::where('part_id',$detail->parts_id)->where('store_id', $acceptedLoan->store_id)->sum('stock_in');
                $stock_out = InventoryStock::where('part_id',$detail->parts_id)->where('store_id', $acceptedLoan->store_id)->sum('stock_out');
                $stock_in_hand = $stock_in - $stock_out;
                array_push($stock_collect, $stock_in_hand);
            }
            return view('loan_views.accepted_loans.edit', compact('acceptedLoan', 'stock_collect', 'acceptLoanRequestDetails'));
        } catch (\Exception $e) {
            $bug = $e->getMessage();
            return redirect()->back()->with('error', $bug);
        }
    }

    public function updateAcceptedLoan($id, Request $request){
        DB::beginTransaction();
        try {
        $acceptedLoan= AcceptLoanRequest::find($id);
        $loan = Loan::find($acceptedLoan->loan_id);
            $data = $request->all();

            $data['issue_quantity'] = array_sum($request->issue_quantity);
            $issue_quantity=array_sum($request->issue_quantity);
            $loan->update([
                'issued_quantity'=>$issue_quantity,
            ]);
            $acceptedLoan->update($data);

        $acceptLoanRequestDetails= AcceptLoanRequestDetails::where('accept_loan_request_id', $acceptedLoan->id)->get();

        foreach($acceptLoanRequestDetails as $key => $acceptLoanRequestDetail){
            $inventoryStock= InventoryStock::where('accept_loan_request_id', $acceptedLoan->id)->where('part_id',$acceptLoanRequestDetail->parts_id)->first();
            if($acceptLoanRequestDetail != null){

                if($acceptLoanRequestDetail){
                    $acceptLoanRequestDetail->update([
                        'issued_quantity' => $request->issue_quantity[$key],
                    ]);
                }
                    $inventoryStock->update([
                    'stock_out' => $request->issue_quantity[$key],
                ]);
            }
        }
        DB::commit();
            return redirect()->route('loan.accepted-loans')->with('success', __('Accepted loan updated successfully.'));
        } catch (\Exception $e) {
            DB::rollback();
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
        //
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
            $acceptedLoan= AcceptLoanRequest::find($id);
            $loan = Loan::find($acceptedLoan->loan_id);

            if ($acceptedLoan != null) {
                $acceptLoanRequestDetails= AcceptLoanRequestDetails::where('accept_loan_request_id', $acceptedLoan->id)->get();
            
                foreach($acceptLoanRequestDetails as $key => $acceptLoanRequestDetail){
                    if ($acceptLoanRequestDetail !=null ) {
                        $inventoryStock= InventoryStock::where('accept_loan_request_id', $acceptedLoan->id)->where('part_id',$acceptLoanRequestDetail->parts_id)->first();
                        if($inventoryStock != null){
                            $inventoryStock->delete();
                        }
                        $acceptLoanRequestDetail->delete();
                    }
                }
                $acceptedLoan->delete();
                
                $loan->update([
                    'status' => 1,
                    'issued_quantity' => 0,
                ]);
            }

            DB::commit();
                return redirect()->route('loan.accepted-loans')->with('success', __('Accepted Loan Deleted successfully.'));
            } catch (\Exception $e) {
                DB::rollback();
                $bug = $e->getMessage();
                return redirect()->back()->with('error', $bug);
            }
    }
}
