<?php

namespace App\Http\Controllers\Job;

use DB;
use Auth;
use DataTables;
use Carbon\Carbon;
use App\Models\JobModel\Job;
use Illuminate\Http\Request;
use App\Models\Inventory\Parts;
use App\Models\Employee\Employee;
use App\Models\Job\JobAttachment;
use App\Models\Job\JobSubmission;
use App\Models\Ticket\ServiceType;
use App\Http\Controllers\Controller;
use App\Models\Inventory\PartsModel;
use App\Models\Requisition\Requisition;
use App\Models\Inventory\InventoryStock;
use App\Models\Job\JobSubmissionDetails;
use App\Models\Inventory\PriceManagement;
use App\Models\Job\CustomerAdvancedPayment;
// use Intervention\Image\Facades\Image;
use Illuminate\Support\Str;
use Image;
use App\Services\ImageUploadService;

class JobSubmissionController extends Controller
{
    protected $imageUploadService;

    public function __construct(ImageUploadService $imageUploadService)
    {
        $this->imageUploadService = $imageUploadService;
    }

    public function index()
    {
        try{
            $auth = Auth::user();
            $employee=Employee::where('user_id',Auth::user()->id)->first();
            $user_role = $auth->roles->first();
            if ($user_role->name == 'Super Admin' || $user_role->name == 'Admin') {
                $submittedJobs=DB::table('job_submissions')
                ->join('jobs', 'job_submissions.job_id', '=', 'jobs.id')
                ->join('tickets', 'jobs.ticket_id', '=', 'tickets.id')
                ->select('job_submissions.id as id','job_submissions.submission_date as submission_date','job_submissions.total_amount as total_amount',
                'jobs.job_number as job_number','jobs.id as job_id','jobs.is_ticket_reopened_job as is_ticket_reopened_job','jobs.created_at as job_assigned_date','tickets.id as ticket_id','tickets.created_at as ticket_date','tickets.delivery_date_by_team_leader as ticket_delivery_date_by_team_leader')
                ->where('job_submissions.deleted_at',null)
                ->orderBy('job_submissions.id', 'desc');
                // ->get();
            }elseif($user_role->name == 'Team Leader'){
                $submittedJobs=DB::table('job_submissions')
                ->join('jobs', 'job_submissions.job_id', '=', 'jobs.id')
                ->join('tickets', 'jobs.ticket_id', '=', 'tickets.id')
                ->select('job_submissions.id as id','job_submissions.submission_date as submission_date','job_submissions.total_amount as total_amount',
                'jobs.job_number as job_number','jobs.id as job_id','jobs.is_ticket_reopened_job as is_ticket_reopened_job','jobs.created_at as job_assigned_date','tickets.id as ticket_id','tickets.created_at as ticket_date','tickets.delivery_date_by_team_leader as ticket_delivery_date_by_team_leader')
                ->where('job_submissions.team_leader_user_id',Auth::user()->id)
                ->where('job_submissions.deleted_at',null)
                ->orderBy('job_submissions.id', 'desc');
                // ->get();
            }elseif($user_role->name == 'Technician'){
                $submittedJobs=DB::table('job_submissions')
                ->join('jobs', 'job_submissions.job_id', '=', 'jobs.id')
                ->join('tickets', 'jobs.ticket_id', '=', 'tickets.id')
                ->select('job_submissions.id as id','job_submissions.submission_date as submission_date','job_submissions.total_amount as total_amount',
                'jobs.job_number as job_number','jobs.id as job_id','jobs.is_ticket_reopened_job as is_ticket_reopened_job','jobs.created_at as job_assigned_date','tickets.id as ticket_id','tickets.created_at as ticket_date','tickets.delivery_date_by_team_leader as ticket_delivery_date_by_team_leader')
                ->where('job_submissions.user_id',Auth::user()->id)
                ->where('job_submissions.deleted_at',null)
                ->orderBy('job_submissions.id', 'desc');
                // ->get();
            }else { 
                if($employee==null){
                    return redirect()->back()->with('error', __("Sorry! You don't have the access."));
                }else{
                $submittedJobs=DB::table('job_submissions')
                    ->join('jobs', 'job_submissions.job_id', '=', 'jobs.id')
                    ->join('tickets', 'jobs.ticket_id', '=', 'tickets.id')
                    ->select('job_submissions.id as id','job_submissions.submission_date as submission_date','job_submissions.total_amount as total_amount',
                        'jobs.job_number as job_number','jobs.id as job_id','jobs.is_ticket_reopened_job as is_ticket_reopened_job','jobs.created_at as job_assigned_date','tickets.id as ticket_id','tickets.created_at as ticket_date','tickets.delivery_date_by_team_leader as ticket_delivery_date_by_team_leader','tickets.outlet_id as outlet_id')
                    ->where('job_submissions.deleted_at',null)
                    ->where('tickets.outlet_id', $employee->outlet_id)
                    ->orderBy('job_submissions.id', 'desc');
                    // ->get();
                }
            }
            if (request()->ajax()) {
                return DataTables::of($submittedJobs)

                    ->addColumn('date', function ($submittedJobs) {
                        $date=Carbon::parse($submittedJobs->submission_date)->format('m/d/Y');
                        return $date;
                    })

                    ->addColumn('ticket_number', function ($submittedJobs) {
                        $ticket_number='TSL'.'-'.$submittedJobs->ticket_id;
                        return $ticket_number;
                    })
                    ->addColumn('ticket_date', function ($submittedJobs) {
                        $ticket_date=null;
                        if($submittedJobs->ticket_date){
                            $ticket_date=Carbon::parse($submittedJobs->ticket_date)->format('m/d/Y'); 
                        }
                        return $ticket_date;
                    })

                    ->addColumn('job_number', function ($submittedJobs) {
                        $job_number='JSL-'.$submittedJobs->job_id;
                        return $job_number;
                    })
                    ->addColumn('job_assigned_date', function ($submittedJobs) {
                        $job_assigned_date=null;
                        if($submittedJobs->job_assigned_date){
                            $job_assigned_date=Carbon::parse($submittedJobs->job_assigned_date)->format('m/d/Y'); 
                        }
                        return $job_assigned_date;
                    })
                    ->addColumn('ticket_delivery_date_by_team_leader', function ($submittedJobs) {
                        $ticket_delivery_date_by_team_leader=null;
                        if($submittedJobs->ticket_delivery_date_by_team_leader){
                            $ticket_delivery_date_by_team_leader=Carbon::parse($submittedJobs->ticket_delivery_date_by_team_leader)->format('m/d/Y'); 
                        }
                        return $ticket_delivery_date_by_team_leader;
                    })
                    ->addColumn('amount', function ($submittedJobs) {
                        $amount=$submittedJobs->total_amount ?? 0;
                        return $amount;
                    })

                    ->addColumn('status', function ($submittedJobs) {
                    if($submittedJobs->is_ticket_reopened_job == 1)
                        return '<span class="badge badge-danger">Re Opened Ticket</span>';
                     else{
                        return '<span class="bbadge badge-success">Normal</span>';
                     }
                                             
                    })
                    ->addColumn('action', function ($submittedJobs) {
                            if (Auth::user()->can('edit') && Auth::user()->can('delete') && Auth::user()->can('show')) {
                                    return '<div class="table-actions text-center" style="display: flex;">           
                                                <a href=" '.route('technician.submitted-jobs.edit', $submittedJobs->id). ' " title="View">
                                                    <i class="ik ik-edit f-16 mr-15 text-info"></i>
                                                </a>
                                                <a href=" '.route('technician.submitted-job-show', $submittedJobs->id). ' " title="View">
                                                    <i class="ik ik-eye f-16 mr-15 text-green"></i>
                                                </a>
                                                <a type="submit" onclick="showDeleteConfirm(' . $submittedJobs->id . ')" title="Delete"><i class="ik ik-trash-2 f-16 text-red"></i></a>
                                            </div>';

                            } elseif (Auth::user()->can('edit') && Auth::user()->can('show')) {
                                return '<div class="table-actions text-center" style="display: flex;">           
                                            <a href=" '.route('technician.submitted-jobs.edit', $submittedJobs->id). ' " title="View">
                                                <i class="ik ik-edit f-16 mr-15 text-info"></i>
                                            </a>
                                            <a href=" '.route('technician.submitted-job-show', $submittedJobs->id). ' " title="View">
                                                <i class="ik ik-eye f-16 mr-15 text-green"></i>
                                            </a>
                                            <a type="submit" onclick="showDeleteConfirm(' . $submittedJobs->id . ')" title="Delete"><i class="ik ik-trash-2 f-16 text-red"></i></a>
                                        </div>';
                            } elseif (Auth::user()->can('show')) {
                                return '<div class="table-actions text-center" style="display: flex;">           
                                            <a href=" '.route('technician.submitted-jobs.edit', $submittedJobs->id). ' " title="View">
                                                <i class="ik ik-edit f-16 mr-15 text-info"></i>
                                            </a>
                                            <a href=" '.route('technician.submitted-job-show', $submittedJobs->id). ' " title="View">
                                                <i class="ik ik-eye f-16 mr-15 text-green"></i>
                                            </a>
                                            <a type="submit" onclick="showDeleteConfirm(' . $submittedJobs->id . ')" title="Delete"><i class="ik ik-trash-2 f-16 text-red"></i></a>
                                        </div>';
                            } 
                    })
                    ->addIndexColumn()
                    ->rawColumns(['status','action'])
                    ->make(true);
            }
            return view('employee.completed_job_submit.index');
        } catch (\Exception $e) {
            $bug = $e->getMessage();
            return redirect()->back()->with('error', $bug);
        }

    }

    public function createJobSubmission($id)
    {
        try{
            $job = Job::findOrFail($id);
            $advance_payment=CustomerAdvancedPayment::where('job_id',$id)->first();

            $serviceTypes = ServiceType::where('status', 1)->orderBy('service_type')->get();
            $currentDate= Carbon::now('Asia/Dhaka');
            $service_amount=0;
            // if ($job->ticket->purchase->service_warranty_date < $job->ticket->date) {
            //     $service_amount = $job->ticket->service->service_amount;
            // }
            $inventoryStocks = [];
            $my_requisition=Requisition::where('job_id',$job->id)->latest()->first();
            if ($my_requisition != null) {
                $inventoryStocksDetails=InventoryStock::where('store_id',$my_requisition->from_store_id)->where('is_consumed',1)->where('job_id',$id)->get();
                
                foreach ($inventoryStocksDetails as $key => $value) {
                    $item = [];
                    $price = PriceManagement::where(
                            'part_id', $value->part_id
                        )->latest('id')->first();
                    $item['id'] = $value->id;
                    $item['part_id'] = $value->part->id;
                    $item['code'] = $value->part->code;
                    $item['type'] = $value->part->type;
                    $item['part_name'] = $value->part->name;
                    $item['stock_out'] = $value->stock_out;
                    $item['price'] = floatval($price->selling_price_bdt);
                    array_push($inventoryStocks, $item);
                }
            }
            
            $parts=Parts::where('status', 1)->get();
            return view('employee.completed_job_submit.create', compact('job','parts','inventoryStocks','service_amount', 'currentDate', 'serviceTypes','advance_payment'));
        } catch (\Exception $e) {
            $bug = $e->getMessage();
            return redirect()->back()->with('error', $bug);
        }
    }

    public function storeJobSubmission(Request $request)
    {
        $this->validate($request, [
            'job_id' => 'required',
            'submission_date' => 'required',
            'service_amount' => 'required',
        ]);

        try {
            DB::beginTransaction();

            $employee=Employee::where('user_id',Auth::user()->id)->first();
            $job=Job::where('id',$request->job_id)->first();
            $job->update([
                'status' => 4,
                'is_submitted'=>1
            ]);
            $jobSubmission = JobSubmission::create([
                'outlet_id' =>  $employee ? $employee->outlet_id : null,
                'job_id' => $request->job_id,
                'user_id' =>  Auth::user()->id,
                'team_leader_user_id' =>  $job->created_by,
                'submission_date' => $request->submission_date,
                'service_amount' => $request->service_amount, // 2=Branch
                'remark' => $request->remark,
                'subtotal_for_spare' => $request->subtotal_for_spare,
                'subtotal_for_servicing' => $request->subtotal_for_servicing,
                'fault_finding_charges' => $request->fault_finding_charges,
                'repair_charges' => $request->repair_charges,
                'vat' => $request->vat,
                'other_charges' => $request->other_charges,
                'discount' => $request->discount,
                'advance_amount' => $request->advance_amount,
                'total_amount' => $request->total,
                'created_by' => Auth::id(),
            ]);

            if($jobSubmission && $request->used_quantity > 0 ){
                foreach($request->part_id as $key => $id){
                    // if($id != null &&  $id > 0){
                        $details['job_submission_id'] = $jobSubmission->id;
                        $details['part_id'] = $id;
                        $details['used_quantity'] = $request->used_quantity[$key];
                        $details['selling_price_bdt'] = $request->selling_price_bdt[$key] ?? 0;
                        $details['selling_value'] = $request->subtotal_selling_price_bdt[$key] ?? 0;
                        JobSubmissionDetails::create($details);
                    // }
                }
            }
            DB::commit();
            return redirect()->route('technician.jobs.show',$job->id)->with('success', __('Job Submitted successfully.'));
        } catch (\Exception $e) {
            DB::rollback();
            $bug = $e->getMessage();
            return redirect()->back()->with('error', $bug);
        }

    }

    public function show($id)
    {
        try{
            $jobSubmission = JobSubmission::findOrFail($id);
            $JobAttachment = JobAttachment::where('job_id',$jobSubmission->job_id)->get();
            $jobSubmissionDetails=JobSubmissionDetails::where('job_submission_id',$jobSubmission->id)->get();

            return view('employee.completed_job_submit.show', compact('jobSubmission','jobSubmissionDetails','JobAttachment'));
        } catch (\Exception $e) {
            $bug = $e->getMessage();
            return redirect()->back()->with('error', $bug);
        }
    }

    public function edit($id)
    {
        try{
            $jobSubmission = JobSubmission::findOrFail($id);
            $job = Job::with('ticket')
                ->where('id', $jobSubmission->job_id)
                ->first();

            $serviceTypes = ServiceType::where('status', 1)->orderBy('service_type')->get();
            $currentDate = Carbon::now('Asia/Dhaka');

            $service_amount=0;
            // if ($job->ticket->purchase->service_warranty_date < $job->ticket->date) {
            //     $service_amount = $job->ticket->service->service_amount;
            // }

            $inventoryStocksDetails = InventoryStock::where('belong_to', 3)
                ->where('is_consumed', 1)
                ->where('job_id', $job->id)
                // ->where('stock_out', !null)
                ->get();

            $inventoryStocks =JobSubmissionDetails::where('job_submission_id',$jobSubmission->id)->get();
            $parts = Parts::where('status', 1)->get();
            return view('employee.completed_job_submit.edit', compact(
                'job',
                'parts',
                'inventoryStocks',
                'service_amount',
                'currentDate',
                'jobSubmission',
                'serviceTypes'
            ));
        } catch (\Exception $e) {
            $bug = $e->getMessage();
            return redirect()->back()->with('error', $bug);
        }
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'job_id' => 'required',
            'submission_date' => 'required',
            'service_amount' => 'required',
        ]);

        try {
            DB::beginTransaction();
            $job = Job::where('id',$request->job_id)->first();
            $jobSubmission = JobSubmission::findOrFail($id);

            $jobSubmission->update([
                'job_id' => $request->job_id,
                'user_id' =>  Auth::user()->id,
                'team_leader_user_id' =>  $job->created_by,
                'submission_date' => $request->submission_date,
                'service_amount' => $request->service_amount, // 2=Branch
                'remark' => $request->remark,
                'is_bill' => $request->billradio,
                'subtotal_for_spare' => $request->subtotal_for_spare,
                'subtotal_for_servicing' => $request->subtotal_for_servicing,
                'fault_finding_charges' => $request->fault_finding_charges,
                'repair_charges' => $request->repair_charges,
                'vat' => $request->vat,
                'other_charges' => $request->other_charges,
                'discount' => $request->discount,
                'advance_amount' => $request->advance_amount,
                'total_amount' => $request->total_amount,
                'updated_by' => Auth::id(),
            ]);

            if($jobSubmission && $request->used_quantity > 0) {
                foreach($request->part_id as $key => $id) {
                    $jobSubmissionDetails = JobSubmissionDetails::where('job_submission_id',$jobSubmission->id)->where('part_id',$id)
                    ->update([
                        'job_submission_id' => $jobSubmission->id,
                        'part_id' => $id,
                        'used_quantity' => $request->used_quantity[$key],
                        'selling_price_bdt' => $request->selling_price_bdt[$key],
                        'selling_value' => $request->subtotal_selling_price_bdt[$key],
                    ]);
                }
            }
            DB::commit();
            return redirect()->route('technician.submitted-jobs')->with('success', __('Bill updated successfully.'));
        }catch (\Exception $e) {
            DB::rollback();
            $bug = $e->getMessage();
            return redirect()->back()->with('error', $bug);
        }
    }

    public function submissionImageUpload($id)
    {
        try{        
            $job = Job::findOrFail($id);
            return view('employee.attachment.create', compact('job'));
        } catch (\Exception $e) {
            $bug = $e->getMessage();
            return redirect()->back()->with('error', $bug);
        }
    }

    public function submissionImageStore(Request $request)
    {

        // try{
        //  if($request->hasfile('filename'))
        //     {
                
        //         foreach($request->file('filename') as $key=>$image)
        //         {
        //             $newimage = Image::make($image);
        //             $name=date('m-d-Y_H-i-s').'-'.$image->getClientOriginalName();
        //             $destinationPath = public_path('attachments/');
        //             $newimage->save($destinationPath.$name,60);
        //             $data[] = $name;    
        //         }
        //     }
        //     $JobAttachment= new JobAttachment();
        //     $JobAttachment->name=json_encode($data);
        //     $JobAttachment->job_id=$request->job_id;
        //     $JobAttachment->save();
        //     return redirect()->route('technician.jobs.show', $request->job_id)->with('success', __('Attachment uploaded successfully.'));
        // } catch (\Exception $e) {
        //     $bug = $e->getMessage();
        //     return redirect()->back()->with('error', $bug);
        // }
        $this->validate($request, [
            'filename' => 'required',
            'filename.*' => 'mimes:jpeg,jpg,png|required|max:10000' // max 10000kb
        ]);

        try {
            if ($request->hasfile('filename')) {
                $destinationPath = public_path('attachments/');
                $uploadedFiles = $this->imageUploadService->uploadImages($request->file('filename'), $destinationPath);
                $attachments = new JobAttachment();
                $attachments->name = json_encode($uploadedFiles);
                $attachments->job_id = $request->job_id;
                $attachments->save();

                return redirect()->route('technician.jobs.show', $request->job_id)->with('success', __('Attachment uploaded successfully.'));
            }

        } catch (\Exception $e) {
            $bug = $e->getMessage();
            return redirect()->back()->with('error', $bug);
        }
    }

    public function destroy($id)
    {
        try{
            $jobSubmission = JobSubmission::findOrFail($id);
            if($jobSubmission){
                $jobSubmissionDetails = JobSubmissionDetails::where('job_submission_id',$jobSubmission->id)->get();
                if($jobSubmissionDetails){
                    foreach($jobSubmissionDetails as $jobSubmissionDetail) {
                        $jobSubmissionDetail->delete();
                    }
                }else{ 
                    return response()->json([
                        'success' => false,
                        'message' => "Whoops! Somjething Went Wronng.",
                    ]);  
                }
                $jobSubmission->delete();
                return response()->json([
                    'success' => true,
                    'message' => "Submitted Job Deleted Successfully.",
                ]);              
            }
        } catch (\Exception $e) {
            $bug = $e->getMessage();
            return redirect()->back()->with('error', $bug);
        }
    }
    public function imageDownload($filename)
    {
        try{
            $path = public_path().'/attachments/'.$filename;
            return response()->download($path);
        } catch (\Exception $e) {
            $bug = $e->getMessage();
            return redirect()->back()->with('error', $bug);
        }
    }
    //Print
    public function print($id)
    {
        try{
            $jobSubmission = JobSubmission::findOrFail($id);
            $JobAttachment = JobAttachment::where('job_id',$jobSubmission->job_id)->get();
            $jobSubmissionDetails=JobSubmissionDetails::where('job_submission_id',$jobSubmission->id)->get();

            return view('employee.completed_job_submit.print', compact('jobSubmission','jobSubmissionDetails','JobAttachment'));
        } catch (\Exception $e) {
            $bug = $e->getMessage();
            return redirect()->back()->with('error', $bug);
        }
    }
}
