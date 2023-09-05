<?php

namespace App\Http\Controllers\Requisition;

use DataTables;
use Carbon\Carbon;
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

class RequisitionAllocationController extends Controller
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
            $mystore='';
            if ($user_role->name == 'Super Admin' || $user_role->name == 'Admin' || $user_role->name =='Store Admin') {

                $allocations=DB::table('allocations')
                    ->join('requisitions', 'allocations.requisition_id', '=', 'requisitions.id')
                    ->join('stores', 'requisitions.from_store_id', '=', 'stores.id')
                    ->select('allocations.id as id','allocations.is_received as is_received','allocations.date as date','allocations.status as status','allocations.allocate_quantity as allocate_quantity','allocations.received_quantity as received_quantity',
                    'allocations.requisition_id as requisition_id','requisitions.requisition_no as requisition_no','requisitions.total_quantity as total_quantity','requisitions.issued_quantity as requisitions',
                    'stores.name as store_name',
                    'requisitions.total_quantity as requisition_quantity')
                    ->where('allocations.is_reallocated', 0 )
                    ->where('allocations.belong_to', 1 )
                    ->where('allocations.deleted_at',null)
                    ->orderBy('allocations.id', 'desc')
                    ->get();
            } else {
                $employee = Employee::where('user_id',Auth::user()->id)->first();
                $mystore=Store::where('id',$employee->store_id )->first();
                if ($mystore != null) {
                    $allocations=DB::table('allocations')
                    ->join('requisitions', 'allocations.requisition_id', '=', 'requisitions.id')
                    ->join('stores', 'requisitions.from_store_id', '=', 'stores.id')
                    ->select('allocations.id as id','allocations.is_received as is_received','allocations.date as date','allocations.status as status','allocations.allocate_quantity as allocate_quantity','allocations.received_quantity as received_quantity',
                    'allocations.requisition_id as requisition_id','requisitions.requisition_no as requisition_no','requisitions.total_quantity as total_quantity','requisitions.issued_quantity as requisitions',
                    'stores.name as store_name',
                    'requisitions.total_quantity as requisition_quantity')
                    ->where('allocations.store_id',$mystore->id)
                    ->where('allocations.is_reallocated',0)
                    ->where('allocations.belong_to',1)
                    ->where('allocations.deleted_at',null)
                    ->orderBy('allocations.id', 'desc')
                    ->get();
                }else{
                    return redirect()->back()->with('error', __('Sorry you dont have the permission.'));
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
                                if ($allocations->is_received != 1) {
                                    return '<div class="table-actions text-center" style="display: flex;">           
                                        <a href=" '.route('central.allocation.show', $allocations->id). ' " title="View">
                                            <i class="ik ik-eye f-16 mr-15 text-green"></i>
                                        </a>
                                        <a href=" '.route('central.allocation.edit', $allocations->id). ' " title="View">
                                        <i class="ik ik-edit f-16 mr-15 text-info"></i>
                                        </a>
                                        <a type="submit" onclick="showDeleteConfirm(' . $allocations->id . ')" title="Delete"><i class="ik ik-trash-2 f-16 text-red"></i></a>
                                    </div>';
                                }else{
                                    return '<div class="table-actions text-center" style="display: flex;">           
                                        <a href=" '.route('central.allocation.show', $allocations->id). ' " title="View">
                                            <i class="ik ik-eye f-16 mr-15 text-green"></i>
                                        </a>
                                        <a href="#" title="Received">
                                        <i class="ik ik-edit f-16 mr-15 text-yellow"></i>
                                        </a>
                                        <a type="#" disabled title="Received"><i class="ik ik-trash-2 f-16 text-yellow"></i></a>
                                    </div>'; 
                                }

                            } elseif (Auth::user()->can('edit') && Auth::user()->can('show')) {
                                if ($allocations->is_received != 1) {
                                    return '<div class="table-actions text-center" style="display: flex;">           
                                        <a href=" '.route('central.allocation.show', $allocations->id). ' " title="View">
                                            <i class="ik ik-eye f-16 mr-15 text-green"></i>
                                        </a>
                                        <a href=" '.route('central.allocation.edit', $allocations->id). ' " title="View">
                                        <i class="ik ik-edit f-16 mr-15 text-info"></i>
                                        </a>
                                    </div>';
                                }else{
                                    return '<div class="table-actions text-center" style="display: flex;">           
                                        <a href=" '.route('central.allocation.show', $allocations->id). ' " title="View">
                                            <i class="ik ik-eye f-16 mr-15 text-green"></i>
                                        </a>
                                        <a href="#" title="Received">
                                        <i class="ik ik-edit f-16 mr-15 text-yellow"></i>
                                        </a>
                                        <a href="#" disabled title="Received"><i class="ik ik-trash-2 f-16 text-yellow"></i></a>
                                    </div>'; 
                                }
                            } elseif (Auth::user()->can('delete')) {
                                if ($allocations->is_received != 1) {
                                    return '<div class="table-actions text-center" style="display: flex;">           
                                        <a type="submit" onclick="showDeleteConfirm(' . $allocations->id . ')" title="Delete"><i class="ik ik-trash-2 f-16 text-red"></i></a>
                                    </div>';
                                }else{
                                    return '<div class="table-actions text-center" style="display: flex;">           
                                        <a href="#" disabled title="Received"><i class="ik ik-trash-2 f-16 text-yellow"></i></a>
                                    </div>'; 
                                }
                            } elseif (Auth::user()->can('show')) {
                                return '<div class="table-actions">
                                            <a href=" '.route('central.allocation.show', $allocations->id). ' " title="View">
                                                <i class="ik ik-eye f-16 mr-15 text-green"></i>
                                            </a>
                                        </div>';
                            } 
                    })
                    ->addIndexColumn()
                    ->rawColumns(['status','action'])
                    ->make(true);
            }
            return view('requisition.allocation.index',compact('allocations'));
        } catch (\Exception $e) {
            $bug = $e->getMessage();
            dd($bug);
            return redirect()->back()->with('error', $bug);
        }
    }
    
    public function requisitationAllocate($id)
    {
        try{
            $requisition = Requisition::find($id);
            $details = RequisitionDetails::where('requisition_id',$requisition->id)->with('part','part_model')->get();
            $rackbinInfo = [];
            $stock_collect = [];
            foreach($details as $key=>$detail){
                $rackbin=RackBinManagement::where('parts_id',$detail->parts_id)->where('store_id',$requisition->store_id)->first();
                $stock_in = InventoryStock::where('part_id',$detail->parts_id)->where('store_id',$requisition->store_id)->sum('stock_in');
                $stock_out = InventoryStock::where('part_id',$detail->parts_id)->where('store_id',$requisition->store_id)->sum('stock_out');
                $stock_in_hand = $stock_in - $stock_out;
                array_push($rackbinInfo, $rackbin);
                array_push($stock_collect,$stock_in_hand);
            }
            return view('requisition.requisition.allocate',compact('requisition','details','rackbinInfo','stock_collect'));
        } catch (\Exception $e) {
            $bug = $e->getMessage();
            return redirect()->back()->with('error', $bug);
        }
    }

    public function requisitationAllocateStore(Request $request)
    {
        $this->validate($request, [
            'store_id' => 'required|numeric',
            'rack_id' => 'required|array',
            'bin_id' => 'required|array',
            'issue_quantity' => 'required|array',
        ]);

        DB::beginTransaction();
        try {
            //store data to allocation
            $requisition = Requisition::find($request->requisition_id);
            $data = $request->all();
            $data['belong_to'] = 1;
            $data['requisition_id'] = $request->requisition_id;
            $data['date'] = $request->allocation_date;
            $data['status'] = 0;
            $data['allocate_quantity'] = array_sum($request->issue_quantity);
            $data['created_by'] = Auth::id();
            $issued_quantity = array_sum($request->issue_quantity);
            $allocation = Allocation::create($data);

            $requisition->update([
                'status'=> 1, // Status 1 = Allocated
                'is_issued'=> 1,
                'allocation_status' => 1,
                'issued_quantity' =>$issued_quantity,
            ]);

            foreach($request->issue_quantity as $key => $quantity){
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
                    //Stock out from central wirehouse
                    InventoryStock::create([
                        'allocation_id'=>$allocation->id,
                        'allocation_details_id'=>$AllocationDetails->id ?? Null,
                        'belong_to' =>  1, //1=Central WareHouse
                        // 'price_management_id' => $priceManagement->id,
                        'store_id' =>  $requisition->store_id,
                        'part_id' => $request->part_id[$key],
                        'stock_out' => $request->issue_quantity[$key],
                        'created_by' => Auth::id(),
                    ]);
            }
            DB::commit();
            return redirect()->route('central.allocation.index')->with('success', __('Allocated successfully.'));
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
            $allocation = Allocation::findOrFail($id);
            $allocation_details = AllocationDetails::where('allocation_id', $allocation->id)
                                ->with('part')->get();

            return view('requisition.allocation.show', compact('allocation', 'allocation_details'));
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
            $allocation = Allocation::findOrFail($id);
            $allocationDetails = AllocationDetails::where('allocation_id', $allocation->id)
                                ->with('part')->get();
            $stock_collect = [];
            foreach($allocationDetails as $key=>$detail){
                $stock_in = InventoryStock::where('part_id',$detail->parts_id)->where('store_id',$allocation->store_id)->sum('stock_in');
                $stock_out = InventoryStock::where('part_id',$detail->parts_id)->where('store_id',$allocation->store_id)->sum('stock_out');
                $stock_in_hand = $stock_in - $stock_out;
                array_push($stock_collect,$stock_in_hand);
            }
            return view('requisition.allocation.edit', compact('allocation', 'allocationDetails', 'stock_collect'));
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
            'issue_quantity' => 'required|array',
        ]);
        
        $allocation = Allocation::findOrFail($id);
        DB::beginTransaction();
        try {
            //store data to allocation
            $requisition = Requisition::findOrFail($allocation->requisition_id);
            $totalIssueQuantity = array_sum($request->issue_quantity);

            $data['date'] = $request->date;
            $data['allocate_quantity'] = $totalIssueQuantity;
            $data['updated_by'] = Auth::id();
            $allocation->update($data);

            $requisition->update([
                'issued_quantity' => $totalIssueQuantity,
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
            return redirect()->route('central.allocation.index')
                    ->with('success', __('Requisition allocation updated successfully.'));
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
            $allocation = Allocation::findOrFail($id);
            //Check if the data exists in Received tables
            $branchAllocationReceived=BranchAllocationReceived::where('allocation_id',$allocation->id)->get();

            if(count($branchAllocationReceived) > 0){
                return response()->json([
                    'success' => False,
                    'message' => "Sorry! Can't Delete. This Allocation is Received Already",
                ]);
            } else {
                $allocation_details = AllocationDetails::where('allocation_id', $allocation->id)->get();
                
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
                    return response()->json([
                    'success' => true,
                    'message' => "Allocation deleted successfully",
                    ]);
                } else {
                    return response()->json([
                        'success' => False,
                        'message' => "Something went wrong",
                    ]);
                }
            }
        } catch (\Exception $e) {
            $bug = $e->getMessage();
            return redirect()->back()->with('error', $bug);
        }
    }
        /**
     * Print the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function print($id)
    {
        try{
            $current_date = Carbon::now();
            // $current_date=$date->toDateTimeString();
            $allocation = Allocation::findOrFail($id);
           
            $allocation_details = [];
            if ($allocation != null) {
                $allocations = AllocationDetails::where('allocation_id', $allocation->id)->with('part')->get();
                
                foreach ($allocations as $key => $value) {
                    $item = [];
                    $price = PriceManagement::where(
                            'part_id', $value->parts_id
                        )->latest('id')->first();
                    $item['id'] = $value->id;
                    $item['code'] = $value->part->code;
                    $item['part_name'] = $value->part->name;
                    $item['part_model'] = $value->part->partModel->name ?? null;
                    $item['issued_quantity'] = $value->issued_quantity;
                    $item['price'] = floatval($price->selling_price_bdt);
                    $item['amount'] = $value->issued_quantity * floatval($price->selling_price_bdt);
                    array_push($allocation_details, $item);
                }
            }
            return view('requisition.allocation.print', compact('allocation', 'allocation_details','current_date'));
        } catch (\Exception $e) {
            $bug = $e->getMessage();
            return redirect()->back()->with('error', $bug);
        }
    }
}
