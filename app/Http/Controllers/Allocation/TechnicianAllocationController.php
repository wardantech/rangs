<?php

namespace App\Http\Controllers\Allocation;

use Auth;
use Illuminate\Http\Request;
use App\Models\Inventory\Rack;
use App\Models\Inventory\Store;
use App\Models\Employee\Employee;
use App\Http\Controllers\Controller;
use App\Models\Requisition\Allocation;
use App\Models\Requisition\Requisition;
use App\Models\Inventory\InventoryStock;
use App\Models\Inventory\PriceManagement;
use App\Models\Requisition\AllocationDetails;
use App\Models\Requisition\RequisitionDetails;

class TechnicianAllocationController extends Controller
{
    public function technicianAllocationIndex()
    {
        try{
            $auth = Auth::user();
            $user_role = $auth->roles->first();
            $mystore='';
            if ($user_role->name == 'Technician') {
                $mystore=Store::where('user_id',Auth::user()->id)->first();
                if ($mystore != null) {
                    $allocations = Allocation::where('to_store_id', $mystore->id)->where('is_reallocated', 0)->where('belong_to',2)->latest()->get();
                }else{
                    return redirect()->back()->with('error', __("Sorry you don't have the permission."));
                }
            } elseif ($user_role->name == 'Admin' || $user_role->name == 'Super Admin' || $user_role->name =='Store Admin') {
                $allocations = Allocation::where('is_reallocated', 0)->where('belong_to', 2)->latest()->get();
            } else {
                $employee = Employee::where('user_id',Auth::user()->id)->first();
                // $mystore = Store::where('outlet_id',$employee->outlet_id)->first();
                $mystore = Store::where('id',$employee->store_id)->first();
                if ($mystore != null) {
                    $allocations = Allocation::where('is_reallocated', 0)->where('belong_to', 2)->latest()->get();
                }else{
                    return redirect()->back()->with('error', __("Sorry you don't have the permission."));
                }
            }
            return view('allocation.technician.index',compact('allocations'));
        } catch (\Exception $e) {
            $bug = $e->getMessage();
            return redirect()->back()->with('error', $bug);
        }
    }

    public function branchAllocationIndex()
    {
        try{
            $auth = Auth::user();
            $user_role = $auth->roles->first();
            if ($user_role->name == 'Admin' || $user_role->name == 'Super Admin' || $user_role->name =='Store Admin') {
                $allocations = Allocation::where('is_reallocated', 0)->where('belong_to', 2)->latest()->get();
            } else {
                $employee=Employee::where('user_id',Auth::user()->id)->first();
                // $mystore=Store::where('outlet_id',$employee->outlet_id)->first();
                $mystore=Store::where('id',$employee->store_id )->first();
                if(empty($mystore)){
                    return redirect()->back()->with('error',"Whoops! You don't have the access"); 
                }
                $allocations = Allocation::where('store_id', $mystore->id)->where('is_reallocated', 0)->where('belong_to', 2)->latest()->get();
            }
            return view('allocation.branch.itechnicians_index',compact('allocations'));
        } catch (\Exception $e) {
            $bug = $e->getMessage();
            return redirect()->back()->with('error', $bug);
        }
    }

    public function showTechnicianAllocation($id)
    {
        try{
            $allocation = Allocation::findOrFail($id);
            $allocation_details = AllocationDetails::where('allocation_id', $allocation->id)->with('part')->get();
            return view('allocation.technician.show', compact('allocation', 'allocation_details'));
        } catch (\Exception $e) {
            $bug = $e->getMessage();
            return redirect()->back()->with('error', $bug);
        }
    }
}
