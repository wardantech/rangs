<?php

namespace App\Http\Controllers\Consumption;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Consumption\PartWithdraw;
use App\Models\Consumption\PartWithdrawDetails;
use App\Models\Ticket\Accessories;
use App\Models\Inventory\Fault;
use App\Models\Job\JobCloseRemark;
use App\Models\Job\JobPendingNote;
use App\Models\Job\JobPendingRemark;
use App\Models\Ticket\ServiceType;
use App\Models\Employee\TeamLeader;
use DataTables;
use Carbon\Carbon;
use DB;
use Auth;
use App\Models\JobModel\Job;
use App\Models\Inventory\InventoryStock;
use App\Models\Job\JobSubmission;
use App\Models\Job\JobSubmissionDetails;

class WithdrawController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try{
        $partwithdraw=PartWithdraw::with('job')->where('status', 0)->latest()->get();
        
        if (request()->ajax()) {
            return DataTables::of($partwithdraw)

                ->addColumn('created_at', function ($partwithdraw) {
                    $created_at=Carbon::parse($partwithdraw->job_created_at)->format('m/d/Y');
                    return $created_at;
                })
                ->addColumn('created_by', function ($partwithdraw) {
                    $created_by=$partwithdraw->createdBy->name; 
                    return $created_by;
                })

                ->addColumn('ticket_sl', function ($partwithdraw) {
                    return '<a href="'.route('show-ticket-details', $partwithdraw->job->ticket_id).'" class="badge badge-primary" title="Ticket Details">'.'TSL-'.''. $partwithdraw->job->ticket_id.'</a>';
                })
                ->addColumn('job_number', function ($partwithdraw) {
                    $job_number='JSL-'.$partwithdraw->job_id; 
                    return $job_number;
                })

                ->addColumn('product_category', function ($partwithdraw) {
                    $product_category=$partwithdraw->job->ticket->purchase->category->name ?? Null;
                    return $product_category;
                })
                ->addColumn('brand_name', function ($partwithdraw) {
                    $brand_name=$partwithdraw->job->ticket->purchase->brand->name ?? Null;
                    return $brand_name;
                })
                ->addColumn('model_name', function ($partwithdraw) {
                    $model_name=$partwithdraw->job->ticket->purchase->modelname->model_name ?? Null;
                    return $model_name;
                })
                ->addColumn('product_serial', function ($partwithdraw) {
                    $product_serial=$partwithdraw->job->ticket->purchase->product_serial ?? Null;
                    return $product_serial;
                })




                ->addColumn('action', function ($partwithdraw) {
                        if (Auth::user()->can('edit') && Auth::user()->can('delete') && Auth::user()->can('show')) {
                            return '<div class="table-actions text-center" style="display: flex;">
                                        <a href=" '.route('technician.withdraw-request.show', $partwithdraw->id). ' " title="View">
                                            <i class="ik ik-eye f-16 mr-15 text-blue"></i>
                                        </a>
                                    </div>';
                        } elseif (Auth::user()->can('edit') && Auth::user()->can('show')) {
                            return '<div class="table-actions" style="display: flex;">
                                            <a href=" '.route('technician.withdraw-request.show', $partwithdraw->id). ' " title="View">
                                                <i class="ik ik-eye f-16 mr-15 text-blue"></i>
                                            </a>

                                            </div>';

                        } elseif (Auth::user()->can('show')) {
                            return '<div class="table-actions">
                                        <a href=" '.route('technician.withdraw-request.show', $partwithdraw->id). ' " title="View">
                                            <i class="ik ik-eye f-16 mr-15 text-blue"></i>
                                        </a>
                                    </div>';
                        } 
                })
                ->addIndexColumn()
                ->rawColumns(['ticket_sl','job_number','service_type','warranty_type','status','job_pending_remark','action'])
                ->make(true);
        }
        return view('employee.part-withdraw.index');
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
    public function create($id)
    {
        $auth=Auth::user()->roles->first();
        $job = Job::findOrFail($id);
        $allAccessories=Accessories::where('status', 1)->get();
        $allFaults=Fault::where('status', 1)->get();
        $jobCloseRemarks = JobCloseRemark::orderBy('id', 'DESC')->get();
        $jobpendingRemarks = JobPendingRemark::orderBy('id', 'DESC')->get();
        $consumption=InventoryStock::where('is_consumed',1)->where('job_id', $id)->get();
        return view('employee.part-withdraw.create',compact('job','consumption','allAccessories','allFaults','jobCloseRemarks','jobpendingRemarks','auth'));
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
            'withdraw_qnty'=>'required|array',
            'withdraw_qnty.*'=>'required|numeric',
        ]);
        try {
            DB::beginTransaction();
            $job = Job::findOrFail($request->job_id);

            $job->withdraw_request = $job->withdraw_request == 0 ? 1 : 0;
            $job->update();

            $partWithdraw=PartWithdraw::create([
                'job_id'=>$request->job_id,
                'created_by'=>Auth::user()->id,
            ]);

            foreach ($request->withdraw_qnty as $key => $value) {
                if ($request->withdraw_qnty[$key] != null && $request->withdraw_qnty[$key] > 0) {
                    PartWithdrawDetails::create([
                        'part_withdraw_id'=>$partWithdraw->id,
                        'job_id'=>$request->job_id,
                        'inventory_stock_id'=>$request->inventory_stock_id[$key],
                        'part_id'=>$request->part_id[$key],
                        'used_qnty'=>$request->used_qnty[$key],
                        'required_qnty'=>$request->withdraw_qnty[$key],
                        'created_by'=>Auth::user()->id,
                    ]);
                }

            };
            DB::commit();
            return redirect()->route('technician.withdraw-request.index')
            ->with('success', 'Withdraw request sent successfully');
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
        $auth = Auth::user();
        $auth_role = $auth->roles->first();
       $partWithdraw=PartWithdraw::with('withdrawdetails')->where('id',$id)->first();
       $allAccessories=Accessories::where('status', 1)->get();
       $allFaults=Fault::where('status', 1)->get();
       return view('employee.part-withdraw.show',compact('partWithdraw','allAccessories','allFaults','auth_role'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
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
        //
    }
    public function approve(Request $request, $id)
    {
        if ($request->ajax()) {
            $partWithdraw=PartWithdraw::findOrFail($id);
            $job = Job::where('id',$partWithdraw->job_id)->first();
            if ($partWithdraw) {
                $partWithdraw->status = 1;
                $partWithdraw->update();
            }
            $jobSubmission = JobSubmission::where('job_id',$partWithdraw->job_id)->first();
            foreach ($partWithdraw->withdrawdetails as $key => $value) {
                $consumption=InventoryStock::where('is_consumed',1)->where('job_id', $value->job_id)->where('part_id', $value->part_id)->first();
                $consumption->update([
                    'withdraw_qnty'=>$value->required_qnty,
                    'stock_out'=>0
                ]);
                $jobSubmissionDetails = JobSubmissionDetails::where('job_submission_id',$jobSubmission->id)->where('part_id', $value->part_id)->delete();
            };
            $job->withdraw_request = $job->withdraw_request == 1 ? 2 : 1;
            $job->update();
            $jobSubmission->delete();
            if ($job->withdraw_request==2) {
                return response()->json([
                    'data' => $job,
                    'success' => true,
                    'message' => 'Request Approved Successfully',
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
}
