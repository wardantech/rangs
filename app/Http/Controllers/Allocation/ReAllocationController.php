<?php

namespace App\Http\Controllers\Allocation;

use DB;
use Auth;
use DataTables;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\Inventory\Rack;
use App\Models\Inventory\Store;
use App\Models\Employee\Employee;
use App\Http\Controllers\Controller;
use App\Models\Requisition\Allocation;
use App\Models\Requisition\Requisition;
use App\Models\Inventory\InventoryStock;
use App\Models\Inventory\PriceManagement;
use App\Models\Inventory\RackBinManagement;
use App\Models\Requisition\AllocationDetails;
use App\Models\Requisition\RequisitionDetails;
use function PHPUnit\Framework\returnArgument;
use App\Models\Requisition\BranchAllocationReceived;
use App\Models\Requisition\BranchAllocationReceivedDetali;


class ReAllocationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    // Index For Branch
    public function reAllocatedRequisitionIndex()
    {
        try{
            $auth = Auth::user();
            $user_role = $auth->roles->first();
            $mystore='';
            if ($user_role->name == 'Super Admin' || $user_role->name == 'Admin' || $user_role->name =='Store Admin') {
                $allocations = Allocation::where('is_reallocated', 1)->where('is_received',0)->where('belong_to', 1)->latest()->get();
            } else {
                $employee=Employee::where('user_id',Auth::user()->id)->first();
                // $mystore=Store::where('outlet_id',$employee->outlet_id)->first();
                $mystore=Store::where('id',$employee->store_id )->first();
                if ($mystore != null) {
                    $allocations = Allocation::where('to_store_id', $mystore->id)->where('is_reallocated', 1)->where('is_received',0)->where('belong_to', 1)->latest()->get();
                }else{
                    return redirect()->back()->with('error', __("Sorry you don't have the permission."));
                }

            }
            return view('requisition.re-allocation.branch.re-allocation-outlet_list',compact('allocations','mystore'));
        } catch (\Exception $e) {
            $bug = $e->getMessage();
            return redirect()->back()->with('error', $bug);
        }
    }

    // Index For Central Warehouse
    public function reAllocatedRequisitionIndexForCentral()
    {
        try{
            $auth = Auth::user();
            $user_role = $auth->roles->first();
            $mystore='';
            if ($user_role->name == 'Super Admin' || $user_role->name == 'Admin') {
                $allocations=DB::table('allocations')
                ->join('requisitions', 'allocations.requisition_id', '=', 'requisitions.id')
                ->join('stores', 'requisitions.from_store_id', '=', 'stores.id')
                ->select('allocations.id as id','allocations.is_received as is_received','allocations.date as date','allocations.status as status','allocations.allocate_quantity as allocate_quantity','allocations.received_quantity as received_quantity',
                'allocations.requisition_id as requisition_id','requisitions.requisition_no as requisition_no','requisitions.total_quantity as total_quantity','requisitions.issued_quantity as requisitions',
                'stores.name as store_name',
                'requisitions.total_quantity as requisition_quantity')
                ->where('allocations.is_reallocated',1)
                ->where('allocations.belong_to',1)
                ->where('allocations.deleted_at',null)
                ->orderBy('allocations.id', 'desc')
                ->get();
            } else {
                $employee=Employee::where('user_id',Auth::user()->id)->first();
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
                    ->where('allocations.is_reallocated',1)
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
                    if($allocations->is_received != 1)
                        return '<span class="badge badge-danger">Pending</span>';
                     elseif($allocations->is_received ==1 && $allocations->allocate_quantity == $allocations->received_quantity)
                        return '<span class="badge badge-warning">Full Received</span>';    
                     elseif($allocations->is_received ==1 && $allocations->allocate_quantity > $allocations->received_quantity)
                        return '<span class="badge badge-warning">Partial Received</span>';                                             
                    })
                    ->addColumn('action', function ($allocations) {
                            if (Auth::user()->can('edit') && Auth::user()->can('delete') && Auth::user()->can('show')) {
                                if ($allocations->is_received != 1) {
                                    return '<div class="table-actions text-center" style="display: flex;">           
                                        <a href=" '.route('central.re-allocation.show', $allocations->id). ' " title="View">
                                            <i class="ik ik-eye f-16 mr-15 text-green"></i>
                                        </a>
                                        <a href=" '.route('central.re-allocation.edit', $allocations->id). ' " title="View">
                                        <i class="ik ik-edit f-16 mr-15 text-info"></i>
                                        </a>
                                        <a type="submit" onclick="showDeleteConfirm(' . $allocations->id . ')" title="Delete"><i class="ik ik-trash-2 f-16 text-red"></i></a>
                                    </div>';
                                }else{
                                    return '<div class="table-actions text-center" style="display: flex;">           
                                        <a href=" '.route('central.re-allocation.show', $allocations->id). ' " title="View">
                                            <i class="ik ik-eye f-16 mr-15 text-green"></i>
                                        </a>
                                        <a href="#" title="Received">
                                        <i class="ik ik-edit f-16 mr-15 text-yellow"></i>
                                        </a>
                                        <a href="#" title="Received"><i class="ik ik-trash-2 f-16 text-yellow"></i></a>
                                    </div>';
                                }

                            } elseif (Auth::user()->can('edit') && Auth::user()->can('show')) {
                                if ($allocations->is_received != 1) {
                                    return '<div class="table-actions text-center" style="display: flex;">           
                                        <a href=" '.route('central.re-allocation.show', $allocations->id). ' " title="View">
                                            <i class="ik ik-eye f-16 mr-15 text-green"></i>
                                        </a>
                                        <a href=" '.route('central.re-allocation.edit', $allocations->id). ' " title="View">
                                        <i class="ik ik-edit f-16 mr-15 text-info"></i>
                                        </a>
                                    </div>';
                                }else{
                                    return '<div class="table-actions text-center" style="display: flex;">           
                                        <a href=" '.route('central.re-allocation.show', $allocations->id). ' " title="View">
                                            <i class="ik ik-eye f-16 mr-15 text-green"></i>
                                        </a>
                                        <a href="#" title="Received">
                                        <i class="ik ik-edit f-16 mr-15 text-yellow"></i>
                                        </a>
                                    </div>';
                                }
                            } elseif (Auth::user()->can('delete')) {
                                return '<div class="table-actions">
                                            <a type="submit" onclick="showDeleteConfirm(' . $allocations->id . ')" title="Delete"><i class="ik ik-trash-2 f-16 text-red"></i></a>
                                        </div>';
                            } elseif (Auth::user()->can('show')) {
                                return '<div class="table-actions">
                                            <a href=" '.route('central.re-allocation.show', $allocations->id). ' " title="View">
                                                <i class="ik ik-eye f-16 mr-15 text-green"></i>
                                            </a>
                                        </div>';
                            } 
                    })
                    ->addIndexColumn()
                    ->rawColumns(['status','action'])
                    ->make(true);
            }
            return view('requisition.re-allocation.central.re-allocation-central_list', compact('allocations'));
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
        //
    }


    public function requisitationReAllocate($id)
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
            // dd($details);
            return view('requisition.re-allocation.central.re-allocate',compact('requisition','details','rackbinInfo','stock_collect'));
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
    public function requisitationReAllocateStore(Request $request)
    {

        DB::beginTransaction();
        try {
            //store data to allocation
            $nowInBd = Carbon::now('Asia/Dhaka');
            $requisition = Requisition::find($request->requisition_id);

            $data = $request->all();
            $data['belong_to'] = 1; //1=Central Warehouse
            $data['requisition_id'] = $request->requisition_id;
            $data['date'] = $request->date;
            $data['allocation_date'] = $request->date;
            $data['outlate_id'] = $requisition->outlate_id;
            $data['store_id'] = $requisition->store_id;
            $data['to_store_id'] = $requisition->from_store_id;
            $data['status'] = 1;
            $data['is_reallocated'] = 1;
            $data['created_by'] = Auth::id();
            $data['allocate_quantity'] = array_sum($request->issue_quantity);
            $issued_quantity = array_sum($request->issue_quantity);
            $allocation = Allocation::create($data);

            $qnty=$requisition->issued_quantity;
            $total=$qnty + $issued_quantity;

            $requisition->update([
                'issued_quantity' =>$total,
            ]);

            foreach($request->issue_quantity as $key => $quantity){
                    if($allocation){
                        $allo_details['allocation_id'] = $allocation->id;
                        $allo_details['requisition_detail_id'] = $request->requisition_detail_id[$key];
                        $allo_details['parts_id'] = $request->part_id[$key];
                        $allo_details['rack_id'] = $request->rack_id[$key];
                        $allo_details['bin_id'] = $request->bin_id[$key];
                        $allo_details['requisition_quantity'] = $request->required_quantity[$key];
                        $allo_details['issued_quantity'] = $quantity;
                        $AllocationDetails=AllocationDetails::create($allo_details);
                    }
                    $priceManagement=PriceManagement::where('part_id',$request->part_id[$key])
                    ->latest('id')->first();

                    $requisitionDetail=RequisitionDetails::where('requisition_id',$request->requisition_id)
                                        ->where('parts_id',$request->part_id[$key])->first();

                    $previous_issued_quantity= $requisitionDetail->issued_quantity;
                    $total_issued_quantity=$previous_issued_quantity + $request->issue_quantity[$key];

                    $requisitionDetail->update([
                                'issued_quantity' =>$total_issued_quantity,
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
            return redirect()->route('central.re-allocations')->with('success', __('Re-allocated successfully.'));
        } catch (\Exception $e) {
            DB::rollback();
            $bug = $e->getMessage();
            return redirect()->back()->with('error', $bug);
        }
    }

    //Receive Allocated Items
    public function requisitionOutletReceiveForm($id)
    {
        try{
            $allocation = Allocation::with('requisition')->findOrFail($id);

            $details = AllocationDetails::where('allocation_id', $allocation->id)->with('part')->get();

            $rackbinInfo = [];
            $stock_collect = [];
            foreach($details as $key=>$detail){
                $rackbin=RackBinManagement::where('parts_id',$detail->parts_id)->where('store_id',$allocation->to_store_id)->first();
                $stock_in = InventoryStock::where('part_id',$detail->parts_id)->where('store_id', $allocation->to_store_id)->sum('stock_in');
                $stock_out = InventoryStock::where('part_id',$detail->parts_id)->where('store_id', $allocation->to_store_id)->sum('stock_out');
                $stock_in_hand = $stock_in - $stock_out;
                array_push($rackbinInfo, $rackbin);
                array_push($stock_collect,$stock_in_hand);
            }
            return view('requisition.re-allocation.branch.outlet_receive', compact('allocation', 'details', 'stock_collect','rackbinInfo'));
        } catch (\Exception $e) {
            $bug = $e->getMessage();
            return redirect()->back()->with('error', $bug);
        }
    }

    //Store allocated items
    public function requisitionOutletReceiveStore(Request $request)
    {
        DB::beginTransaction();
        try {
            $received_quantity = array_sum($request->receiving_quantity);
            $allocation = Allocation::find($request->allocation_id);
            

            if($allocation != null){
                $allocation->update([
                    'is_received' => 1,
                    'status' => 1,
                    'received_quantity' => $received_quantity,
                ]);
            }

            $requisition = Requisition::findOrFail($allocation->requisition_id);

            $qnty = $requisition->received_quantity;
            $total = $qnty + $received_quantity;

            if($requisition != null){
                $requisition->update([
                    'status' => 2,
                    'received_quantity' => $total,
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
                    
                    $RequisitionDetails=RequisitionDetails::where('requisition_id',$allocation->requisition_id)
                    ->where('parts_id',$request->part_id[$key])->first();

                    $total_received_requisition_detail=$RequisitionDetails->received_quantity + $request->receiving_quantity[$key];
                    
                    $RequisitionDetails->update([
                        'received_quantity' => $total_received_requisition_detail,
                    ]);
                    
                    //
                    AllocationDetails::where('allocation_id', $allocation->id)
                    ->where('parts_id',$request->part_id[$key])
                    ->update([
                        'received_quantity' => $request->receiving_quantity[$key]
                    ]);
                    
                    $priceManagement=PriceManagement::where(
                        'part_id',$request->part_id[$key]
                    )->latest('id')->first();
                    
                    InventoryStock::where(
                        'allocation_id', $allocation->id
                    )
                    ->where('part_id', $request->part_id[$key])
                    ->update([
                       // 'price_management_id' => $priceManagement->id,
                        'stock_out' => $request->receiving_quantity[$key],
                        'updated_by' => Auth::id(),  
                    ]);

                    //stock out from central wirehouse
                    if($request->receiving_quantity[$key] > 0) {
                        InventoryStock::create([
                            'branch_allocation_received_id' => $received->id,
                            'branch_received_details_id' => $details->id,
                            'belong_to' =>  2, // 2=Branch
                          //  'price_management_id' => $priceManagement->id,
                            'store_id' =>  $requisition->from_store_id,
                            'part_category_id' => $request->part_category_id[$key],
                            'part_id' => $request->part_id[$key],
                            'stock_in' => $request->receiving_quantity[$key],
                            'type' => 2,
                            'created_by' => Auth::id(),
                        ]);
                    }
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
            $allocation_details = AllocationDetails::where('allocation_id', $allocation->id)->with('part')->get();
            return view('requisition.re-allocation.central.show', compact('allocation', 'allocation_details'));
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
            return view('requisition.re-allocation.central.edit', compact('allocation', 'allocationDetails', 'stock_collect'));
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
        $allocation = Allocation::findOrFail($id);
        DB::beginTransaction();
        try {
            //store data to allocation
            $requisition = Requisition::findOrFail($allocation->requisition_id);
            $totalIssueQuantity = array_sum($request->issue_quantity);



            $qnty = $requisition->issued_quantity - $allocation->allocate_quantity;
            
            $total = $qnty + $totalIssueQuantity;
            $requisition->update([
                'issued_quantity' =>$total,
            ]);

            $data['date'] = $request->date;
            $data['allocate_quantity'] = $totalIssueQuantity;
            $data['updated_by'] = Auth::id();
            $allocation->update($data);
            
            foreach($request->issue_quantity as $key => $quantity){
                $allocation_detail=AllocationDetails::where('allocation_id', $allocation->id)
                                        ->where('parts_id',$request->part_id[$key])->first();

                $requisitionDetails= RequisitionDetails::where('requisition_id',$allocation->requisition_id)
                ->where('parts_id',$request->part_id[$key])->first();
    
                $pre_qnty=$requisitionDetails->issued_quantity - $allocation_detail->issued_quantity;
                $total_issued_quantity=$pre_qnty + $request->issue_quantity[$key];
    
                $requisitionDetails->update([
                    'issued_quantity' => $total_issued_quantity,
                ]);

                $allocation_detail->update([
                    'issued_quantity' => $request->issue_quantity[$key]
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
            return redirect()->route('central.re-allocations')->with('success', __('Requisition Re-allocation updated successfully.'));
        } catch (\Exception $e) {
            DB::rollback();
            $bug = $e->getMessage();
            return redirect()->back()->with('error', $bug);
        }
       
    }

    public function destroy($id)
    {

        $allocation = Allocation::findOrFail($id);
        $allocation_details = AllocationDetails::where('allocation_id', $allocation->id)->get();
        try {
            if ($allocation_details != null) {

                $requisition = Requisition::findOrFail($allocation->requisition_id);
                
                $qnty = $requisition->issued_quantity;
                $total_issued_quantity = $qnty - $allocation->allocate_quantity;
                
                $requisition->update([
                    'issued_quantity' => $total_issued_quantity,
                ]);

                $requisitionDetails = RequisitionDetails::where('requisition_id', $allocation->requisition_id)->get();
                $total_received_quantity = [];
                if (!empty($requisitionDetails)) {

                    foreach ($requisitionDetails as $key=> $value) {
                        if (!empty($value)) {
                        $allocation_detail = AllocationDetails::where('allocation_id', $allocation->id)->where('parts_id', $value->parts_id)->first();
                        $r_issued=$value->issued_quantity ?? 0;
                        $a_issued=$allocation_detail->issued_quantity ?? 0;
                        $pre_qnty = $r_issued - $a_issued ;
                            $value->update([
                                'issued_quantity' => $pre_qnty,
                            ]);
                        }
                    }
                }
                foreach ($allocation_details as $key => $value) {
                    $inv=InventoryStock::where('allocation_id', $allocation->id)
                        ->where('part_id', $value->parts_id)
                        ->first();
                    $inv->delete();
                    $value->delete();
                };

                $allocation->delete();
                return response()->json([
                    'success' => true,
                    'message' => "Re-allocation deleted successfully",
                ]);
            }
            }catch (\Exception $e) {
                $bug = $e->getMessage();
                return response()->json([
                    'success' => true,
                    'message' => $bug,
                ]);
        }
    }
}
