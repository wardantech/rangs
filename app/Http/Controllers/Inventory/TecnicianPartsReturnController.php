<?php

namespace App\Http\Controllers\Inventory;

use Session;
use App\User;
use Redirect;
use Validator;
use DataTables;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\Inventory\Bin;
use App\Models\Outlet\Outlet;
use App\Models\Inventory\Rack;
use App\Models\Inventory\Parts;
use App\Models\Inventory\Store;
use App\Models\Employee\Employee;
use Illuminate\Support\Facades\DB;
use App\Models\Inventory\Inventory;
use App\Http\Controllers\Controller;
use App\Models\Inventory\PartsModel;
use Illuminate\Support\Facades\Auth;
use App\Models\Inventory\PartsReturn;
use App\Models\Inventory\ReceivedParts;
use App\Models\Inventory\InventoryStock;
use App\Models\Inventory\PriceManagement;
use App\Models\Inventory\RackBinManagement;
use App\Models\Inventory\PartsReturnDetails;
use App\Models\Inventory\ReceivedPartsDetails;

class TecnicianPartsReturnController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    //Index For Technician
    public function indexforTechnician()
    {
        try{
            $auth = Auth::user();
            $user_role = $auth->roles->first();
            $mystore='';
            if ($user_role->name == 'Super Admin' || $user_role->name == 'Admin') {
                $partsreturns=PartsReturn::latest()->get();
                return view('inventory.partsreturn.index',compact('partsreturns'));
            } else {
                $mystore=Store::where('user_id',Auth::user()->id)->first();
                if ($mystore != null) {
                    $partsreturns=PartsReturn::where('from_store_id', $mystore->id)->latest()->get();
                    return view('inventory.partsreturn.index',compact('partsreturns'));
                }else{
                    return redirect()->back()->with('error', __('Sorry you dont have the access.'));
                }
            }
        } catch (\Exception $e) {
            $bug = $e->getMessage();
            return redirect()->back()->with('error', $bug);
        }
    }

    public function indexforBranch()
    {
        try{
            $auth = Auth::user();
            $user_role = $auth->roles->first();
            $mystore='';
            if ($user_role->name == 'Super Admin' || $user_role->name == 'Admin') {
                $partsreturns=PartsReturn::where('is_received', 0)->latest()->get();
                return view('inventory.partsreturn.index_for_branch',compact('partsreturns'));
            } else {
                $employee=Employee::where('user_id',Auth::user()->id)->first();
                // $mystore=Store::where('outlet_id',$employee->outlet_id)->first();
                $mystore=Store::where('id',$employee->store_id )->first();
                if ($mystore != null) {
                    $partsreturns=PartsReturn::where('to_store_id', $mystore->id)->where('is_received',0)->latest()->get();
                    return view('inventory.partsreturn.index_for_branch',compact('partsreturns'));
                }else{
                    return redirect()->back()->with('error', __('Sorry! you dont have the access.'));
                }
            }
        } catch (\Exception $e) {
            $bug = $e->getMessage();
            return redirect()->back()->with('error', $bug);
        }

    }

    public function receivedIndexforBranch()
    {
        try{
            $auth = Auth::user();
            $user_role = $auth->roles->first();
            $mystore='';
            if ($user_role->name == 'Super Admin' || $user_role->name == 'Admin') {
                // $partsreturns=PartsReturn::where('is_received', 1)->latest()->get();
                $receivedParts=ReceivedParts::latest()->get();
                return view('inventory.partsreturn.received_index_for_branch',compact('receivedParts'));
            } else {
                $employee=Employee::where('user_id',Auth::user()->id)->first();
                // $mystore=Store::where('outlet_id',$employee->outlet_id)->first();
                $mystore=Store::where('id',$employee->store_id )->first();
                if ($mystore != null) {
                    // $partsreturns=PartsReturn::where('to_store_id', $mystore->id)->where('is_received',1)->latest()->get();
                    $receivedParts=ReceivedParts::where('to_store_id', $mystore->id)->latest()->get();
                    return view('inventory.partsreturn.received_index_for_branch',compact('receivedParts'));
                }else{
                    return redirect()->back()->with('error', __('Sorry! you dont have the access.'));
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
                $employeebelongToStore='';
            } else {
                $mystore=Store::where('user_id',Auth::user()->id)->first();
                $employee=Employee::where('user_id',Auth::user()->id)->first();
                $employeebelongToStore=Store::where('id',$employee->store_id)->first();
                if ($mystore == null) {
                    return redirect()->back()->with('error', __('Sorry! you dont have the access.'));
                }
            }
            // dd(Auth::user()->id);
            $outlates = Outlet::where('status', 1)->orderBy('name')->get();
            $stores = Store::where('status', 1)->orderBy('name')->get();
            $parts=Parts::where('status', 1)->orderBy('name')->get();

            //
            // $partsreturns=PartsReturn::where('belong_to', 3)->latest()->first(); //Belong to 3=Technician
            // if(!empty($partsreturns)){
            //     $trim=trim($partsreturns->sl_number,"T-RSL-");
            //     $sl=$trim + 1;
            //     $sl_number="T-RSL-".$sl;
            // }else{
            //     $sl_number="T-RSL-"."1";
            // }
            return view('inventory.partsreturn.create',compact('outlates','parts','stores','mystore','user_role', 'employeebelongToStore'));
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
                'date' => 'required',
                'part_id' => 'required|array',
                'from_store_id' => 'required',
                'to_store_id' => 'required',
            ]);

        DB::beginTransaction();
        try{
            $total_quantity = array_sum($request->required_quantity);
            $partsreturn=PartsReturn::create([
                'sl_number' =>  $request->sl_no,
                'date' =>  $request->date,
                'from_store_id'=>$request->from_store_id,
                'to_store_id'=>$request->to_store_id,
                'total_quantity'=>$total_quantity,
                'belong_to'=> 3, // 3=Technician
                'created_by' => Auth::id(),
            ]);
            if($partsreturn){
                foreach($request->part_id as $key => $id){
                    if($id != null &&  $id > 0){
                        $details['partsreturn_id'] = $partsreturn->id;
                        $details['parts_id'] = $id;
                        $details['required_quantity'] = $request->required_quantity[$key];
                        $partsReturnDetails= PartsReturnDetails::create($details);
                    }

                    InventoryStock::create([
                        'date'=> $request->date,
                        'parts_return_id'=> $partsreturn->id ?? Null,
                        'belong_to' =>  3, //3=technician
                        'store_id' =>  $partsreturn->from_store_id,
                        'part_id' => $request->part_id[$key],
                        'stock_out' => $request->required_quantity[$key],
                        'created_by' => Auth::id(),
                    ]);
                }
            }
            DB::commit();
            return redirect('technician/parts-return')
            ->with('success', __('label.RETURN_PARTS_CREATED'));
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
            $partsreturn=PartsReturn::findOrFail($id);
            $partsReturnDetails=PartsReturnDetails::where('partsreturn_id',$partsreturn->id)->get();
            return view('inventory.partsreturn.show',compact('partsreturn','partsReturnDetails'));
        } catch (\Exception $e) {
            $bug = $e->getMessage();
            return redirect()->back()->with('error', $bug);
        }
    }

    public function showForBranch($id)
    {
        try{
            $partsreturn=PartsReturn::findOrFail($id);
            $partsReturnDetails=PartsReturnDetails::where('partsreturn_id', $partsreturn->id)->get();
            return view('inventory.partsreturn.show_for_branch',compact('partsreturn','partsReturnDetails'));
        } catch (\Exception $e) {
            $bug = $e->getMessage();
            return redirect()->back()->with('error', $bug);
        }
    }

    public function receivedShowForBranch($id)
    {
        try{
            $partsreturn=ReceivedParts::findOrFail($id);
            $partsReturnDetails=ReceivedPartsDetails::where('received_parts_id', $partsreturn->id)->get();
            return view('inventory.partsreturn.received_show_for_branch',compact('partsreturn','partsReturnDetails'));
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
            $partsreturn=PartsReturn::findOrFail($id);
            $partsReturnDetails=PartsReturnDetails::where('partsreturn_id', $partsreturn->id)->get();
            $stock_collect=[];
            $selectParts=[];
            foreach($partsReturnDetails as $partsReturnDetail){
                $partsInfo=Parts::where('id', $partsReturnDetail->parts_id)->first();
                $stock_in = InventoryStock::where('part_id',$partsReturnDetail->parts_id)->where('store_id',$partsreturn->from_store_id)->sum('stock_in');
                $stock_out = InventoryStock::where('part_id',$partsReturnDetail->parts_id)->where('store_id',$partsreturn->from_store_id)->sum('stock_out');
                $stock_in_hand = $stock_in - $stock_out;
                array_push($selectParts,$partsInfo);
                array_push($stock_collect,$stock_in_hand);
            }

            $parts=Parts::where('status', 1)->get();
            return view('inventory.partsreturn.edit', compact( 'partsreturn', 'partsReturnDetails', 'stock_collect','selectParts','parts'));
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
            'date'          => 'required',
            'part_id'       => 'required|array',
            'from_store_id' => 'required',
            'to_store_id'   => 'required',
        ]);

        DB::beginTransaction();
        try{
        $total_quantity = array_sum($request->required_quantity);
        $partsreturn= PartsReturn::find($id);
        $partsreturn->update([
            'sl_number'      => $request->sl_no,
            'date'           => $request->date,
            'from_store_id'  => $request->from_store_id,
            'to_store_id'    => $request->to_store_id,
            'total_quantity' => $total_quantity,
            'belong_to'      => 3, // 3=Technician
            'updated_by'     => Auth::id(),
        ]);
        if($partsreturn){
            foreach($request->part_id as $key => $id){

                $PartsReturnDetails = PartsReturnDetails::where('partsreturn_id', $partsreturn->id)
                                                            ->where('parts_id',$id)->first();
                $PartsReturnDetails->update([
                    'required_quantity'      => $request->required_quantity[$key],
                ]);
                
                $InventoryStock = InventoryStock::where('parts_return_id', $partsreturn->id)
                ->where('part_id',$id)->first();
                
                $InventoryStock->update([
                    'date' => $request->date,
                    'stock_out' => $request->required_quantity[$key],
                    'updated_by' => Auth::id(),
                ]);

                
            }
        }
        DB::commit();
        return redirect('technician/parts-return')
        ->with('success', __('label.RETURN_PARTS_CREATED'));
    } catch (\Exception $e) {
        DB::rollback();
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
        try{
            $partsreturn=PartsReturn::findOrFail($id);
            
            if ($partsreturn != null) {
                $partsReturnDetails=PartsReturnDetails::where('partsreturn_id',$partsreturn->id)->get();
                foreach ($partsReturnDetails as $key => $value) {
                    $parts_return_inventory_stock= InventoryStock::where('parts_return_id', $partsreturn->id)->where('part_id', $value->parts_id)->first();
                    if ($parts_return_inventory_stock != null) {
                        $parts_return_inventory_stock->delete();
                    }
                    $value->delete();
                }
                $partsreturn->delete();
                return redirect()->route('technician.parts-return')->with('success', __('Data Deleted Successfully.'));
            }
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

    // public function returnPartsCentralReceive($id){
    //     try{
    //         $partsReturn = PartsReturn::find($id);

    //     $partsReturn_details = PartsReturnDetails::where('partsreturn_id',$partsReturn->id)->get();
    //     foreach($partsReturn_details as $key=> $detail){
                
    //             InventoryStock::create([
    //                 'parts_model_id' => $detail->model_id ,
    //                 'part_id' => $detail->parts_id,
    //                 'stock_in' => $detail->quantity,
    //                 'belong_to' => 1, // 1=Central WareHouse
    //                 'type' => 2,
    //                 'created_by' => Auth::id(),
    //             ]);
    //             InventoryStock::create([
    //                 'parts_model_id' => $detail->model_id ,
    //                 'part_id' => $detail->parts_id,
    //                 'stock_out' => $detail->quantity,
    //                 'belong_to' => 2, // 2=Outlet
    //                 'type' => 2,
    //                 'created_by' => Auth::id(),
    //             ]);
    //     }
    //     return redirect()->route('outlet.requisitionList')->with('success', __('Allocated parts received successfully.'));
    //     } catch (\Exception $e) {
    //         $bug = $e->getMessage();
    //         return redirect()->back()->with('error', $bug);
    //     }

    // }

    public function receiveParts($id)
    {
        try{
            $partsReturn = PartsReturn::where('id', $id)->first();

            $details = PartsReturnDetails::where('partsreturn_id', $partsReturn->id)->with('part')->get();
            $racks=Rack::where('status', 1)->where('store_id',$partsReturn->to_store_id)->get();
            $rackbinInfo = [];
            $stock_collect = [];
            foreach($details as $key=>$detail){
                $rackbin=RackBinManagement::where('parts_id',$detail->parts_id)->where('store_id',$partsReturn->to_store_id)->first();
                $stock_in = InventoryStock::where('part_id',$detail->parts_id)->where('store_id', $partsReturn->to_store_id)->sum('stock_in');
                $stock_out = InventoryStock::where('part_id',$detail->parts_id)->where('store_id', $partsReturn->to_store_id)->sum('stock_out');
                $stock_in_hand = $stock_in - $stock_out;
                array_push($rackbinInfo, $rackbin);
                array_push($stock_collect,$stock_in_hand);
            }
            return view('inventory.partsreturn.receive_parts', compact('partsReturn', 'details', 'stock_collect', 'racks','rackbinInfo'));
        } catch (\Exception $e) {
            $bug = $e->getMessage();
            return redirect()->back()->with('error', $bug);
        }
    }

    public function receivePartsStore(Request $request){
        
        $this->validate($request, [
            'receiving_quantity' => 'required',
        ]);

        try {
        $partReturn = PartsReturn::find($request->parts_return_id);
        $received_quantity = array_sum($request->receiving_quantity);
        if($partReturn != null){
            $partReturn->update([
                'total_receiving_quantity' => $received_quantity,
                'status' => 1,
                'is_received' => 1
            ]);
            $receivedParts=ReceivedParts::create([
                            'date'                     => $request->date, //$request->date,
                            'parts_return_id'          => $partReturn->id,
                            'from_store_id'            => $partReturn->from_store_id,
                            'to_store_id'              => $partReturn->to_store_id,
                            'total_requested_quantity' => $partReturn->total_quantity,
                            'total_receiving_quantity' => $received_quantity,
                            'belong_to'                => 2,
                            'status'    => 1,
                            'created_by' => Auth::id(),
                        ]);
        }
        foreach($request->receiving_quantity as $key=> $value){
            // $priceManagement=PriceManagement::where('part_id',$request->part_id)->first();
            $partsReturnDetails= PartsReturnDetails::where('partsreturn_id', $request->parts_return_id)
                                ->where('parts_id',$request->part_id[$key])->first();
            $partsReturnDetails->update([
                'received_quantity' => $request->receiving_quantity[$key]
            ]);

            $inventoryStock= InventoryStock::where('parts_return_id', $partReturn->id)
            ->where('part_id',$request->part_id[$key])->first();

                //stock out from OUTLET
                if($request->receiving_quantity[$key] > 0){
                    $receivedDetails['received_parts_id'] = $receivedParts->id;
                    $receivedDetails['belong_to'] = 2;
                    $receivedDetails['store_id'] = $partReturn->to_store_id;
                    $receivedDetails['rack_id'] = $request->rack_id[$key];
                    $receivedDetails['bin_id'] = $request->bin_id[$key];
                    $receivedDetails['part_category_id'] = $request->part_category_id[$key];
                    $receivedDetails['part_id'] = $request->part_id[$key];
                    $receivedDetails['required_quantity'] = $partsReturnDetails->required_quantity;
                    $receivedDetails['received_quantity'] = $request->receiving_quantity[$key];
                    $receivedDetails['created_by'] = Auth::id();

                    $receivedPartsDetails= ReceivedPartsDetails::create($receivedDetails);

                    $inventoryStock->update([
                        'stock_out' => $request->receiving_quantity[$key],
                        'updated_by' => Auth::id(),
                    ]);

                    InventoryStock::create([
                        'returned_parts_receive_id' => $receivedParts->id,
                        'belong_to' =>  2, //2=OUTLET
                        // 'price_management_id' => $priceManagement->id,
                        'store_id' =>  $partReturn->to_store_id,
                        'part_category_id' => $request->part_category_id[$key],
                        'part_id' => $request->part_id[$key],
                        'stock_in' => $request->receiving_quantity[$key],
                        'created_by' => Auth::id(),
                    ]);

                }
        }
        return redirect()->route('branch.parts-return.received')->with('success', __('Allocated parts received successfully.'));
        // return "success";
        } catch (\Exception $e) {
            $bug = $e->getMessage();
            return redirect()->back()->with('error', $bug);
        }
    }

    public function receiveEdit($id)
    {
        try{
            $ReceivedParts = ReceivedParts::where('id', $id)->first();

            $details = ReceivedPartsDetails::where('received_parts_id', $ReceivedParts->id)->with('part')->get();
            
            $stock_collect = [];
            $inventory_stocks= [];
            foreach($details as $key=>$detail){
                $stock_in = InventoryStock::where('part_id', $detail->part_id)->where('store_id', $ReceivedParts->to_store_id)->sum('stock_in');
                $stock_out = InventoryStock::where('part_id', $detail->part_id)->where('store_id', $ReceivedParts->to_store_id)->sum('stock_out');
                $stock_in_hand = $stock_in - $stock_out;
                $inventory_stock= InventoryStock::where('returned_parts_receive_id', $ReceivedParts->id)->where('part_id', $detail->part_id)->where('store_id', $ReceivedParts->to_store_id)->first();


                array_push($stock_collect, $stock_in_hand);
                array_push($inventory_stocks, $inventory_stock);
            }
            return view('inventory.partsreturn.receive_parts_edit', compact('ReceivedParts', 'details', 'stock_collect'));
        } catch (\Exception $e) {
            $bug = $e->getMessage();
            return redirect()->back()->with('error', $bug);
        }
    }

    public function receiveupdate(Request $request){
        try {
            $receivedParts= ReceivedParts::where('id', $request->parts_return_id)->first();

            $received_quantity = array_sum($request->receiving_quantity);
            $partsReturn=PartsReturn::where('id', $receivedParts->parts_return_id)->first();

            $receivedParts->update([
                'total_receiving_quantity' => $received_quantity
            ]);
            
            $partsReturn->update([
                'total_receiving_quantity' => $received_quantity
            ]);
            $partsReturnDetails= PartsReturnDetails::where('partsreturn_id', $partsReturn->id)->get(); 
            foreach($partsReturnDetails as $key=> $partsReturnDetail){                                    
                $partsReturnDetail->update([
                    'received_quantity' => $request->receiving_quantity[$key]
                ]);
                $parts_return_inventory_stock= InventoryStock::where('parts_return_id', $partsReturn->id)->where('part_id', $partsReturnDetail->parts_id)->first();
                $parts_return_inventory_stock->update([
                    'stock_out' => $request->receiving_quantity[$key],
                    'updated_by' => Auth::id(),
                ]);
            }
            $receivedPartsDetails= ReceivedPartsDetails::where('received_parts_id', $receivedParts->id)->get();
            foreach ($receivedPartsDetails as $key => $value) {
                $value->update([
                    'received_quantity' => $request->receiving_quantity[$key],
                    'updated_by' => Auth::id(),
                ]);
                $parts_receive_inventory_stock= InventoryStock::where('returned_parts_receive_id', $receivedParts->id)->where('part_id', $value->part_id)
                    ->update([
                    'stock_in' => $request->receiving_quantity[$key],
                    'updated_by' => Auth::id(),
                    ]);
            }

        return redirect()->route('branch.parts-return.received')->with('success', __('Allocated parts receive updated successfully.'));
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
    public function receiveDestroy($id)
    {
        try{
            $receivedParts=ReceivedParts::findOrFail($id);

            $partsReturn=PartsReturn::where('id', $receivedParts->parts_return_id)->first();
            if($partsReturn != null){
                $partsReturn->update([
                    'status' => 0,
                    'is_received' => 0,
                    'total_receiving_quantity' => 0,
                ]);
            }

            if ($receivedParts != null) {
                $partsreceiveDetails=ReceivedPartsDetails::where('received_parts_id',$receivedParts->id)->get();
                foreach ($partsreceiveDetails as $key => $value) {
                    $partsReturnDetails=PartsReturnDetails::where('partsreturn_id', $receivedParts->parts_return_id )->where('parts_id', $value->part_id)->first();
                    if ($partsReturnDetails !=null ) {
                        $partsReturnDetails->update([
                            'received_quantity' => 0,
                        ]);
                    }


                    $parts_return_inventory_stock= InventoryStock::where('parts_return_id', $receivedParts->parts_return_id)->where('part_id', $value->part_id)->first();
                    if ($parts_return_inventory_stock != null) {
                        $parts_return_inventory_stock->update([
                            'stock_out'=>$partsReturnDetails->required_quantity,
                        ]);
                    }
                    
                    $InventoryStock = InventoryStock::where('returned_parts_receive_id', $receivedParts->id)->where('part_id', $value->part_id)->first();
                    if ($InventoryStock != null) {
                        $InventoryStock->delete();
                    }
                    $value->delete();
                }
                $receivedParts->delete();
                return redirect()->back()->with('success', __('Data Deleted Successfully.'));
            }
        } catch (\Exception $e) {
            $bug = $e->getMessage();
            return redirect()->back()->with('error', $bug);
        }

    }
}
