<?php

namespace App\Http\Controllers\Allocation;

use App\Models\Inventory\Store;
use App\Models\Employee\Employee;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\Requisition\Allocation;
use App\Models\Requisition\BranchAllocationReceived;

class BranchReceivedAllocationController extends Controller
{
    public function index()
    {
        try{
            $auth = Auth::user();
            $employee=Employee::where('user_id',Auth::user()->id)->first();
            $user_role = $auth->roles->first();
            // $mystore='';

            // if($user_role->name == 'Branch Store Executive') {
            //     $mystore=Store::where('outlet_id',$employee->outlet_id)->first();
            //     $receives = BranchAllocationReceived::where('store_id', $mystore->id)
            //                 ->where('is_received', 1)
            //                 ->latest()->get();
            // }
            if($user_role->name == 'Admin' || $user_role->name == 'Super Admin' || $user_role->name =='Store Admin') {
                $receives = BranchAllocationReceived::where('is_received', 1)
                            ->latest()->get();
                            dd($receives);
            }else{
                // $mystore=Store::where('outlet_id',$employee->outlet_id)->first();
                $mystore=Store::where('id',$employee->store_id )->first();
                if(empty($mystore)){
                    return redirect()->back()->with('error',"Whoops! You don't have the access"); 
                }
                $receives = BranchAllocationReceived::where('store_id', $mystore->id)
                            ->where('is_received', 1)
                            ->latest()->get();
            }
            // else {
            //     $receives = BranchAllocationReceived::where('is_received', 1)
            //                 ->latest()->get();
            // }

            return view('allocation.branch.received.index', compact('receives'));
        } catch (\Exception $e) {
            $bug = $e->getMessage();
            return redirect()->back()->with('error', $bug);
        }

    }
}
