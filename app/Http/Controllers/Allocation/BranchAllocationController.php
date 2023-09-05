<?php

namespace App\Http\Controllers\Allocation;

use Auth;
use DataTables;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\Inventory\Rack;
use App\Models\Inventory\Store;
use App\Models\Employee\Employee;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\Requisition\Allocation;
use App\Models\Inventory\InventoryStock;
use App\Models\Requisition\AllocationDetails;

class BranchAllocationController extends Controller
{
    public function branchAllocationIndex()
    {
        try{
            $auth = Auth::user();
            $user_role = $auth->roles->first();
            $mystore='';
            if ($user_role->name == 'Super Admin' || $user_role->name == 'Admin' || $user_role->name =='Store Admin') {
                $allocations=DB::table('allocations')
                ->join('requisitions', 'allocations.requisition_id', '=', 'requisitions.id')
                ->join('stores', 'requisitions.from_store_id', '=', 'stores.id')
                ->select('allocations.id as id','allocations.is_reallocated_received as is_reallocated_received','allocations.is_received as is_received','allocations.date as date','allocations.status as status','allocations.allocate_quantity as allocate_quantity','allocations.received_quantity as received_quantity',
                'allocations.requisition_id as requisition_id','requisitions.requisition_no as requisition_no','requisitions.total_quantity as total_quantity','requisitions.issued_quantity as requisitions',
                'stores.name as store_name',
                'requisitions.total_quantity as requisition_quantity')
                ->where('allocations.is_reallocated',0)
                ->where('allocations.is_received','!=', 1)
                ->where('allocations.belong_to',1)
                ->where('allocations.deleted_at',null)
                ->orderBy('allocations.id', 'desc')
                ->get();
            } else {
                $employee=Employee::where('user_id',Auth::user()->id)->first();
                $mystore=Store::where('id',$employee->store_id )->first();
                if ($mystore != null) {
                    // $allocations = Allocation::where('to_store_id', $mystore->id)->where('is_reallocated', 0)->where('belong_to', 1)->latest()->get();
                    $allocations=DB::table('allocations')
                    ->join('requisitions', 'allocations.requisition_id', '=', 'requisitions.id')
                    ->join('stores', 'requisitions.from_store_id', '=', 'stores.id')
                    ->select('allocations.id as id','allocations.is_reallocated_received as is_reallocated_received','allocations.is_received as is_received','allocations.date as date','allocations.status as status','allocations.allocate_quantity as allocate_quantity','allocations.received_quantity as received_quantity',
                    'allocations.requisition_id as requisition_id','requisitions.requisition_no as requisition_no','requisitions.total_quantity as total_quantity','requisitions.issued_quantity as requisitions',
                    'stores.name as store_name',
                    'requisitions.total_quantity as requisition_quantity')
                    ->where('allocations.to_store_id',$mystore->id)
                    ->where('allocations.is_reallocated',0)
                    ->where('allocations.is_received','!=', 1)
                    ->where('allocations.belong_to',1)
                    ->where('allocations.deleted_at',null)
                    ->orderBy('allocations.id', 'desc')
                    ->get();
                }else{
                    return redirect()->back()->with('error', __("Sorry you don't have the permission."));
                }
            }
            if (request()->ajax()) {
                return DataTables::of($allocations)

                    ->addColumn('date', function ($allocations) {
                        $date=Carbon::parse($allocations->date)->format('m/d/Y');
                        return $date;
                    })

                    ->addColumn('requisition_no', function ($allocations) {
                        $requisition_no='B-RSL'.'-'.$allocations->requisition_id;
                        return $requisition_no;
                    })

                    ->addColumn('sender_store', function ($allocations) {
                        $sender_store=$allocations->store_name ?? null;
                        return $sender_store;
                    })
                    ->addColumn('required', function ($allocations) {
                        $allocationDetails=DB::table('allocation_details')
                        ->join('parts', 'allocation_details.parts_id', '=', 'parts.id')
                        ->select('allocation_details.requisition_quantity as requisition_quantity','allocation_details.issued_quantity as issued_quantity','parts.name as part_name','parts.code as part_code')
                        ->where('allocation_details.allocation_id',$allocations->id)
                        ->where('allocation_details.deleted_at',null)
                        ->get();
                        $res='Not Found';
                        if(!empty($allocationDetails)){
                            $data = [];
                            $part_name = '';
                            foreach($allocationDetails as $detail){
                                $requisition_quantity=intval($detail->requisition_quantity);
                                $issued_quantity=intval($detail->issued_quantity);
                                $res=$requisition_quantity-$issued_quantity;
                                    $data[] =$detail->part_code.'-'. $detail->part_name.' = '.$requisition_quantity .'-'.$issued_quantity.' = '.$res.' Pcs ';
                                    // $data[] =$detail->part_code.'-'. $detail->part_name.' = '.$detail->requisition_quantity .'-'.$detail->issued_quantity.' = '.$detail->requisition_quantity - $detail->issued_quantity.' Pcs ';
                            }
                            foreach ($data as $key => $result) {
                                $total = count($data);
                                if ($total == 1) {
                                    $part_name .= $result;
                                } else {
                                    $part_name .= $result . '; ';
                                }
                            };
                            return rtrim($part_name, ', ');
                        }else{
                            return $res; 
                        }
                    })
                    ->addColumn('received_quantity', function ($allocations) {
                        $received_quantity=$allocations->received_quantity ?? null;
                        return $received_quantity;
                    })
                    ->addColumn('issued_quantity', function ($allocations) {
                        $issued_quantity=$allocations->allocate_quantity; 
                        return $issued_quantity;
                    })
                    
                    ->addColumn('balance', function ($allocations) {
                        $balance=($allocations->requisition_quantity) - ($allocations->allocate_quantity);
                        return $balance;
                    })

                    ->addColumn('status', function ($allocations) {
                    if($allocations->is_received == 0)
                        return '<span class="badge badge-danger">Pending</span>';
                     elseif($allocations->status == 1 && $allocations->total_quantity > $allocations->allocate_quantity)
                        return '<a href="'.route('central.re-allocate', $allocations->requisition_id). '" class="badge badge-warning" title="Re Allocate">
                            Partially Allocated
                            <i class="fa fa-reply f-16 mr-15" aria-hidden="true"></i>
                        </a>';
                     elseif($allocations->is_received == 1 && $allocations->allocate_quantity > $allocations->received_quantity)
                        return '<span class="badge badge-warning">Partially Received</span>';
                     elseif($allocations->is_received == 1 && $allocations->allocate_quantity == $allocations->received_quantity)
                        return '<span class="badge badge-info">Received</span>';
                                             
                    })
                    ->addColumn('action', function ($allocations) {
                            if (Auth::user()->can('edit') && Auth::user()->can('delete') && Auth::user()->can('show')) {
                                if ($allocations->is_reallocated_received == 0 && $allocations->is_received == 0) {
                                    return '<div class="table-actions text-center" style="display: flex;">           
                                                <a href=" '.route('branch.allocation-details', $allocations->id). ' " title="View">
                                                    <i class="ik ik-eye f-16 mr-15 text-green"></i>
                                                </a>
                                                <a href=" '.route('branch.requisition-receive-form', $allocations->id). ' " title="View">
                                                    <i class="fa fa-check-square f-16 mr-15 text-info"></i>
                                                </a>
                                            </div>';
                                }else{
                                    return '<div class="table-actions text-center" style="display: flex;">           
                                                <a href=" '.route('branch.allocation-details', $allocations->id). ' " title="View">
                                                    <i class="ik ik-eye f-16 mr-15 text-green"></i>
                                                </a>
                                                <a href="#" title="Sorry, Already received">
                                                    <i class="fa fa-check-square f-16 mr-15 text-yellow"></i>
                                                </a>
                                            </div>';  
                                }

                            } elseif (Auth::user()->can('edit') && Auth::user()->can('show')) {
                                return '<div class="table-actions" style="display: flex;">
                                            <a href=" '.route('branch.allocation-details', $allocations->id). ' " title="View">
                                                <i class="ik ik-eye f-16 mr-15 text-green"></i>
                                            </a>
                                            <a href=" '.route('branch.requisition-receive-form', $allocations->id). ' " title="View">
                                                <i class="fa fa-check-square f-16 mr-15 text-info"></i>
                                            </a>
                                        </div>';
                            } elseif (Auth::user()->can('show')) {
                                return '<div class="table-actions" style="display: flex;">
                                            <a href=" '.route('branch.allocation-details', $allocations->id). ' " title="View">
                                                <i class="ik ik-eye f-16 mr-15 text-green"></i>
                                            </a>
                                            <a href=" '.route('branch.requisition-receive-form', $allocations->id). ' " title="View">
                                            <i class="fa fa-check-square f-16 mr-15 text-info"></i>
                                        </a>
                                        </div>';
                            } 
                    })
                    ->addIndexColumn()
                    ->rawColumns(['status','action'])
                    ->make(true);
            }

            return view('allocation.branch.index',compact('allocations','mystore'));
        } catch (\Exception $e) {
            $bug = $e->getMessage();
            return redirect()->back()->with('error', $bug);
        }
    }

    public function showBranchAllocation($id)
    {
        try{
            $allocation = Allocation::findOrFail($id);
            $allocation_details = AllocationDetails::where('allocation_id', $allocation->id)->with('part')->get();
            return view('allocation.branch.show', compact('allocation', 'allocation_details'));
        } catch (\Exception $e) {
            $bug = $e->getMessage();
            return redirect()->back()->with('error', $bug);
        }
    }

    public function allocationReceiveForm($id)
    {
        try{
            $allocation = Allocation::find($id);

            $details = AllocationDetails::where('allocation_id', $allocation->id)->with('part')->get();
            $racks=Rack::where('status', 1)->where('store_id',$allocation->requisition->from_store_id)->get();

            $stock_collect = [];
            foreach($details as $key=>$detail){
                $stock_in = InventoryStock::where('part_id',$detail->parts_id)->where('store_id',$allocation->to_store_id)->where('belong_to',2)->sum('stock_in');
                $stock_out = InventoryStock::where('part_id',$detail->parts_id)->where('store_id',$allocation->to_store_id)->where('belong_to',2)->sum('stock_out');
                $stock_in_hand = $stock_in - $stock_out;
                array_push($stock_collect,$stock_in_hand);
            }
            return view('requisition.requisition.outlet_receive', compact('allocation', 'details', 'stock_collect','racks'));
        } catch (\Exception $e) {
            $bug = $e->getMessage();
            return redirect()->back()->with('error', $bug);
        }
    }
}
