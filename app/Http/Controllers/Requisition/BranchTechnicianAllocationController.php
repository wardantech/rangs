<?php

namespace App\Http\Controllers\Requisition;

use Illuminate\Http\Request;
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

class BranchTechnicianAllocationController extends Controller
{
    public function requisitions()
    {
        try{
            $auth = Auth::user();
            $user_role = $auth->roles->first();
            $mystore='';
            if ($user_role->name == 'Super Admin' || $user_role->name == 'Admin' || $user_role->name =='Store Admin') {
                $requisitions=Requisition::where('belong_to',3)->latest()->get();
            } else {
                $employee = Employee::where('user_id',Auth::user()->id)->first();
                // $mystore = Store::where('outlet_id',$employee->outlet_id)->first();
                $mystore=Store::where('id',$employee->store_id )->first();
                if ($mystore != null) {
                    $requisitions = Requisition::where('store_id',$mystore->id)->where('belong_to',3)->latest()->get();
                }else{
                    return redirect()->back()->with('error', __("Sorry you don't have the permission."));
                }
            }
            return view('employee.requisition.technician.requsitions',compact('requisitions','mystore'));
        } catch (\Exception $e) {
            $bug = $e->getMessage();
            return redirect()->back()->with('error', $bug);
        }
    }

    public function allocations()
    {
        try{
            $auth = Auth::user();
            $user_role = $auth->roles->first();
            $mystore='';
            if ($user_role->name == 'Super Admin' || $user_role->name == 'Admin' || $user_role->name =='Store Admin') {
                $allocations = Allocation::where('is_reallocated', 0)
                                ->where('belong_to', 2)
                                ->latest()->get();
            } else {
                $employee = Employee::where('user_id',Auth::user()->id)->first();
                // $mystore = Store::where('outlet_id',$employee->outlet_id)->first();
                $mystore=Store::where('id',$employee->store_id )->first();
                if ($mystore != null) {
                    $allocations = Allocation::where('store_id', $mystore->id)
                            ->where('is_reallocated', 0)
                            ->where('belong_to', 2)
                            ->latest()->get();
                }else{
                    return redirect()->back()->with('error', __("Sorry you don't have the permission."));
                }
            }
            return view('employee.requisition.technician.allocations',compact('allocations','mystore'));
        } catch (\Exception $e) {
            $bug = $e->getMessage();
            return redirect()->back()->with('error', $bug);
        }
    }
    public function requisitationAllocate($id)
    {
        try{
            $requisition = Requisition::find($id);
            $details = RequisitionDetails::where('requisition_id',$requisition->id)->with('part')->get();
            $rackbinInfo = [];
            $stock_collect = [];
            foreach($details as $key=>$detail){
                $rackbin=RackBinManagement::where('parts_id',$detail->parts_id)->where('store_id',$requisition->store_id)->first();
                $stock_in = InventoryStock::where('part_id', $detail->parts_id)->where('store_id',$requisition->store_id)->sum('stock_in');
                $stock_out = InventoryStock::where('part_id',$detail->parts_id)->where('store_id',$requisition->store_id)->sum('stock_out');
                $stock_in_hand = $stock_in - $stock_out;
                array_push($rackbinInfo, $rackbin);
                array_push($stock_collect,$stock_in_hand);
            }

            return view('employee.requisition.allocate',compact('requisition','details','stock_collect', 'rackbinInfo'));
        } catch (\Exception $e) {
            $bug = $e->getMessage();
            return redirect()->back()->with('error', $bug);
        }
    }

    public function requisitationAllocateStore(Request $request)
    {
        $this->validate($request, [
            'store_id'       => 'required',
            'rack_id'        => 'required',
            'bin_id'         => 'required',
            'issue_quantity' => 'required',
        ]);

        DB::beginTransaction();
        try {
            //store data to allocation
            $employee=Employee::where('user_id',Auth::user()->id)->first();
            $requisition = Requisition::find($request->requisition_id);
            $data = $request->all();

            $data['belong_to'] = 2;
            $data['requisition_id'] = $request->requisition_id;
            $data['date'] = $request->allocation_date;
            $data['employee_id'] = $employee ? $employee->id : null;
            $data['to_store_id'] = $request->from_store_id;
            $data['status'] = 0;
            $data['created_by'] = Auth::id();
            $data['allocate_quantity'] = array_sum($request->issue_quantity);
            $issued_quantity = array_sum($request->issue_quantity);
            $allocation = Allocation::create($data);

            $requisition->update([
                'status'=> 1,
                'is_issued'=> 1,
                'allocation_status' =>1,
                'issued_quantity' =>$issued_quantity,
            ]);

            foreach($request->issue_quantity as $key => $quantity){
                    // Getting Price Details
                    $priceManagement=PriceManagement::where('part_id',$request->part_id[$key])
                    ->latest('id')->first();
                    
                    if($allocation){
                        $allo_details['allocation_id'] = $allocation->id;
                        $allo_details['rack_id'] = $request->rack_id[$key];
                        $allo_details['bin_id'] = $request->bin_id[$key];
                        $allo_details['parts_id'] = $request->part_id[$key];
                        $allo_details['requisition_quantity'] = $request->required_quantity[$key];
                        $allo_details['issued_quantity'] = $quantity;
                        $AllocationDetails=AllocationDetails::create($allo_details);
                    }

                    RequisitionDetails::where('requisition_id',$request->requisition_id)
                                        ->where('parts_id',$request->part_id[$key])
                                        ->update([
                                            'issued_quantity' => $quantity
                                        ]);

                    //Stock out from Branch
                    InventoryStock::create([
                        'date'=>$request->allocation_date,
                        'allocation_id'=>$allocation->id,
                        'allocation_details_id'=>$AllocationDetails->id ?? Null,
                        'belong_to' =>  2, //2=Branch/Outlet
                        'price_management_id' => $priceManagement->id ?? null,
                        'store_id' =>  $requisition->store_id,
                        'part_id' => $request->part_id[$key],
                        'stock_out' => $request->issue_quantity[$key],
                        'created_by' => Auth::id(),
                    ]);
            }
            DB::commit();
            return redirect()->route('branch.technician-requisitions')->with('success', __('New requisition allocation created successfully.'));
        } catch (\Exception $e) {
            // dd($e);
            DB::rollback();
            $bug = $e->getMessage();
            return redirect()->back()->with('error', $bug);
        }
    }
    
    public function show($id)
    {
        try{
            $allocation = Allocation::findOrFail($id);
            $allocation_details = AllocationDetails::where('allocation_id', $allocation->id)
                                    ->with('part')->get();
            return view('employee.requisition.technician.show', compact('allocation', 'allocation_details'));
        } catch (\Exception $e) {
            $bug = $e->getMessage();
            return redirect()->back()->with('error', $bug);
        }
    }

    public function edit($id)
    {
        try{
            $allocation = Allocation::findOrFail($id);
            $allocationDetails = AllocationDetails::where('allocation_id', $allocation->id)
                                ->with('part')->get();
            $stock_collect = [];
            foreach($allocationDetails as $key=>$detail){
                $stock_in = InventoryStock::where('part_id', $detail->parts_id)->where('store_id', $allocation->store_id)->sum('stock_in');
                $stock_out = InventoryStock::where('part_id', $detail->parts_id)->where('store_id', $allocation->store_id)->sum('stock_out');
                $stock_in_hand = $stock_in - $stock_out;
                array_push($stock_collect,$stock_in_hand);
            }
            return view('employee.requisition.technician.edit', compact('allocation', 'allocationDetails', 'stock_collect'));
        } catch (\Exception $e) {
            $bug = $e->getMessage();
            return redirect()->back()->with('error', $bug);
        }
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'issue_quantity' => 'required|array',
        ]);
        
        $allocation = Allocation::findOrFail($id);

        DB::beginTransaction();
        try {
            $requisition = Requisition::findOrFail($request->requisition_id);
            $issued_quantity = array_sum($request->issue_quantity);

            $data['date'] = $request->date;
            $data['allocate_quantity'] = $issued_quantity;
            $data['updated_by'] = Auth::id();
            $allocation->update($data);

            $requisition->update([
                'issued_quantity' =>$issued_quantity,
            ]);

            foreach($request->issue_quantity as $key => $quantity){
                RequisitionDetails::where('requisition_id',$allocation->requisition_id)
                                        ->where('parts_id',$request->part_id[$key])
                                        ->update([
                                            'issued_quantity' => $quantity
                                        ]);
                AllocationDetails::where('allocation_id', $allocation->id)
                                        ->where('parts_id',$request->part_id[$key])
                                        ->update([
                                            'issued_quantity' => $quantity
                                        ]);
                InventoryStock::where(
                        'allocation_id', $allocation->id)
                        ->where('part_id', $request->part_id[$key])
                        ->update([
                            'stock_out' => $request->issue_quantity[$key],
                            'updated_by' => Auth::id(),  
                        ]);
            }
            
            DB::commit();
            return redirect()->route('branch.technician.allocations')
                    ->with('success', __('Technician allocation updated successfully.'));
        } catch (\Exception $e) {
            DB::rollback();
            $bug = $e->getMessage();
            return redirect()->back()->with('error', $bug);
        }
    }

    public function destroy($id)
    {
        try{
            $allocation = Allocation::findOrFail($id);
            $allocation_details = AllocationDetails::where('allocation_id', $allocation->id)->get();
            $ranchAllocationReceived=BranchAllocationReceived::where('allocation_id', $allocation->id)->get();
            if(count($ranchAllocationReceived) > 0){
                return back()->with('error', "Sorry! Can't Delete. This Allocation is Received Already");
            } else {
                if ($allocation_details != null) {

                    $requisition = Requisition::findOrFail($allocation->requisition_id);
                    $qnty = $requisition->issued_quantity;
                    $total_issued_quantity = $qnty - $allocation->allocate_quantity;
                    $requisition->update([
                        'status' => 0,
                        'allocation_status' => 0,
                        'is_issued' => 0,
                        'issued_quantity' => $total_issued_quantity,
                    ]);

                    $requisitionDetails = RequisitionDetails::where('requisition_id', $allocation->requisition_id)->get();
                    $total_received_quantity = [];
                    foreach ($requisitionDetails as $key=> $value) {
                        $qnty = $value->issued_quantity;
                        array_push($total_received_quantity, $qnty - $value->issued_quantity);

                        $value->update([
                            'issued_quantity' => $total_received_quantity[$key],
                        ]);
                    }

                    foreach ($allocation_details as $key => $value) {
                        $value->delete();
                    };

                    $allocation->delete();
                    return redirect()->back()->with('success', __('Technichian Allocation Deleted successfully.'));
                } else {
                    return redirect()->back()->with('error', 'Whoops! Something Error!');
                }
            }
        } catch (\Exception $e) {
            $bug = $e->getMessage();
            return redirect()->back()->with('error', $bug);
        }
    }

}
