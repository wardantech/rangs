<?php

namespace App\Http\Controllers\Inventory;

use Session;
use Redirect;
use Validator;
use DataTables;
use Illuminate\Http\Request;
use App\Models\Outlet\Outlet;
use App\Models\Inventory\Parts;
use App\Models\Inventory\Store;
use Illuminate\Support\Facades\DB;
use App\Models\Inventory\Inventory;
use App\Http\Controllers\Controller;
use App\Models\Inventory\PartsModel;
use Illuminate\Support\Facades\Auth;
use App\Models\Inventory\PartsReturn;
use App\Models\Inventory\InventoryStock;
use App\Models\Inventory\PartsReturnDetails;

class PartsReturnController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try{
            $partsreturns=PartsReturn::latest()->get();
            return view('inventory.partsreturn.index',compact('partsreturns'));
        } catch (\Exception $e) {
            $bug = $e->getMessage();
            return redirect()->back()->with('error', $bug);
        }
    }

    public function warehouseindex()
    {
        try{
            $partsreturns=PartsReturn::latest()->get();
            return view('inventory.partsreturn.warehouseindex.blade.php',compact('partsreturns'));
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
            $models = PartsModel::where('status', 1)->pluck('name','id')->toArray();
            $outlates = Outlet::where('status', 1)->latest()->get();
            $parts=Parts::where('status', 1)->get();

            return view('inventory.partsreturn.create',compact('parts','models','outlates'));
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
    public function outletRequisitionStore(Request $request)
    {
        $rules = [
            'date' => 'required',
            'part_id' => 'required',
            'model_id' => 'required',
            'quantity' => 'required',
            'outlet_id' => 'required'
            ];
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return redirect('inventory/parts-return')
                            ->withInput($request->all())
                            ->withErrors($validator);
        }
        DB::beginTransaction();

        try{
            PartsReturn::create([
                // 'purchase_id' =>  $request->purchase_id,
                'date' =>  $request->date,
                'part_id' =>  $request->part_id,
                'model_id' =>  $request->model_id,
                'quantity' =>  $request->quantity,
                'outlet_id'=>$request->outlet_id,
                'description'=>$request->note,
                'created_by' => Auth::id(),
            ]);
            DB::commit();
            return redirect('inventory/parts-return')
            ->with('success', __('label.RETURN_PARTS_CREATED'));
        } catch (\Exception $e) {
            DB::rollback();
            $bug = $e->getMessage();
            return redirect()->back()->with('error', $bug);
        }
    }

    public function store(Request $request){
        $data = $request->all();
        $belong_to = 1;
        $status = 1;
        $total_quantity = array_sum($request->quantity);
        try {
            $partsreturn = PartsReturn::create([
                'date' =>  $request->date,
                'outlet_id' =>  $request->outlate_id,
                'total_quantity' =>  $total_quantity,
                'belong_to'=>$belong_to,
                'status'=>$status,
                'created_by' => Auth::id(),
            ]);
            if($partsreturn){
                foreach($request->part_id as $key => $id){
                    if($id != null &&  $id > 0){
                        $details['partsreturn_id'] = $partsreturn->id;
                        $details['parts_id'] = $id;
                        $details['model_id'] = $request->model_id[$key];
                        $details['stock_in_hand'] = $request->stock_in_hand[$key];
                        $details['quantity'] = $request->quantity[$key];
                        PartsReturnDetails::create($details);
                    }
                }
            }
            return redirect()->route('inventory.parts-return')->with('success', __('New Transfer created successfully.'));
        } catch (\Exception $e) {
            $bug = $e->getMessage();
            dd($bug);
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
            $partsreturn=PartsReturn::findOrFail($id);
            $partsReturnDetails=PartsReturnDetails::where('partsreturn_id',5)->get();
            return view('inventory.partsreturn.show',compact('partsreturn','partsReturnDetails'));
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
        try{
            $partsreturn=PartsReturn::findOrFail($id);
            if ($partsreturn != null) {
                $partsreturn->delete();
                return redirect()->route('inventory.parts-return.index')->with('success', __('Tranfer Data Deleted Successfully.'));
            }
        } catch (\Exception $e) {
            $bug = $e->getMessage();
            return redirect()->back()->with('error', $bug);
        }
    }

    public function getPartsStock(Request $request){
        //return $request->all();
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
        foreach($part_id_array as $key=>$pr_id){
            $model_id = $model_id_array[$key];

            $stock_in = InventoryStock::where('part_id',$pr_id)->where('belong_to',1)->where('parts_model_id',$model_id)->sum('stock_in');
            $stock_out = InventoryStock::where('part_id',$pr_id)->where('belong_to',1)->where('parts_model_id',$model_id)->sum('stock_out');

            $partsInfo=PartsModel::where('id', $model_id)->with('part')->first();
            $stock_in_hand = $stock_in - $stock_out;
            array_push($stock_collect,$stock_in_hand);
            array_push($partInfo_collect,$partsInfo);
        }

        $html = view('requisition.requisition.return_parts_info', compact('partInfo_collect','stock_collect'))->render();
        return response()->json(compact('html'));
        //return  $partInfo_collect;
    }

    public function returnPartsCentralReceive($id){
        try{
            $partsReturn = PartsReturn::find($id);

        $partsReturn_details = PartsReturnDetails::where('partsreturn_id',$partsReturn->id)->get();
        foreach($partsReturn_details as $key=> $detail){
                //stock out from central wirehouse
                InventoryStock::create([
                    'parts_model_id' => $detail->model_id ,
                    'part_id' => $detail->parts_id,
                    'stock_in' => $detail->quantity,
                    'belong_to' => 1, // 1=Central WareHouse
                    'type' => 2,
                    'created_by' => Auth::id(),
                ]);
                InventoryStock::create([
                    'parts_model_id' => $detail->model_id ,
                    'part_id' => $detail->parts_id,
                    'stock_out' => $detail->quantity,
                    'belong_to' => 2, // 2=Outlet
                    'type' => 2,
                    'created_by' => Auth::id(),
                ]);
        }
        return redirect()->route('outlet.requisitionList')->with('success', __('Allocated parts received successfully.'));
        } catch (\Exception $e) {
            $bug = $e->getMessage();
            return redirect()->back()->with('error', $bug);
        }

    }
}
