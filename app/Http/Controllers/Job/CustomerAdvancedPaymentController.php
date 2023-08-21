<?php

namespace App\Http\Controllers\Job;

use DB;
use Auth;
use App\Models\JobModel\Job;
use Illuminate\Http\Request;
use App\Models\Outlet\Outlet;
use App\Models\Inventory\Store;
use App\Models\Employee\Employee;
use App\Http\Controllers\Controller;
use App\Models\Job\CustomerAdvancedPayment;

class CustomerAdvancedPaymentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try{
            $customerAdvancePayments= DB::table('customer_advanced_payments')
                                        ->where('customer_advanced_payments.deleted_at', NULL)
                                        // ->where('customer_advanced_payments.created_by', Auth::user()->id)
                                        ->join('outlets', 'outlets.id', '=', 'customer_advanced_payments.branch_id')
                                        ->join('jobs', 'jobs.id', '=', 'customer_advanced_payments.job_id')
                                        ->select('customer_advanced_payments.*', 'outlets.name as branch_name', 'jobs.job_number as jobnumber')->get();
                                        // dd($customerAdvancePayments);
            return view('customer_advanced_payment.index', compact('customerAdvancePayments'));
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

    public function createPayment($id)
    {
        try{
            $auth = Auth::user();
            $user_role = $auth->roles->first();

            $branches = Outlet::where('status', 1)->orderBy('name')->get();
            $job= Job::find($id);
            $CustomerAdvancedPayment= CustomerAdvancedPayment::latest()->first();
            if(!empty($CustomerAdvancedPayment)){
                $trim=trim($CustomerAdvancedPayment->adv_mr_no,"MR-NO-");
                $sl=$trim + 1;
                $sl_number="MR-NO-".$sl;
            }else{
                $sl_number="MR-NO-"."1";
            }

            $outlet='';
            $employee=Employee::where('user_id',Auth::user()->id)->first();
            if ($employee != null) {
                $outlet=Outlet::where('id',$employee->outlet_id)->first();
                if($outlet == null){
                    return redirect()->back()->with('error', __('Sorry you dont have the permission.'));
                }
            }
            return view('customer_advanced_payment.create', compact('branches', 'job','sl_number','user_role','outlet'));
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
            "adv_mr_no" => "required",
            "advance_receipt_date" => "required",
            "job_id" => "required",
            "job_no" => "nullable",
            "branch_id" => "nullable",
            "customer_name" => "nullable",
            "customer_phone" => "nullable",
            "customer_address" => "nullable",
            "receive_date" => "nullable",
            "product_name" => "nullable",
            "product_sl" => "nullable",
            "advance_amount" => "required",
            "pay_type" => "nullable",
            "remark" => "nullable|string",
        ]);
        try {
            $job = Job::findOrFail($request->job_id);
            $job->update([
                'is_advanced_payment'=> 1,
            ]);
            $input = $request->except('job_no');
            CustomerAdvancedPayment::create($input);
            return redirect()->route('customer-advanced-payment.index')
                ->with('success', __('New advance payment created successfully.'));
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
            $customerAdvancedPayment= CustomerAdvancedPayment::find($id);
            return view('customer_advanced_payment.show', compact('customerAdvancedPayment'));
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
            $branches=Outlet::where('status', 1)->get();
            $customerAdvancePayment= CustomerAdvancedPayment::find($id);
            return view('customer_advanced_payment.edit', compact('branches', 'customerAdvancePayment'));
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
            "adv_mr_no" => "required",
            "advance_receipt_date" => "required",
            "job_id" => "required",
            "job_no" => "nullable",
            "branch_id" => "nullable",
            "customer_name" => "nullable",
            "customer_phone" => "nullable",
            "customer_address" => "nullable",
            "receive_date" => "nullable",
            "product_name" => "nullable",
            "product_sl" => "nullable",
            "advance_amount" => "required",
            "pay_type" => "nullable",
            "remark" => "nullable|string",
        ]);
        try{
            $customerAdvancePayment= CustomerAdvancedPayment::find($id);
            $customerAdvancePayment->update($request->all());

            return redirect()->route('customer-advanced-payment.index')->with('success', __('Advance payment updated successfully.'));
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
            $customerAdvancedPayment=CustomerAdvancedPayment::findOrFail($id);
            if ($customerAdvancedPayment) {
                $job = Job::findOrFail($customerAdvancedPayment->job_id);
                $job->update([
                    'is_advanced_payment'=> 1,
                ]);
                $customerAdvancedPayment->delete();
            }
            
            return redirect()->route('customer-advanced-payment.index')->with('success', __('Advance payment deleted successfully.'));
        } catch (\Exception $e) {
            $bug = $e->getMessage();
            return redirect()->back()->with('error', $bug);
        }
    }
}
