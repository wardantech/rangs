<?php

namespace App\Http\Controllers\Allocation;

use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use App\Models\Inventory\Bin;
use App\Models\Inventory\Rack;
use App\Models\Inventory\Store;
use App\Models\Employee\Employee;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\Requisition\Allocation;
use App\Models\Requisition\Requisition;
use App\Models\Inventory\InventoryStock;
use App\Models\Inventory\PriceManagement;
use App\Models\Inventory\RackBinManagement;
use App\Models\Requisition\AllocationDetails;
use App\Models\Requisition\RequisitionDetails;
use App\Models\Requisition\BranchAllocationReceived;
use App\Models\Requisition\BranchAllocationReceivedDetali;

class BranchAllocationReceivedController extends Controller
{
    public function index()
    {
        try{
            $auth = Auth::user();
            $employee=Employee::where('user_id',Auth::user()->id)->first();
            $user_role = $auth->roles->first();
            $mystore='';

            if($user_role->name == 'Branch Store Executive') {
                // $mystore=Store::where('outlet_id',$employee->outlet_id)->first();
                $mystore=Store::where('id',$employee->store_id )->first();
                if ($mystore != null) {
                    $receives = BranchAllocationReceived::where('belong_to',2)->where('store_id', $mystore->id)
                    ->latest()->get();
                }else{
                    return redirect()->back()->with('error', __("Sorry you don't have the permission."));
                }

            }elseif($user_role->name == 'Admin' || $user_role->name == 'Super Admin' || $user_role->name =='Store Admin') {
                $receives = BranchAllocationReceived::where('belong_to',2)
                            ->latest()->get();
            }else {
                // $mystore=Store::where('outlet_id',$employee->outlet_id)->first();
                $mystore=Store::where('id',$employee->store_id )->first();
                if ($mystore != null) {
                    $receives = BranchAllocationReceived::where('belong_to',2)->where('store_id', $mystore->id)
                    ->latest()->get();
                }else{
                    return redirect()->back()->with('error', __("Sorry you don't have the permission."));
                }
            }
            return view('allocation.branch.received.index', compact('receives'));
        } catch (\Exception $e) {
            $bug = $e->getMessage();
            return redirect()->back()->with('error', $bug);
        }

    }

    public function allocationReceiveForm($id)
    {
        try{
            $allocation = Allocation::find($id);

            $details = AllocationDetails::where('allocation_id', $allocation->id)
                ->with('part')->get();

            $rackbinInfo= [];
            foreach($details as $key=>$value){
                    $rackbin=RackBinManagement::where('parts_id',$value->parts_id)->where('store_id',$allocation->to_store_id)->first();
                    array_push($rackbinInfo, $rackbin);
                }
            $stock_collect = $this->stockInHand($details, $allocation);

            return view('requisition.requisition.outlet_receive', compact(
                'allocation', 'details', 'stock_collect', 'rackbinInfo'
            ));
        } catch (\Exception $e) {
            $bug = $e->getMessage();
            return redirect()->back()->with('error', $bug);
        }
    }

    public function requisitionOutletReceiveStore(Request $request)
    {
        DB::beginTransaction();
        try {
            $received_quantity = array_sum($request->receiving_quantity);
            $allocation = Allocation::find($request->allocation_id);
            
            $qnty=$allocation->received_quantity;
            $total=$qnty + $received_quantity;

            if($allocation != null){
                $allocation->update([
                    'is_received' => 1,
                    'status' => 1,
                    'received_quantity' => $received_quantity,
                ]);
            }

            $requisition = Requisition::findOrFail($allocation->requisition_id);

            if($requisition != null){
                $requisition->update([
                    'status' => 2,
                    'received_quantity' => $received_quantity,
                ]);
            }

            // Store in received and branch_allocation_receiveds table.
            $employee = Employee::where('user_id',Auth::user()->id)->first();

            $data['date'] = $request->receiveing_date;
            $data['belong_to'] = 2;
            $data['store_id'] = $request->store_id;
            $data['allocation_id'] = $request->allocation_id;
            $data['employee_id'] = $employee->id ?? null;
            $data['requisition_no'] = $request->requisition_no;
            $data['is_received'] = 1;
            $data['allocate_quantity'] = array_sum($request->issued_quantity);
            $issued_quantity = array_sum($request->issued_quantity);
            $received = BranchAllocationReceived::create($data);

            foreach($request->issued_quantity as $key=> $quantity) {

                if($received) {
                    $receivedDetails['branch_allocation_received_id'] = $received->id;
                    $receivedDetails['part_id'] = $request->part_id[$key];
                    $receivedDetails['part_category_id'] = $request->part_category_id[$key];
                    $receivedDetails['allocation_details_id'] = $request->allocation_details_id[$key];
                    $receivedDetails['rack_id'] = $request->rack_id[$key];
                    $receivedDetails['bin_id'] = $request->bin_id[$key];
                    $receivedDetails['stock_in_hand'] = $request->stock_in_hand[$key];
                    $receivedDetails['issued_quantity'] = $request->issued_quantity[$key];
                    $receivedDetails['receiving_quantity'] = $request->receiving_quantity[$key];
                    $details = BranchAllocationReceivedDetali::create($receivedDetails);
                    
                    RequisitionDetails::where('requisition_id',$allocation->requisition_id)
                    ->where('parts_id',$request->part_id[$key])
                    ->update([
                        'received_quantity' => $request->receiving_quantity[$key]
                    ]);
                    
                    //
                    AllocationDetails::where('allocation_id', $allocation->id)
                    ->where('parts_id',$request->part_id[$key])
                    ->update([
                        'received_quantity' => $request->receiving_quantity[$key]
                    ]);

                    
                    InventoryStock::where(
                        'allocation_id', $allocation->id
                    )
                    ->where('part_id', $request->part_id[$key])
                    ->update([
                        'stock_out' => $request->receiving_quantity[$key],
                        'updated_by' => Auth::id(),  
                    ]);

                    InventoryStock::create([
                            'branch_allocation_received_id' => $received->id,
                            'branch_received_details_id' => $details->id,
                            'belong_to' =>  2, // 2=Branch
                            'store_id' =>  $requisition->from_store_id,
                            'part_category_id' => $request->part_category_id[$key],
                            'part_id' => $request->part_id[$key],
                            'stock_in' => $request->receiving_quantity[$key],
                            'type' => 2,
                            'created_by' => Auth::id(),
                        ]);
                }
            }

            DB::commit();
            return redirect()->route('branch.allocation.received.index')
                    ->with('success', __('Allocated parts received successfully.'));
        } catch (\Exception $e) {
            DB::rollback();
            $bug = $e->getMessage();
            return redirect()->back()->with('error', $bug);
        }
    }

    public function show($id)
    {
        try{
            $received = BranchAllocationReceived::with('allocation')->findOrFail($id);
            $receivedDetails = BranchAllocationReceivedDetali::where(
                'branch_allocation_received_id', $received->id
            )->with('part')->get();

            return view('allocation.branch.received.show', compact(
                'received',
                'receivedDetails'
            ));
        } catch (\Exception $e) {
            $bug = $e->getMessage();
            return redirect()->back()->with('error', $bug);
        }
    }

    public function edit($id)
    {
        try{
            $received = BranchAllocationReceived::with('allocation', 'receivedDetails')
                        ->findOrFail($id);
            $receivedDetails = BranchAllocationReceivedDetali::where(
                'branch_allocation_received_id', $received->id
            )->with('part')->get();
            $stock_collect = [];
            foreach($receivedDetails as $key=>$detail){
                $stock_in = InventoryStock::where('part_id',$detail->part_id)->where('store_id',$received->store_id)->sum('stock_in');
                $stock_out = InventoryStock::where('part_id',$detail->part_id)->where('store_id',$received->store_id)->sum('stock_out');
                $stock_in_hand = $stock_in - $stock_out;
                array_push($stock_collect,$stock_in_hand);
            }

            return view('allocation.branch.received.edit', compact(
                'received', 'stock_collect'
            ));
        } catch (\Exception $e) {
            $bug = $e->getMessage();
            return redirect()->back()->with('error', $bug);
        }
    }

    public function update(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $received = BranchAllocationReceived::with('allocation', 'receivedDetails')
                    ->findOrFail($id);
            $requisition = Requisition::findOrFail($received->allocation->requisition_id);
            $received_quantity = array_sum($request->receiving_quantity);

            if($requisition != null){
                $requisition->update([
                    'status' => 2,
                    'received_quantity' => $received_quantity,
                ]);
            }
            $allocation = Allocation::find($request->allocation_id);
            if($allocation != null){
                $allocation->update([
                    'received_quantity' => $received_quantity,
                ]);
            }
            // Store in received and branch_allocation_receiveds table.
            $employee = Employee::where('user_id',Auth::user()->id)->first();
            
            // BranchAllocationReceived Update
            $data['date'] = $request->receiveing_date;
            $data['belong_to'] = 2;
            $data['store_id'] = $request->store_id;
            $data['allocation_id'] = $request->allocation_id;
            $data['employee_id'] = $employee->id ?? null;
            $data['requisition_no'] = $request->requisition_no;
            $data['is_received'] = 1;
            $data['allocate_quantity'] = array_sum($request->issued_quantity);
            $issued_quantity = array_sum($request->issued_quantity);
            $received->update($data);

            foreach ($request->receiving_quantity as $key=> $value) {

                RequisitionDetails::where('requisition_id', $requisition->id)
                        ->where('parts_id',$request->part_id[$key])
                        ->update([
                            'received_quantity' => $request->receiving_quantity[$key]
                        ]);                    
                AllocationDetails::where('allocation_id', $received->allocation_id)
                        ->where('parts_id',$request->part_id[$key])
                        ->update([
                            'received_quantity' => $request->receiving_quantity[$key]
                        ]);
                BranchAllocationReceivedDetali::where(
                            'branch_allocation_received_id', $received->id
                        )
                        ->where('part_id',$request->part_id[$key])
                        ->update([
                            'receiving_quantity' => $request->receiving_quantity[$key]
                        ]);
                
                InventoryStock::where(
                    'allocation_id', $received->allocation_id)
                    ->where('part_id', $request->part_id[$key])
                    ->update([
                        'stock_out' => $request->receiving_quantity[$key],
                        'updated_by' => Auth::id(),  
                    ]);
                    
                InventoryStock::where(
                        'branch_allocation_received_id', $received->id)
                        ->where('part_id', $request->part_id[$key])
                        ->update([
                            'stock_in' => $request->receiving_quantity[$key],
                            'updated_by' => Auth::id(),  
                        ]);

            }

            DB::commit();
            return redirect()->route('branch.allocation.received.index')
                    ->with('success', __('Branch allocated received updated successfully.'));
        }catch(\Exception $e) {
            DB::rollback();
            $bug = $e->getMessage();
            return redirect()->back()->with('error', $bug);
        }
    }

    public function destroy($id)
    {
        try {
            $received = BranchAllocationReceived::with('receivedDetails')->findOrFail($id);

            $allocation = Allocation::findOrFail($received->allocation_id);

            $mystore = Store::where('user_id', Auth::user()->id)->first();

            if($allocation != null){
                $allocation->update([
                    'received_quantity' => null,
                    'is_received' => 0,
                    'status' => 0,
                ]);
            }
            
            $requisition = Requisition::findOrFail($allocation->requisition_id);
                
            $qnty = $requisition->issued_quantity;
            $total_issued_quantity = $qnty - $received->allocate_quantity;

            if($requisition != null){
                $requisition->update([
                    'status' => 1,
                    'received_quantity' => $total_issued_quantity,
                ]);
            }
            $rDetails = RequisitionDetails::where('requisition_id', $allocation->requisition_id)->get();

            foreach($rDetails as $key=> $rDetail) {
                $BranchAllocationReceivedDetali = BranchAllocationReceivedDetali::where('branch_allocation_received_id', $received->id)->where('part_id', $rDetail->parts_id)->first();
                $r_received=$rDetail->received_quantity ?? 0;
                $a_received=$BranchAllocationReceivedDetali->receiving_quantity ?? 0;
                $pre_qnty = $r_received - $a_received ;
                $rDetail->update([
                    'received_quantity' => $pre_qnty
                ]);
                $BranchAllocationReceivedDetali->delete();
            }
            
            $allocationDetails = AllocationDetails::where('allocation_id', $allocation->id)->get();

                foreach($allocationDetails as $key=> $allocationDetail) {
                    $allocationDetail->update([
                        'received_quantity' => 0
                    ]);
                }
            $centralinventoryStocks= InventoryStock::where('allocation_id', $received->allocation_id)->get();
            
                foreach($centralinventoryStocks as $key=> $centralinventoryStock) {
                    $centralinventoryStock->update([
                        'stock_out' => 0, 
                    ]);
                }

            $branchinventoryStocks = InventoryStock::where(
                'branch_allocation_received_id', $received->id
            )->get();
            
                foreach($branchinventoryStocks as $key=> $stock) {
                    $stock->delete();
                }

                $received->delete();

            return back()->with('success', 'Branch allocation received deleted successfully.');
            }catch(\Exception $e) {
            $bug = $e->getMessage();
            return redirect()->back()->with('error', $bug);
        }
    }

    // Count total stock in hand
    protected function stockInHand($partsDetails, $allocation)
    {
        $stockCollect = [];
        foreach($partsDetails as $key=>$detail){
            $stockIn = InventoryStock::where('part_id',$detail->parts_id)
                        ->where('store_id',$allocation->to_store_id)
                        ->sum('stock_in');


            $stockOut = InventoryStock::where('part_id',$detail->parts_id)
                        ->where('store_id',$allocation->to_store_id)
                        ->sum('stock_out');

            $stockInHand = $stockIn - $stockOut;
            array_push($stockCollect, $stockInHand);
        }

        return $stockCollect;
    }
}
