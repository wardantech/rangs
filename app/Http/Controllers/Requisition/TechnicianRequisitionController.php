<?php

namespace App\Http\Controllers\Requisition;

use DB;
use Auth;
use Carbon\Carbon;
use App\Models\JobModel\Job;
use Illuminate\Http\Request;
use App\Models\Outlet\Outlet;
use App\Models\Inventory\Rack;
use App\Models\Inventory\Parts;
use App\Models\Inventory\Store;
use App\Models\Employee\Employee;
use App\Http\Controllers\Controller;
use App\Models\Inventory\PartsModel;
use App\Models\Requisition\Allocation;
use App\Models\Requisition\Requisition;
use App\Models\Inventory\InventoryStock;
use App\Models\Inventory\PriceManagement;
use App\Models\Inventory\RackBinManagement;
use App\Models\Requisition\AllocationDetails;
use App\Models\Requisition\RequisitionDetails;
// use App\Models\Inventory\PriceManagement;

class TechnicianRequisitionController extends Controller
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
            $employee=Employee::where('user_id',Auth::user()->id)->first();
            $user_role = $auth->roles->first();
            $mystore='';
            if ($user_role->name == 'Technician') {
                $mystore=Store::where('user_id',Auth::user()->id)->first();
                if ($mystore != null) {
                    $requisitions=Requisition::where('from_store_id',$mystore->id)->where('belong_to',3)->latest()->get();
                }else{
                    return redirect()->back()->with('error', __("Sorry you don't have the permission."));
                }
            } elseif ($user_role->name == 'Admin' || $user_role->name == 'Super Admin' || $user_role->name =='Team Leader Admin' || $user_role->name =='Store Admin' ) {
                $requisitions=Requisition::where('belong_to',3)->latest()->get();
            } else {
                // $mystore = Store::where('outlet_id',$employee->outlet_id)->first();
                $mystore=Store::where('id',$employee->store_id )->first();
                if ($mystore != null) {
                    $requisitions=Requisition::where('from_store_id',$mystore->id)->where('belong_to',3)->latest()->get();
                }else{
                    return redirect()->back()->with('error', __("Sorry you don't have the permission."));
                }
            }
            return view('employee.requisition.list',compact('requisitions','mystore'));
        } catch (\Exception $e) {
            $bug = $e->getMessage();
            return redirect()->back()->with('error', $bug);
        }
    }

    public function indexForStores()
    {
        try{
            $auth = Auth::user();
            $user_role = $auth->roles->first();
            $mystore='';
            if ($user_role->name == 'Super Admin' || $user_role->name == 'Admin' || $user_role->name =='Team Leader Admin' || $user_role->name =='Store Admin') {
                $requisitions=Requisition::where('belong_to',3)->latest()->get();
            } else {
                $employee=Employee::where('user_id',Auth::user()->id)->first();
                // $mystore=Store::where('outlet_id',$employee->outlet_id)->first();
                $mystore=Store::where('id',$employee->store_id )->first();
                if ($mystore != null) {
                    $requisitions=Requisition::where('store_id',$mystore->id)->where('belong_to',3)->latest()->get();
                }else{
                    return redirect()->back()->with('error', __("Sorry you don't have the permission."));
                }

            }
            return view('employee.requisition.listforstore',compact('requisitions','mystore'));
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
            $outlates = Outlet::where('status', 1)->latest()->get();
            $stores = Store::where('status', 1)->latest()->get();
            $parts=Parts::where('status', 1)->get();
                    //
            $requistion=Requisition::where('belong_to',3)->latest('id')->first();
            if(!empty($requistion)){
                $trim=trim($requistion->requisition_no,"T-RSL-");
                $sl=$trim + 1;
                $sl_number="T-RSL-".$sl;
            }else{
                $sl_number="T-RSL-"."1";
            }
            return view('employee.requisition.create',compact('outlates','parts','stores','sl_number'));
        } catch (\Exception $e) {
            $bug = $e->getMessage();
            return redirect()->back()->with('error', $bug);
        }
    }
    // Requisition from a job
    public function requisitionCreateByJob(Request $request, $id)
    {
        try{
            $auth = Auth::user();
            $user_role = $auth->roles->first();
            if ($user_role->name == 'Super Admin' || $user_role->name == 'Admin') {
                $mystore='';
                $employeebelongToStore='';
            } else {
                $mystore=Store::where('user_id',Auth::user()->id)->first();
                if ($mystore == null) {
                    return redirect()->back()->with('error', __("Sorry you don't have the permission."));
                }else{
                    $employee=Employee::where('user_id',Auth::user()->id)->first();
                    $employeebelongToStore=Store::where('id',$employee->store_id)->first();
                }

            }

            $job = Job::findOrFail($id);

            $outlates = Outlet::where('status', 1)->orderBy('name')->get();
            $stores = Store::where('status', 1)->orderBy('name')->get();

            //
            $requistion=Requisition::where('belong_to',3)->latest('id')->first();
            if(!empty($requistion)){
                $trim=trim($requistion->requisition_no,"T-RSL-");
                $sl=$trim + 1;
                $sl_number="T-RSL-".$sl;
            }else{
                $sl_number="T-RSL-"."1";
            }
            return view('employee.requisition.create',compact('outlates','stores','job','sl_number','mystore','user_role','employeebelongToStore'));
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
            // 'requisition_no' => 'required|unique:requisitions,requisition_no,NULL,id,deleted_at,NULL',
            'job_id' => 'required',
            'date' => 'required',
            'from_store_id' => 'required|numeric',
            'store_id' => 'required|numeric',
            'stock_in_hand' => 'array',
            'required_quantity' => 'required|nullable|array',
            'part_id' => 'required|array',
        ]);

        $total_quantity = array_sum($request->required_quantity);
        $employee=Employee::where('user_id',Auth::id())->first();
        DB::beginTransaction();
        try {
            $sl_number = $this->generateUniqueSl();

            $job = Job::findOrFail($request->job_id);
            $job->update([
                'is_requisition'=> 1,
            ]);

            $requisition = Requisition::create([
                'job_id' => $request->job_id,
                'store_id' => $request->store_id,
                'from_store_id' => $request->from_store_id,
                'employee_id' => $employee ? $employee->id : null ,
                'belong_to' => 3,
                'date' => $request->date,
                'requisition_no' => $sl_number,
                'total_quantity' => $total_quantity,
                'created_by' => Auth::id(),
            ]);
            if($requisition){
                foreach($request->part_id as $key => $id){
                    if($id != null &&  $id > 0){

                        $details['requisition_id'] = $requisition->id;
                        $details['parts_id'] = $id;
                        $details['stock_in_hand'] = $request->stock_in_hand[$key];
                        $details['required_quantity'] = $request->required_quantity[$key];

                        RequisitionDetails::create($details);
                    }
                }
            }
            DB::commit();
            return redirect()->route('technician.requisition.index')
            ->with('success', $requisition->requisition_no.'-'.'Requisition Created successfully.');
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
            $requisition = Requisition::with([
                'requisitionDetails',
                'senderStore',
                'partsModel',
                'employee',
                'store',
                'parts',
                'user',
                'job'
            ])->where('id', $id)->first();
            return view('employee.requisition.show', compact('requisition'));
        } catch (\Exception $e) {
            $bug = $e->getMessage();
            return redirect()->back()->with('error', $bug);
        }
    }
    //Details For Branch
    public function showforbranch($id)
    {
        try{
            $requisition = Requisition::with([
                'requisitionDetails',
                'senderStore',
                'partsModel',
                'employee',
                'store',
                'parts',
                'user',
                'job'
            ])->where('id', $id)->first();
            return view('employee.requisition.show', compact('requisition'));
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
            $outlates = Outlet::where('status', 1)->orderBy('name')->get();
            $stores = Store::where('status', 1)->orderBy('name')->get();
            $parts=Parts::where('status', 1)->orderBy('name')->get();

            $requistion = Requisition::where('belong_to',3)->find($id);

            $requisitionDetails = RequisitionDetails::where('requisition_id', $id)->get();

            $partsId = [];
            foreach($requisitionDetails as $requisitionDetail)
            {
                $partsId[] = $requisitionDetail->parts_id;
            }

            return view('employee.requisition.technician_edit', compact(
                'requistion',
                'outlates',
                'stores',
                'partsId',
                'parts',
            ));
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
            'requisition_no' => 'required|unique:requisitions,requisition_no,' . $id,
            'date' => 'required',
            'from_store_id' => 'nullable|numeric',
            'store_id' => 'required|numeric',
            'parts_id' => 'nullable|numeric',
            // 'parts_model_id' => 'nullable',
            'stock_in_hand' => 'nullable|array',
            'model_id' => 'nullable|array',
            'required_quantity' => 'nullable|array',
            'part_id' => 'nullable|array',
        ]);

        $requisition = Requisition::find($id);

        $total_quantity = array_sum($request->required_quantity);

        try {
            $requisition->update([
                'store_id' => $request->store_id,
                'from_store_id' => $request->from_store_id,
                'belong_to' => 3, // 2=Technician
                'date' => $request->date,
                'requisition_no' => $request->requisition_no,
                'total_quantity' => $total_quantity,
                'created_by' => Auth::id(),
            ]);

            if($requisition){
                foreach($request->part_id as $key => $id){
                    if($id != null &&  $id > 0){

                        $partId = $request->part_id;
                        $old_parts_id = [];

                        $previous_parts_id = RequisitionDetails::where('requisition_id', $requisition->id)->get();
                        foreach($previous_parts_id as $key=>$parts_id){
                            $id = $parts_id->parts_id;
                            array_push($old_parts_id, $id);
                        }

                        // dd($previous_parts_id);
                        foreach($partId as $key => $id){
                            $data['parts_id'] = $id;
                            $data['requisition_id'] = $requisition->id;
                            $data['stock_in_hand'] = $request->stock_in_hand[$key];
                            $data['required_quantity'] = $request->required_quantity[$key];

                            if($old_parts_id != null ){
                                if(in_array($id,$old_parts_id)){
                                    $details = RequisitionDetails::where('requisition_id', $requisition->id)
                                                                ->where('parts_id',$id)->first();
                                    $details->update($data);
                                }else{
                                    //dd('create');
                                    RequisitionDetails::create($data);
                                }
                            }else{
                                RequisitionDetails::create($data);
                            }

                        }

                        $previous = RequisitionDetails::where('requisition_id', $requisition->id)->get();
                        foreach($previous as $key=>$parts){
                            if(!in_array($parts->parts_id,$partId)){
                                RequisitionDetails::where('requisition_id',$requisition->id)
                                                        ->where('parts_id',$parts->parts_id)->delete();
                            }
                        }

                    }
                }
            }
            return back()->with('success', __('Requisition updated successfully.'));
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
            $requisition = Requisition::find($id);
            $requisition_details = RequisitionDetails::where('requisition_id',$requisition->id);
            if($requisition_details){
                $requisition_details->delete();
            }
            $requisition->delete();
            return redirect()->route('outlet.requisitionList')->with('success', __('Requisition deleted successfully.'));
        } catch (\Exception $e) {
            $bug = $e->getMessage();
            return redirect()->back()->with('error', $bug);
        }
    }

    public function getPartsStock(Request $request){
        $part_id = $request->parts_id;
        $store_id=$request->from_store_id;
        $part_id_array = [];
        $model_id_array = [];

        $stock_collect = [];
        $partInfo_collect = [];
        foreach($part_id as $key=>$pr_id){
            $stock_in = InventoryStock::where('part_id',$pr_id)->where('store_id',$store_id)->sum('stock_in');
            $stock_out = InventoryStock::where('part_id',$pr_id)->where('store_id',$store_id)->sum('stock_out');

            $partsInfo=Parts::where('id', $pr_id)->first();
            $stock_in_hand = $stock_in - $stock_out;
            array_push($stock_collect,$stock_in_hand);
            array_push($partInfo_collect,$partsInfo);
        }
        $html = view('requisition.requisition.technician_parts_info', compact('partInfo_collect','stock_collect'))->render();
        return response()->json(compact('html'));
    }

    public function getPartsStockForPartsReturn(Request $request){

        $part_id = $request->parts_id;
        $store_id=$request->from_store_id;
        $part_id_array = [];
        $model_id_array = [];

        $stock_collect = [];
        $partInfo_collect = [];
        foreach($part_id as $key=>$pr_id){

            $stock_in = InventoryStock::where('part_id',$pr_id)->where('store_id',$store_id)->sum('stock_in');
            $stock_out = InventoryStock::where('part_id',$pr_id)->where('store_id',$store_id)->sum('stock_out');

            $partsInfo=Parts::where('id', $pr_id)->first();
            $stock_in_hand = $stock_in - $stock_out;
            array_push($stock_collect,$stock_in_hand);
            array_push($partInfo_collect,$partsInfo);
        }
        $html = view('inventory.partsreturn.parts_info', compact('partInfo_collect','stock_collect'))->render();
        return response()->json(compact('html'));
    }

    public function getPartsStockForJob(Request $request){
        $part_model_id = $request->model_id;
        $part_id_array = [];
        $model_id_array = [];
        foreach($part_model_id as $key=>$id){
            $create_id = explode("-",$id);
            $part_id = $create_id[0];
            $model_id = $create_id[1];
            array_push($part_id_array,$part_id);
            array_push($model_id_array,$model_id);
        }

        $stock_collect = [];
        $partInfo_collect = [];
        $priceInfo = [];
        foreach($part_id_array as $key=>$pr_id){
            $model_id = $model_id_array[$key];

            $stock_in = InventoryStock::where('part_id',$pr_id)->where('belong_to',3)->where('parts_model_id',$model_id)->sum('stock_in');
            $stock_out = InventoryStock::where('part_id',$pr_id)->where('belong_to',3)->where('parts_model_id',$model_id)->sum('stock_out');

            $partsInfo=PartsModel::where('id', $model_id)->with('part')->first();
            $price=PriceManagement::where('id', $model_id)->latest('id')->first();;
            $stock_in_hand = $stock_in - $stock_out;
            array_push($stock_collect,$stock_in_hand);
            array_push($partInfo_collect,$partsInfo);
            array_push($priceInfo,$price);
        }

        $html = view('employee.completed_job_submit.parts_info', compact('partInfo_collect','stock_collect','priceInfo'))->render();
        return response()->json(compact('html'));
    }

    //Part Consumption by job
    public function consumptionCreateByJob(Request $request, $id)
    {
        try{
                $job=Job::findOrFail($id);
                $outlates = Outlet::where('status', 1)->latest()->get();
                $stores = Store::where('status', 1)->latest()->get();
                $parts=Parts::where('status', 1)->get();

                //
                $requistion=Requisition::where('belong_to',3)->latest()->first();
                if(!empty($requistion)){
                    $trim=trim($requistion->requisition_no,"RSL-");
                    $sl=$trim + 1;
                    $sl_number="RSL-".$sl;
                }else{
                    $sl_number="RSL-"."1";
                }
                return view('employee.consumption.create',compact('outlates','parts','stores','job','sl_number'));
        } catch (\Exception $e) {
            $bug = $e->getMessage();
            return redirect()->back()->with('error', $bug);
        }
    }

    public function consumptionStoreByJob(Request $request)
    {
        $this->validate($request, [
            'job_id' => 'required',
            'date' => 'required',
            // 'part_id[]' => 'required',
            // 'required_quantity[]' => 'required|numeric',
        ]);

        try {
            foreach ($request->part_id as $key => $value) {
                InventoryStock::create([
                    'date' => $request->date,
                    'job_id' => $request->job_id,
                    'part_id' => $request->part_id[$key],
                    'stock_out' => $request->required_quantity[$key],
                    'belong_to' => 3,
                    'type' => 2,
                    'is_consumed' => 1,
                    'created_by' => Auth::id(),
                ]);
            }
            return redirect('job/job')
            ->with('success', 'Part consumed for this job successfully');
        } catch (\Exception $e) {
            $bug = $e->getMessage();
            return redirect()->back()->with('error', $bug);
        }

    }
    // Unique Serial Number For Tech Requisition
    protected function generateUniqueSl()
    {
        do {
            $requistion = Requisition::where('belong_to',3)->latest('id')->first();
       
            if(!$requistion) {
                return "T-RSL-1";
            }

            $string = preg_replace("/[^0-9\.]/", '', $requistion->requisition_no);
            
            $slNumber = 'T-RSL-' . sprintf('%01d', $string+1);

        } while (Requisition::where('belong_to',3)->where('requisition_no', '==', $slNumber)->first());

        return $slNumber;
    }
}
