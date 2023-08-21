<?php

namespace App\Http\Controllers\Inventory;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\Inventory\Bin;
use App\Models\Inventory\Rack;
use App\Models\Inventory\Parts;
use App\Models\Inventory\Store;
use App\Models\Employee\Employee;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\Inventory\PartsReturn;
use App\Models\Inventory\ReceivedParts;
use App\Models\Inventory\InventoryStock;
use App\Models\Inventory\PriceManagement;
use App\Models\Inventory\RackBinManagement;
use App\Models\Inventory\PartsReturnDetails;
use App\Models\Inventory\ReceivedPartsDetails;

class BranchPartsReturnController extends Controller
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
                $partsreturns=PartsReturn::where('belong_to', 2)->latest()->get();
            } else {
                $employee=Employee::where('user_id',Auth::user()->id)->first();
                // $mystore=Store::where('outlet_id',$employee->outlet_id)->first();
                $mystore=Store::where('id',$employee->store_id )->first();
                if ($mystore != null) {
                    $partsreturns=PartsReturn::where('belong_to', 2)->where('from_store_id', $mystore->id)->latest()->get();
                    return view('inventory.branch_parts_return.index',compact('partsreturns'));
                }else{
                    return redirect()->back()->with('error', __("Sorry! you don't have the access."));
                }
            }
            return view('inventory.branch_parts_return.index',compact('partsreturns'));
        } catch (\Exception $e) {
            $bug = $e->getMessage();
            return redirect()->back()->with('error', $bug);
        }
    }

    public function partsReturnIndexForCentral()
    {
        try{
            $parts_return= 0;
            $auth = Auth::user();
            $user_role = $auth->roles->first();
            $mystore='';
            if ($user_role->name == 'Super Admin' || $user_role->name == 'Admin') {
                $partsreturns=PartsReturn::where('belong_to', 2)->where('is_received', 0)->latest()->get();
                return view('inventory.branch_parts_return.index_for_central',compact('partsreturns', 'parts_return'));
            }else {
                $employee=Employee::where('user_id',Auth::user()->id)->first();
                // $mystore=Store::where('outlet_id',$employee->outlet_id)->first();
                $mystore=Store::where('id',$employee->store_id )->first();
                if ($mystore != null) {
                    $partsreturns=PartsReturn::where('belong_to', 2)->where('to_store_id', $mystore->id)->where('is_received', 0)->latest()->get();
                    return view('inventory.branch_parts_return.index_for_central',compact('partsreturns', 'parts_return'));
                }else{
                    return redirect()->back()->with('error', __("Sorry! you don't have the access."));
                }
            }
        } catch (\Exception $e) {
            $bug = $e->getMessage();
            return redirect()->back()->with('error', $bug);
        }
    }

    public function receivedIndexforCentral()
    {
        try{
            $parts_return= 1;
            $auth = Auth::user();
            $user_role = $auth->roles->first();

            if ($user_role->name == 'Super Admin' || $user_role->name == 'Admin') {
                $partsreturns=ReceivedParts::where('belong_to', 1)->latest()->get();
                return view('inventory.branch_parts_return.received_index_for_central',compact('partsreturns','parts_return'));
            }else {
                $employee=Employee::where('user_id',Auth::user()->id)->first();
                // $mystore=Store::where('outlet_id',$employee->outlet_id)->first();
                $mystore=Store::where('id',$employee->store_id )->first();
                if ($mystore != null) {
                    $partsreturns=ReceivedParts::where('belong_to', 1)->where('to_store_id', $mystore->id)->latest()->get();
                    return view('inventory.branch_parts_return.received_index_for_central',compact('partsreturns','parts_return'));
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
            } else {
                $employee=Employee::where('user_id', Auth::user()->id)->first();
                // $mystore=Store::where('outlet_id', $employee->outlet_id)->first();
                $mystore=Store::where('id',$employee->store_id )->first();
                if ($mystore == null) {
                    return redirect()->back()->with('error', __('Sorry! you dont have the access.'));
                }
            }

            $stores = Store::where('status', 1)->orderBy('name')->get();
            $central_store = Store::where('user_id',null)->where('name', 'LIKE', 'Central Warehouse')->first();

            $partsreturns=PartsReturn::where('belong_to', 2)->latest()->first();
            if(!empty($partsreturns)){
                $trim=trim($partsreturns->sl_number,"B-RSL-");
                $sl=$trim + 1;
                $sl_number="B-RSL-".$sl;
            }else{
                $sl_number="B-RSL-"."1";
            }
            return view('inventory.branch_parts_return.create', compact('user_role', 'stores','sl_number', 'mystore', 'central_store'));
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
            'date'          => 'required',
            'part_id'       => 'required|array',
            'from_store_id' => 'required',
            'to_store_id'   => 'required',
            'quantity'   => 'required',
            'note'   => 'required',
        ]);

    DB::beginTransaction();
    try{
        $partsreturns=PartsReturn::where('belong_to', 2)->latest()->first();
        if(!empty($partsreturns)){
            $trim=trim($partsreturns->sl_number,"B-RSL-");
            $sl=$trim + 1;
            $sl_number="B-RSL-".$sl;
        }else{
            $sl_number="B-RSL-"."1";
        }
        $total_quantity = array_sum($request->quantity);
        $partsreturn=PartsReturn::create([
            'sl_number'      => $sl_number,
            'date'           => $request->date,
            'from_store_id'  => $request->from_store_id,
            'to_store_id'    => $request->to_store_id,
            'total_quantity' => $total_quantity,
            'belong_to'      => 2, // 2=Branch
            'description' => $request->note,
            'created_by'     => Auth::id(),
        ]);
        if($partsreturn){
            foreach($request->part_id as $key => $id){
                if($id != null &&  $id > 0){
                    $details['partsreturn_id']    = $partsreturn->id;
                    $details['parts_id']          = $id;
                    $details['rack_id']          = $request->rack_id[$key];
                    $details['bin_id']          = $request->bin_id[$key];
                    $details['required_quantity'] = $request->quantity[$key];

                    
                    $partsReturnDetails=PartsReturnDetails::create($details);
                }

                InventoryStock::create([
                    'date'=> $request->date,
                    'parts_return_id'=> $partsreturn->id ?? Null,
                    'belong_to' =>  2, //2=branch
                    'store_id' =>  $partsreturn->from_store_id,
                    'part_id' => $request->part_id[$key],
                    'stock_out' => $request->quantity[$key],
                    'created_by' => Auth::id(),
                ]);
            }
        }
        DB::commit();
        return redirect('branch/branch-parts-return')
        ->with('success', __('label.RETURN_PARTS_CREATED'));
        }catch(\Exception $e){
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

            return view('inventory.branch_parts_return.show',compact('partsreturn','partsReturnDetails'));
        } catch (\Exception $e) {
            $bug = $e->getMessage();
            return redirect()->back()->with('error', $bug);
        }
    }

    public function showForCentral($id)
    {
        try{
            $partsreturn=PartsReturn::findOrFail($id);
            $partsReturnDetails=PartsReturnDetails::where('partsreturn_id', $id)->get();
            return view('inventory.branch_parts_return.show_for_central',compact('partsreturn','partsReturnDetails'));
        } catch (\Exception $e) {
            $bug = $e->getMessage();
            return redirect()->back()->with('error', $bug);
        }
    }

    public function receivedShowForCentral($id)
    {
        try{
            $partsreturn=ReceivedParts::findOrFail($id);
            $partsReturnDetails=ReceivedPartsDetails::where('received_parts_id', $partsreturn->id)->get();
            return view('inventory.branch_parts_return.receivedShowForCentral',compact('partsreturn','partsReturnDetails'));
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

            $stock_collect = [];
            $selectParts = [];
            foreach($partsReturnDetails as $key=>$detail){
                $stock_in = InventoryStock::where('part_id',$detail->parts_id)->where('store_id',$partsreturn->from_store_id)->sum('stock_in');
                $stock_out = InventoryStock::where('part_id',$detail->parts_id)->where('store_id',$partsreturn->from_store_id)->sum('stock_out');
                $selectPart = Parts::where('id', $detail->parts_id)->first();

                $stock_in_hand = $stock_in - $stock_out;
                array_push($stock_collect,$stock_in_hand);
                array_push($selectParts, $selectPart);
            }
            $stores = Store::where('status', 1)->orderBy('name')->get();
            $parts=Parts::where('status', 1)->get();
            return view('inventory.branch_parts_return.edit', compact( 'partsreturn', 'partsReturnDetails', 'stock_collect','stores','selectParts','parts'));
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

    }

    public function updatePartsReturn(Request $request, $id)
    {
        $this->validate($request, [
            'date'          => 'required',
            'part_id'       => 'required|array',
            'from_store_id' => 'required',
            'to_store_id'   => 'required',
            'note'   => 'required',
        ]);
        DB::beginTransaction();
        try{
            $total_quantity = array_sum($request->required_quantity);
            $partsreturn= PartsReturn::find($id);
            $partsreturn->update([
                //'sl_number'      =>  $request->sl_no,
                'date'           =>  $request->date,
                'from_store_id'  =>$request->from_store_id,
                'to_store_id'    =>$request->to_store_id,
                'total_quantity' =>$total_quantity,
                'belong_to'      => 2, // 2=Branch
                'description' => $request->note,
                'updated_by'     => Auth::id(),
            ]);
            if($partsreturn != null){
                foreach($request->part_id as $key => $id){
                    $PartsReturnDetails = PartsReturnDetails::where('partsreturn_id', $partsreturn->id)
                                                                ->where('parts_id',$id)->first();
                    $PartsReturnDetails->update([
                        'required_quantity'      => $request->required_quantity[$key],
                    ]);
                    
                    $InventoryStock = InventoryStock::where('parts_return_id', $partsreturn->id)
                    ->where('part_id',$id )->first();
                    if ($InventoryStock != null) {
                        $InventoryStock->update([
                            'date' => $request->date,
                            'stock_out' => $request->required_quantity[$key],
                            'updated_by' => Auth::id(),
                        ]);
                    }

                }
            }
            DB::commit();
            return redirect('branch/branch-parts-return')
            ->with('success', __('label.RETURN_PARTS_UPDATED'));
        }catch(\Exception $e){
            DB::rollback();
            $bug = $e->getMessage();
            return redirect()->back()->with('error', $bug);
        }
    }


    public function partsReturnRow(Request $request)
    {
        $part_id = $request->parts_id;
        $rackbinInfo = [];
        $stock_collect = [];
        $partInfo_collect = [];
        if($part_id!=null){
            $auth = Auth::user();
            $user_role = $auth->roles->first();
            foreach($part_id as $key=>$pr_id){
                $rackbin=RackBinManagement::where('parts_id',$pr_id)->where('store_id',$request->from_store_id)->first();
                $stock_in = InventoryStock::where('part_id', $pr_id)->where('store_id', $request->from_store_id)->sum('stock_in');
                $stock_out = InventoryStock::where('part_id', $pr_id)->where('store_id', $request->from_store_id)->sum('stock_out');

                $partsInfo=Parts::where('id', $pr_id)->first();
                $stock_in_hand = $stock_in - $stock_out;
                array_push($rackbinInfo, $rackbin);
                array_push($stock_collect,$stock_in_hand);
                array_push($partInfo_collect,$partsInfo);
            }
        }
 
        $html = view('inventory.branch_parts_return.part_return_row', compact('partInfo_collect','stock_collect','rackbinInfo'))->render();
        return response()->json(compact('html'));
    }
    public function receiveParts($id){
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
            return view('inventory.branch_parts_return.receive_parts', compact('partsReturn', 'details', 'stock_collect', 'rackbinInfo', 'racks'));
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
                    'date' => $request->date, //$request->date,
                    'parts_return_id' => $partReturn->id,
                    'from_store_id' => $partReturn->from_store_id,
                    'to_store_id' => $partReturn->to_store_id,
                    'total_requested_quantity' => $partReturn->total_quantity,
                    'total_receiving_quantity' => $received_quantity,
                    'belong_to' => 1,
                    'status'    => 1,
                    'created_by' => Auth::id(),
                ]);
            }
        foreach($request->receiving_quantity as $key=> $value){
            // $priceManagement=PriceManagement::where('part_id',$request->part_id)
            // ->first();

            $partsReturnDetails= PartsReturnDetails::where('partsreturn_id', $request->parts_return_id)
                                ->where('parts_id',$request->part_id[$key])->first();

            $partsReturnDetails->update([
                'received_quantity' => $request->receiving_quantity[$key]
            ]);

            $inventoryStock= InventoryStock::where('parts_return_id', $partReturn->id)->where('part_id',$request->part_id[$key])->first();

                if($request->receiving_quantity[$key] > 0){
                    $receivedDetails['received_parts_id'] = $receivedParts->id;
                    $receivedDetails['belong_to'] = 1;
                    $receivedDetails['store_id'] = $partReturn->to_store_id;
                    $receivedDetails['rack_id'] = $request->rack_id[$key];
                    $receivedDetails['bin_id'] = $request->bin_id[$key];
                    $receivedDetails['part_category_id'] = $request->part_category_id[$key];
                    $receivedDetails['part_id'] = $request->part_id[$key];
                    $receivedDetails['required_quantity'] = $partsReturnDetails->required_quantity;
                    $receivedDetails['received_quantity'] = $request->receiving_quantity[$key];
                    $receivedDetails['created_by'] = Auth::id();

                    $receivedPartsDetails= ReceivedPartsDetails::create($receivedDetails);

                    if ($inventoryStock != null) {
                        $inventoryStock->update([
                            'stock_out' => $request->receiving_quantity[$key],
                            'updated_by' => Auth::id(),
                        ]);
                    }


                    InventoryStock::create([
                        'returned_parts_receive_id' => $receivedParts->id,
                        'belong_to' =>  1, //1=Central
                        // 'price_management_id' => $priceManagement->id,
                        'store_id' =>  $partReturn->to_store_id,
                        'part_category_id' => $request->part_category_id[$key],
                        'part_id' => $request->part_id[$key],
                        'stock_in' => $request->receiving_quantity[$key],
                        'created_by' => Auth::id(),
                    ]);
                }
        }
        return redirect()->route('central.parts-return.received')->with('success', __('Parts received successfully.'));
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
            return view('inventory.branch_parts_return.receive_parts_edit', compact('ReceivedParts', 'details', 'stock_collect','inventory_stocks'));
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
            return redirect()->route('central.parts-return.received')->with('success', __('Parts receive updated successfully.'));
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
        try{
            $partsreturn=PartsReturn::findOrFail($id);
            if ($partsreturn != null) {
                $receivedParts=ReceivedParts::where('parts_return_id', $partsreturn->id)->get();
                if(count($receivedParts) > 0 ){
                    return back()->with('error', "Sorry! Can't Delete. This Parts Return Has Been Received Already");
                }else{
                    $partsReturnDetails=PartsReturnDetails::where('partsreturn_id',$partsreturn->id)->get();

                    foreach ($partsReturnDetails as $key => $value) {
                        $parts_return_inventory_stock= InventoryStock::where('parts_return_id', $partsreturn->id)->where('part_id', $value->parts_id)->first();
                        if ($parts_return_inventory_stock != null) {
                            $parts_return_inventory_stock->delete();
                        }
                        $value->delete();
                    }
                    $partsreturn->delete();
                    return back()->with('success', 'Parts Return Deleted Successfully.');
                }

            }
        } catch (\Exception $e) {
            $bug = $e->getMessage();
            return redirect()->back()->with('error', $bug);
        }
    }

    public function receiveDestroy($id)
    {
        try{
            $receivedParts=ReceivedParts::findOrFail($id);


            
            if ($receivedParts != null) {
                $partsReturn=PartsReturn::where('id', $receivedParts->parts_return_id)->first()->update([
                    'status' => 0, 
                    'is_received' => 0,
                    'total_receiving_quantity' => 0,
                ]);
                
                $partsreceiveDetails=ReceivedPartsDetails::where('received_parts_id',$receivedParts->id)->get();
                foreach ($partsreceiveDetails as $key => $value) {
                    $partsReturnDetails=PartsReturnDetails::where('partsreturn_id', $receivedParts->parts_return_id )->where('parts_id', $value->part_id)->first();
                    $partsReturnDetails->update([
                        'received_quantity' => 0,
                    ]);

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
