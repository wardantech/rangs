<?php

namespace App\Http\Controllers\Consumption;

use DB;
use Auth;
use App\Models\JobModel\Job;
use Illuminate\Http\Request;
use App\Models\Outlet\Outlet;
use App\Models\Inventory\Parts;
use App\Models\Inventory\Store;
use App\Models\Employee\Employee;
use App\Http\Controllers\Controller;
use App\Models\Requisition\Requisition;
use App\Models\Inventory\InventoryStock;
use App\Models\Inventory\PriceManagement;
use App\Models\Requisition\RequisitionDetails;

class ConsumptionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    //Part Consumption by job
    public function consumptionCreateByJob(Request $request, $id)
    {
        try {
            $job=Job::findOrFail($id);
            
            $consumptionsdetails=[];
            
            $mystore=Store::where('user_id',$job->user_id)->first();

            if ($mystore != null) {
                $inventoryStocksDetails=InventoryStock::where('store_id',$mystore->id)->where('is_consumed',1)->where('job_id',$id)->get();
                
                foreach ($inventoryStocksDetails as $key => $value) {
                    $item = [];
                    $price = PriceManagement::where(
                            'part_id', $value->part_id
                        )->latest('id')->first();
                    $item['id'] = $value->id;
                    $item['part_id'] = $value->part->id;
                    $item['type'] = $value->part->type;
                    $item['part_name'] = $value->part->name.'-'.$value->part->code;
                    $item['stock_out'] = $value->stock_out;
                    $item['price'] = floatval($price->selling_price_bdt);
                    if ($value->stock_out > 0) {
                        array_push($consumptionsdetails, $item);
                    }  
                }
            }

            $parts=Parts::where('status', 1)->orderBy('name')->get();

            $my_requisition=Requisition::where('job_id',$job->id)->latest()->first();
            if (empty($my_requisition)) {
                return redirect()->back()->with('error', __("Sorry you don't have raised any requisition for the job."));
            }
            // Requisitions Data
            $details = RequisitionDetails::where('requisition_id',$my_requisition->id)->with('part')->get();
            $stock_collect = [];
            foreach($details as $key=>$detail){
                $stock_in = InventoryStock::where('part_id',$detail->parts_id)->where('store_id',$my_requisition->from_store_id)->sum('stock_in');
                $stock_out = InventoryStock::where('part_id',$detail->parts_id)->where('store_id',$my_requisition->from_store_id)->sum('stock_out');
                $stock_in_hand = $stock_in - $stock_out;
                array_push($stock_collect,$stock_in_hand);
            }
            return view('employee.consumption.create',compact('parts','job','mystore','details','stock_collect','consumptionsdetails'));
        } catch (\Exception $e) {
            $bug = $e->getMessage();
            return redirect()->back()->with('error', $bug);
        }
    }

    public function getPartsStock(Request $request)
    {
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
            $html = view('employee.consumption.technician_parts_info', compact('partInfo_collect','stock_collect'))->render();
            return response()->json(compact('html'));
    }

    public function consumptionStoreByJob(Request $request)
    {
        $this->validate($request, [
            'job_id' => 'required',
            'date' => 'required',
            'from_store_id' => 'required',
        ]);
        DB::beginTransaction();
        try {
            $job = Job::findOrFail($request->job_id);
            $job->update([
                'is_consumed'=> 1,
            ]);

            foreach ($request->required_quantity as $key => $value) {

                $InventoryStock=InventoryStock::where('store_id',$request->from_store_id)->where('is_consumed',1)->where('job_id', $request->job_id)->where('part_id',$request->part_id[$key])->first();
           
                if ($InventoryStock) {
                    $total=$InventoryStock->stock_out + $request->required_quantity[$key];
                    $InventoryStock->update([
                        'stock_out' => $total,
                        'updated_by' => Auth::id(),
                    ]);
                }else{
                    InventoryStock::create([
                        'store_id' => $request->from_store_id,
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
            }
            DB::commit();
            return redirect()
            ->route('technician.jobs.show',$request->job_id)
            ->with('success', 'Part consumed for this job successfully');
        } catch (\Exception $e) {
            DB::rollback();
            $bug = $e->getMessage();
            return redirect()->back()->with('error', $bug);
        }

    }
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $consumption=InventoryStock::findOrFail($id);
        
        $stock_in = InventoryStock::where('part_id',$consumption->part_id)->where('store_id',$consumption->store_id)->sum('stock_in');
        $stock_out = InventoryStock::where('part_id',$consumption->part_id)->where('store_id',$consumption->store_id)->sum('stock_out');
        $stock_in_hand = $stock_in - $stock_out;

        return view('employee.consumption.edit',compact('consumption','stock_in_hand'));
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
        try {
            $consumption=InventoryStock::findOrFail($id);
            if ($consumption) {            
                $consumption->update([
                    'stock_out' => $request->required_quantity,
                    'updated_by' => Auth::id(),
                ]);
            }else{
                return redirect()->back()->with('error', __("Sorry you don't have any part consumption for the job.")); 
            }          
            return redirect()
            ->route('technician.jobs.show',$consumption->job_id)
            ->with('success', 'Consuption updated for the job successfully');
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
        //
    }
}
