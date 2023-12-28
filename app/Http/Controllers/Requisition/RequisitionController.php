<?php

namespace App\Http\Controllers\Requisition;

use DB;
use DataTables;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\Outlet\Outlet;
use App\Models\Inventory\Rack;
use App\Models\Inventory\Parts;
use App\Models\Inventory\Store;
use App\Models\Employee\Employee;
use App\Http\Controllers\Controller;
use App\Models\Inventory\PartsModel;
use Illuminate\Support\Facades\Auth;
use App\Models\Requisition\Allocation;
use App\Models\Requisition\Requisition;
use App\Models\Inventory\InventoryStock;
use App\Models\Inventory\PriceManagement;
use App\Models\Inventory\RackBinManagement;
use App\Models\Requisition\AllocationDetails;
use App\Models\Requisition\RequisitionDetails;

class RequisitionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try{
            $requisitions=Requisition::where('status',1)->latest()->get();
            $parts=Parts::where('status', 1)->get();
            $partsModels=PartsModel::where('status', 1)->get();
            $outlates = Outlet::where('status', 1)->latest()->get();
            return view('requisition.requisition.index', compact('requisitions' ,'parts', 'partsModels','outlates'));
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

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
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
        //
    }

    public  function outletRequisitionList()
    {
        try{
            $auth = Auth::user();
            $user_role = $auth->roles->first();
            $mystore='';
            if ($user_role->name == 'Super Admin' || $user_role->name == 'Admin' || $user_role->name == 'Store Admin') {
                $requisitions=Requisition::where('status',0)->where('belong_to',2)->latest()->get();
                $status = $this->centralStatus();
            } else {
                $employee=Employee::where('user_id',Auth::user()->id)->first();
                $mystore=Store::where('id',$employee->store_id )->first();
                if ($mystore != null) {
                    $requisitions=Requisition::where('status',0)->where('from_store_id',$mystore->id)
                    ->where('belong_to',2)->latest()->get();
                    $status = $this->branchStatus($mystore->id);
                }else{
                    return redirect()->back()->with('error', __('Sorry you dont have the permission.'));
                }
            }
            if (request()->ajax()) {
                return DataTables::of($requisitions)

                    ->addColumn('date', function ($requisitions) {
                        $date=$requisitions->date->format('m/d/Y');
                        return $date;
                    })

                    ->addColumn('requisition_no', function ($requisitions) {
                        $requisition_no='B-RSL'.'-'.$requisitions->id;
                        return $requisition_no;
                    })

                    ->addColumn('sender_store', function ($requisitions) {
                        $sender_store=$requisitions->senderStore->name;
                        return $sender_store;
                    })
                    ->addColumn('parts_name', function ($requisitions) {
                        $requisitionDetails = RequisitionDetails::where('requisition_id', $requisitions->id)->get();
                        $res='Not Found';
                        if(!empty($requisitionDetails)){
                            $data = [];
                            $part_name = '';
                            foreach($requisitionDetails as $detail){
                                    $data[] =$detail->part->code.'-'. $detail->part->name.' = '.$detail->required_quantity .' Pcs ';
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
                    ->addColumn('total_quantity', function ($requisitions) {
                        $total_quantity=$requisitions->total_quantity;
                        return $total_quantity;
                    })
                    ->addColumn('issued_quantity', function ($requisitions) {
                        $issued_quantity=$requisitions->issued_quantity; 
                        return $issued_quantity;
                    })
                    
                    ->addColumn('balance', function ($requisitions) {
                        $balance=($requisitions->total_quantity) - ($requisitions->issued_quantity);
                        return $balance;
                    })

                    ->addColumn('status', function ($requisitions) {

                        if ($requisitions->status == 0 && $requisitions->is_declined == 1){
                            return '<span class="badge badge-lime">Rejected</span>';
                        }                       
                        elseif( $requisitions->status == 0){
                            return '<span class="badge badge-orange">Pending</span>';
                        }
                        elseif($requisitions->status == 1 && $requisitions->total_quantity == $requisitions->issued_quantity)
                        {
                            return '<span class="badge badge-yellow">Created</span>';
                        }
                        elseif($requisitions->status == 1 && $requisitions->total_quantity > $requisitions->issue_quantity)
                        {
                            return '<span class="badge badge-info">Job Completed</span>';
                        }
                        elseif($requisitions->status == 2 && $requisitions->issued_quantity > $requisitions->received_quantity)
                        {
                            return '<span class="badge badge-success">Job Started</span>';
                        }
                        elseif($requisitions->status == 2 && $requisitions->total_quantity > $requisitions->issued_quantity)
                        {
                            return '<span class="badge badge-success">Accepted</span>';
                        }
                        elseif($requisitions->status == 2 && $requisitions->total_quantity == $requisitions->issued_quantity)
                        {
                            return '<span class="badge badge-danger">Rejected</span>';
                        }
                        
                    })
                    ->addColumn('action', function ($requisitions) {
                            if (Auth::user()->can('edit') && Auth::user()->can('delete') && Auth::user()->can('show')) {
                                return '<div class="table-actions text-center" style="display: flex;">           
                                            <a href=" '.route('branch.requisitions-details', $requisitions->id). ' " title="View">
                                                <i class="ik ik-eye f-16 mr-15 text-green"></i>
                                            </a>
                                            <a href=" '.route('branch.requisition.edit', $requisitions->id). ' " title="View">
                                            <i class="ik ik-edit f-16 mr-15 text-info"></i>
                                            </a>
                                            <a type="submit" onclick="showDeleteConfirm(' . $requisitions->id . ')" title="Delete"><i class="ik ik-trash-2 f-16 text-red"></i></a>
                                        </div>';
                            } elseif (Auth::user()->can('edit') && Auth::user()->can('show')) {
                                return '<div class="table-actions" style="display: flex;">
                                            <a href=" '.route('central.requisitions.show', $requisitions->id). ' " title="View">
                                                <i class="ik ik-eye f-16 mr-15 text-green"></i>
                                            </a>
                                        </div>';
                            } elseif (Auth::user()->can('delete')) {
                                return '<div class="table-actions">
                                <a type="submit" onclick="showDeleteConfirm(' . $requisitions->id . ')" title="Delete"><i class="ik ik-trash-2 f-16 text-red"></i></a>
                                        </div>';
                            } elseif (Auth::user()->can('show')) {
                                return '<div class="table-actions">
                                            <a href=" '.route('central.requisitions.show', $requisitions->id). ' " title="View">
                                                <i class="ik ik-eye f-16 mr-15 text-green"></i>
                                            </a>
                                        </div>';
                            } 
                    })
                    ->addIndexColumn()
                    ->rawColumns(['ticket_sl','status','job_pending_remark','action'])
                    ->make(true);
            }
            return view('requisition.requisition.outlet_list',compact('requisitions','mystore','status'));
        } catch (\Exception $e) {
            $bug = $e->getMessage();
            return redirect()->back()->with('error', $bug);
        }
    }

    public  function outletRequisitionCreate()
    {
        try{
            $auth = Auth::user();
            $user_role = $auth->roles->first();
            if ($user_role->name == 'Super Admin' || $user_role->name == 'Admin' || $user_role->name == 'Store Admin') {
                $mystore='';
            } else {
                $employee=Employee::where('user_id',Auth::user()->id)->first();
                // $mystore=Store::where('outlet_id',$employee->outlet_id)->first();
                $mystore=Store::where('id',$employee->store_id )->first();
            }

            $outlates =Outlet::where('status', 1)->orderBy('name')->get();
            $stores = Store::where('status', 1)->latest()->get();
            $central_stores = Store::where('user_id',null)->where('name', 'LIKE', 'Central Warehouse')->first();
            $requistion=Requisition::where('belong_to',2)->latest('id')->first();
            if(!empty($requistion)){
                $trim=trim($requistion->requisition_no,"B-RSL-");
                $sl=$trim + 1;
                $sl_number="B-RSL-".$sl;
            }else{
                $sl_number="B-RSL-"."1";
            }
            return view('requisition.requisition.outlet_create',compact('outlates','stores','sl_number','mystore','user_role','central_stores'));
        } catch (\Exception $e) {
            $bug = $e->getMessage();
            return redirect()->back()->with('error', $bug);
        }
    }

    public function outletRequisitionStore(Request $request)
    {
        $this->validate($request, [
            // 'requisition_no' => 'required|unique:requisitions,requisition_no,NULL,id,deleted_at,NULL',
            'date' => 'required',
            'from_store_id' => 'nullable|numeric',
            'store_id' => 'required|numeric',
            'parts_id' => 'nullable|numeric',
            'stock_in_hand' => 'nullable|array',
            'model_id' => 'nullable|array',
            'required_quantity' => 'required|nullable|array',
            'part_id' => 'required|nullable|array',
        ]);

        $total_quantity = array_sum($request->required_quantity);
        try {
            
            $sl_number = $this->generateUniqueId();

            $requisition = Requisition::create([
                'store_id' => $request->store_id,
                'from_store_id' => $request->from_store_id,
                'belong_to' => 2, // 2=Branch
                'date' => $request->date,
                'requisition_no' => $sl_number,
                'total_quantity' => $total_quantity,
                'created_by' => Auth::id(),
            ]);
            if($requisition){
                foreach($request->part_id as $key => $id){
                    if($id != null &&  $id > 0){

                        $details['requisition_id'] = $requisition->id;
                        $details['parts_id'] = $id;
                        $details['stock_in_hand'] = $request->stock_in_hand[$key];
                        $details['required_quantity'] = $request->required_quantity[$key];

                        RequisitionDetails::create($details);
                    }
                }
            }
            return redirect()->route('branch.requisitions')
                    ->with('success', 'B-RSL-'.$requisition->id.'-'.'Requisition Created successfully.');
        } catch (\Exception $e) {
            $bug = $e->getMessage();
            return redirect()->back()->with('error', $bug);
        }
    }

    public function outletRequisitionShow($id)
    {

        try{
            $requisition = Requisition::with([
                'requisitionDetails',
                'senderStore',
                'partsModel',
                'employee',
                'store',
                'parts',
                'user',
                'job'
            ])->where('id', $id)->first();
            return view('requisition.requisition.show', compact('requisition'));
        } catch (\Exception $e) {
            $bug = $e->getMessage();
            return redirect()->back()->with('error', $bug);
        }
    }

    public function outletRequisitionDestroy($id){
        try {
            $requisition = Requisition::find($id);
            //Check if the data exists in allocation tables
            $allocation=Allocation::where('requisition_id',$requisition->id)->get();
            if(count($allocation) > 0){
                return response()->json([
                    'success' => false,
                    'message' => "Sorry! Can't Delete. This Requisition is Allocated Already",
                ]);
            }else{
                $requisition_details = RequisitionDetails::where('requisition_id',$requisition->id)->get();
                if( count($requisition_details) < 0 ){
                    foreach ($requisition_details as $key => $value) {
                        $value->delete();
                    };
                }
                $requisition->delete();
                return response()->json([
                    'success' => true,
                    'message' => "Requisition deleted successfully.",
                ]);
            }
        } catch (\Exception $e) {
            $bug = $e->getMessage();
            return redirect()->back()->with('error', $bug);
        }
    }

    public function centralRequisitionList()
    {
        try{
            $auth = Auth::user();
            $user_role = $auth->roles->first();
            $mystore='';


            if (request()->ajax()) {

                if ($user_role->name == 'Super Admin' || $user_role->name == 'Admin') {
                    $requisitions=Requisition::where('status',0)->where('is_declined',0)->where('belong_to',2)->latest()->get();
                    // Rejected/Declined Requisitions removed from all requisitions need to add another route for rejected requisitions
                    $status = $this->centralStatus();
                } else {
                    $employee=Employee::where('user_id',Auth::user()->id)->first();
                    $mystore=Store::where('id',$employee->store_id )->first();
                    if ($mystore != null) {
                        $requisitions=Requisition::where('status',0)->where('is_declined',0)->where('store_id',$mystore->id)
                        ->where('belong_to',2)->latest()->get();
                        $status = $this->branchStatus($mystore->id);
                    }else{
                        return redirect()->back()->with('error', __('Sorry you dont have the permission.'));
                    }
                }

                return DataTables::of($requisitions)

                    ->addColumn('date', function ($requisitions) {
                        $date=$requisitions->date->format('m/d/Y');
                        return $date;
                    })

                    ->addColumn('requisition_no', function ($requisitions) {
                        $requisition_no='B-RSL'.'-'.$requisitions->id;
                        return $requisition_no;
                    })

                    ->addColumn('sender_store', function ($requisitions) {
                        $sender_store=$requisitions->senderStore->name;
                        return $sender_store;
                    })
                    ->addColumn('parts_name', function ($requisitions) {
                        $requisitionDetails = RequisitionDetails::where('requisition_id', $requisitions->id)->get();
                        $res='Not Found';
                        if(!empty($requisitionDetails)){
                            $data = [];
                            $part_name = '';
                            foreach($requisitionDetails as $detail){
                                $data[] =$detail->part->code.'-'. $detail->part->name.' = '.$detail->required_quantity .' Pcs ';
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
                    ->addColumn('total_quantity', function ($requisitions) {
                        $total_quantity=$requisitions->total_quantity;
                        return $total_quantity;
                    })
                    ->addColumn('issued_quantity', function ($requisitions) {
                        $issued_quantity=$requisitions->issued_quantity; 
                        return $issued_quantity;
                    })
                    
                    ->addColumn('balance', function ($requisitions) {
                        $balance=($requisitions->total_quantity) - ($requisitions->issued_quantity);
                        return $balance;
                    })

                    ->addColumn('status', function ($requisitions) {

                        if ($requisitions->status == 0 && $requisitions->is_declined == 1){
                            return '<span class="badge badge-lime">Rejected</span>';
                        }                       
                        elseif( $requisitions->status == 0){
                            return '<span class="badge badge-orange">Pending</span>';
                        }
                        elseif($requisitions->status == 1 && $requisitions->total_quantity == $requisitions->issued_quantity)
                        {
                            return '<span class="badge badge-yellow">Created</span>';
                        }
                        elseif($requisitions->status == 1 && $requisitions->total_quantity > $requisitions->issue_quantity)
                        {
                            return '<span class="badge badge-info">Job Completed</span>';
                        }
                        elseif($requisitions->status == 2 && $requisitions->issued_quantity > $requisitions->received_quantity)
                        {
                            return '<span class="badge badge-success">Job Started</span>';
                        }
                        elseif($requisitions->status == 2 && $requisitions->total_quantity > $requisitions->issued_quantity)
                        {
                            return '<span class="badge badge-success">Accepted</span>';
                        }
                        elseif($requisitions->status == 2 && $requisitions->total_quantity == $requisitions->issued_quantity)
                        {
                            return '<span class="badge badge-danger">Rejected</span>';
                        }
                        
                    })
                    ->addColumn('action', function ($requisitions) {
                            if (Auth::user()->can('edit') && Auth::user()->can('delete') && Auth::user()->can('show')) {
                                if($requisitions->is_declined != 1){
                                    return '<div class="table-actions" style="display: flex;">
                                        <a href=" '.route('central.requisitions.show', $requisitions->id). ' " title="View">
                                            <i class="ik ik-eye f-16 mr-15 text-green"></i>
                                        </a>
                                        <a href=" '.route('central.requisitations.allocate', $requisitions->id). ' " title="View">
                                            <i class="fa fa-reply f-16 mr-15" title="Allocate"></i>
                                        </a>
                                        <a href="'.route('central.requisitions.decline', $requisitions->id).'" title="Reject"><i class="fa fa-times text-red" aria-hidden="true"></i></a>
                                    </div>';
                                }else{
                                    return '<div class="table-actions" style="display: flex;">
                                        <a href=" '.route('central.requisitions.show', $requisitions->id). ' " title="View">
                                            <i class="ik ik-eye f-16 mr-15 text-green"></i>
                                        </a>
                                        <a href="#" title="View">
                                            <i class="fa fa-reply f-16 mr-15" text-yellow" title="Rejected"></i>
                                        </a>
                                        <a href="#" title="Rejected"><i class="fa fa-times text-yellow" aria-hidden="true" disabled></i></a>
                                    </div>';
                                }
                            } elseif (Auth::user()->can('edit') && Auth::user()->can('show')) {
                                if($requisitions->is_declined != 1){
                                    return '<div class="table-actions" style="display: flex;">
                                        <a href=" '.route('central.requisitions.show', $requisitions->id). ' " title="View">
                                            <i class="ik ik-eye f-16 mr-15 text-green"></i>
                                        </a>
                                        <a href=" '.route('central.requisitations.allocate', $requisitions->id). ' " title="View">
                                            <i class="fa fa-reply f-16 mr-15" title="Allocate"></i>
                                        </a>
                                        <a href="'.route('central.requisitions.decline', $requisitions->id).'" title="Reject"><i class="fa fa-times text-red" aria-hidden="true"></i></a>
                                    </div>';
                                }else{
                                    return '<div class="table-actions" style="display: flex;">
                                        <a href=" '.route('central.requisitions.show', $requisitions->id). ' " title="View">
                                            <i class="ik ik-eye f-16 mr-15 text-green"></i>
                                        </a>
                                        <a href="#" title="View">
                                            <i class="fa fa-reply f-16 mr-15" text-yellow" title="Rejected"></i>
                                        </a>
                                        <a href="#" title="Rejected"><i class="fa fa-times text-yellow" aria-hidden="true" disabled></i></a>
                                    </div>';
                                }

                            } elseif (Auth::user()->can('delete')) {
                                return '<div class="table-actions">
                                            <a href="'.route('central.requisitions.decline', $requisitions->id).'" title="Reject"><i class="fa fa-times text-red" aria-hidden="true"></i></a>
                                        </div>';
                            } elseif (Auth::user()->can('show')) {
                                return '<div class="table-actions">
                                            <a href=" '.route('central.requisitions.show', $requisitions->id). ' " title="View">
                                                <i class="ik ik-eye f-16 mr-15 text-green"></i>
                                            </a>
                                        </div>';
                            } 
                    })
                    ->addIndexColumn()
                    ->rawColumns(['ticket_sl','status','job_pending_remark','action'])
                    ->make(true);
            }
            return view('requisition.requisition.central_list');
        } catch (\Exception $e) {
            $bug = $e->getMessage();
            return redirect()->back()->with('error', $bug);
        }
    }

    //Item Wise Requisition
    public function centralRequisitionItemList()
    {
        try{
            $auth = Auth::user();
            $user_role = $auth->roles->first();
            $mystore='';

            if ($user_role->name == 'Super Admin' || $user_role->name == 'Admin') {
                $requisitionItems = RequisitionDetails::with('requisition','part')
                ->whereHas('requisition', function ($query) {
                    $query->where('status', 0)
                        ->where('is_declined', 0)
                        ->where('belong_to', 2);
                })
                ->latest()
                ->get();
                $status = $this->centralStatus();
            } else {
                $employee=Employee::where('user_id',Auth::user()->id)->first();
                $mystore=Store::where('id',$employee->store_id )->first();
                if ($mystore != null) {
                    $requisitionItems = RequisitionDetails::with('requisition','part')
                    ->whereHas('requisition', function ($query) use ($mystore){
                        $query->where('status', 0)
                            ->where('is_declined', 0)
                            ->where('belong_to', 2)
                            ->where('store_id', $mystore->id);
                    })
                    ->latest()
                    ->get();
                }else{
                    return redirect()->back()->with('error', __('Sorry you dont have the permission.'));
                }
            }
            if (request()->ajax()) {
                return DataTables::of($requisitionItems)

                    ->addColumn('date', function ($requisitionItem) {
                        $date=$requisitionItem->requisition->date->format('m/d/Y');
                        return $date;
                    })

                    ->addColumn('requisition_no', function ($requisitionItem) {
                        $requisition_no='B-RSL'.'-'.$requisitionItem->requisition->id;
                        return $requisition_no;
                    })

                    ->addColumn('sender_store', function ($requisitionItem) {
                        $sender_store=$requisitionItem->requisition->senderStore->name;
                        return $sender_store;
                    })
                    ->addColumn('parts_code', function ($requisitionItem) {
                        $parts_code = $requisitionItem->part->code;
                            return $parts_code; 
                    })
                    ->addColumn('parts_name', function ($requisitionItem) {
                        $parts_name = $requisitionItem->part->name;
                            return $parts_name; 
                    })
                    ->addColumn('parts_model', function ($requisitionItem) {
                        $parts_model = $requisitionItem->part->partModel->name;
                            return $parts_model; 
                    })
                    ->addColumn('total_quantity', function ($requisitionItem) {
                        $total_quantity=$requisitionItem->required_quantity;
                        return $total_quantity;
                    })
                    ->addColumn('issued_quantity', function ($requisitionItem) {
                        $issued_quantity=$requisitionItem->issued_quantity; 
                        return $issued_quantity;
                    })
                    
                    ->addColumn('balance', function ($requisitionItem) {
                        $balance=($requisitionItem->required_quantity) - ($requisitionItem->issued_quantity);
                        return $balance;
                    })
                    ->addIndexColumn()
                    ->rawColumns(['requisition_no','sender_store','parts_code'])
                    ->make(true);
            }
            return view('requisition.requisition.central_requisition_item_list');
        } catch (\Exception $e) {
            $bug = $e->getMessage();
            return redirect()->back()->with('error', $bug);
        }
    }

    public function outletRequisitionEdit($id)
    {
        try{
            $auth = Auth::user();
            $user_role = $auth->roles->first();
            if ($user_role->name == 'Super Admin' || $user_role->name == 'Admin') {
                $mystore='';
            } else {
                $employee=Employee::where('user_id',Auth::user()->id)->first();
                // $mystore=Store::where('outlet_id',$employee->outlet_id)->first();
                $mystore=Store::where('id',$employee->store_id )->first();
            }

            $outlates = Outlet::where('status', 1)->orderBy('name')->get();
            $stores = Store::where('status', 1)->latest()->get();
            $parts = Parts::where('status', 1)->orderBy('name')->get();
            $requistion = Requisition::where('belong_to',2)->find($id);
            $requisitionDetails = RequisitionDetails::where('requisition_id', $id)->get();

            $partsId = [];
            foreach($requisitionDetails as $requisitionDetail)
            {
                $partsId[] = $requisitionDetail->parts_id;
            }

            return view('requisition.requisition.outlet_edit', compact(
                'user_role',
                'mystore',
                'requistion',
                'outlates',
                'stores',
                'partsId',
                'parts',
            ));
        } catch (\Exception $e) {
            $bug = $e->getMessage();
            return redirect()->back()->with('error', $bug);
        }
    }

    public function centralRequisitionShow($id)
    {
        try{
            $requisition = Requisition::with([
                'requisitionDetails',
                'senderStore',
                'partsModel',
                'employee',
                'store',
                'parts',
                'user',
                'job'
            ])->where('id', $id)->first();
            return view('requisition.requisition.show', compact('requisition'));
        } catch (\Exception $e) {
            $bug = $e->getMessage();
            return redirect()->back()->with('error', $bug);
        }
    }

    

    public function requisitationDetaails(Request $request){
        $details = RequisitionDetails::with('part')->where('requisition_id',$request->id)->get();
        return response()->json([
            'detail'          => $details
        ]);
    }
    
    public function requisitationDecline($id){
        try {
            $requisition = Requisition::find($id);
            if($requisition != null){
                $requisition->update([
                    'is_declined' => 1,
                ]);
            }
            return redirect()->back()->with('success', __('Requisition decline successfully.'));
        }catch (\Exception $e) {
            $bug = $e->getMessage();
            return redirect()->back()->with('error', $bug);
        }
    }

    public function outletRequisitionUpdate(Request $request, $id)
    {
        $this->validate($request, [
            'requisition_no' => 'required|unique:requisitions,requisition_no,' . $id,
            'date' => 'required',
            'from_store_id' => 'nullable|numeric',
            'store_id' => 'required|numeric',
            'parts_id' => 'nullable|numeric',
            // 'parts_model_id' => 'nullable',
            'stock_in_hand' => 'nullable|array',
            'model_id' => 'nullable|array',
            'required_quantity' => 'required|nullable|array',
            'part_id' => 'required|nullable|array',
        ]);

        $requisition = Requisition::find($id);

        $total_quantity = array_sum($request->required_quantity);
        try {
            $requisition->update([
                'store_id' => $request->store_id,
                'from_store_id' => $request->from_store_id,
                'belong_to' => 2, // 2=Branch
                'date' => $request->date,
                'requisition_no' => $request->requisition_no,
                'total_quantity' => $total_quantity,
                'created_by' => Auth::id(),
            ]);

            if($requisition){
                foreach($request->part_id as $key => $id){
                    if($id != null &&  $id > 0){

                        $partId = $request->part_id;
                        $old_parts_id = [];

                        $previous_parts_id = RequisitionDetails::where('requisition_id', $requisition->id)->get();
                        foreach($previous_parts_id as $key=>$parts_id){
                            $id = $parts_id->parts_id;
                            array_push($old_parts_id, $id);
                        }

                        // dd($previous_parts_id);
                        foreach($partId as $key => $id){
                            $data['parts_id'] = $id;
                            $data['requisition_id'] = $requisition->id;
                            $data['stock_in_hand'] = $request->stock_in_hand[$key];
                            $data['required_quantity'] = $request->required_quantity[$key];

                            if($old_parts_id != null ){
                                if(in_array($id,$old_parts_id)){
                                    $details = RequisitionDetails::where('requisition_id', $requisition->id)
                                                                ->where('parts_id',$id)->first();
                                    $details->update($data);
                                }else{
                                    //dd('create');
                                    RequisitionDetails::create($data);
                                }
                            }else{
                                RequisitionDetails::create($data);
                            }

                        }

                        $previous = RequisitionDetails::where('requisition_id', $requisition->id)->get();
                        foreach($previous as $key=>$parts){
                            if(!in_array($parts->parts_id,$partId)){
                                RequisitionDetails::where('requisition_id',$requisition->id)
                                                        ->where('parts_id',$parts->parts_id)->delete();
                            }
                        }

                    }
                }
            }
            return redirect('branch/requisitions')->with('success', __('Requisition updated successfully.'));
        } catch (\Exception $e) {
            $bug = $e->getMessage();
            return redirect()->back()->with('error', $bug);
        }
    }

    protected function generateUniqueId()
    {
        do {
            $requistion = Requisition::where('belong_to',2)->latest('id')->first();
       
            if(!$requistion) {
                return "B-RSL-1";
            }

            $string = preg_replace("/[^0-9\.]/", '', $requistion->requisition_no);
            
            $slNumber = 'B-RSL-' . sprintf('%01d', $string+1);

        } while (Requisition::where('belong_to',2)->where('requisition_no', '==', $slNumber)->first());

        return $slNumber;
    }

    protected function centralStatus()
    {
        return DB::table('requisitions')
        ->selectRaw("count(case when status = 0 and is_issued = 0 and belong_to = 2 and deleted_at IS NULL then 1 end) as pending")
        ->selectRaw("count(case when status = 1 and is_issued = 1 and allocation_status = 1 and belong_to = 2 and deleted_at IS NULL then 1 end) as allocated")
        ->selectRaw("count(case when status = 2 and is_issued = 1 and belong_to = 2 and deleted_at IS NULL then 1 end) as received")
        ->selectRaw("count(case when status = 0 and is_declined = 1 and belong_to = 2 and deleted_at IS NULL then 1 end) as declined")
        ->selectRaw("count(case when belong_to = 2 and deleted_at IS NULL then 1 end) as total")
        ->first();
    }
    protected function branchStatus($store_id)
    {
        return DB::table('requisitions')
        ->selectRaw("count(case when status = 0 and is_issued = 0 and belong_to = 2 and from_store_id = $store_id and deleted_at IS NULL then 1 end) as pending")
        ->selectRaw("count(case when status = 1 and is_issued = 1 and allocation_status = 1 and belong_to = 2 and from_store_id = $store_id and deleted_at IS NULL then 1 end) as allocated")
        ->selectRaw("count(case when status = 2 and is_issued = 1 and belong_to = 2 and from_store_id = $store_id and deleted_at IS NULL then 1 end) as received")
        ->selectRaw("count(case when status = 0 and is_declined = 1 and belong_to = 2 and from_store_id = $store_id and deleted_at IS NULL then 1 end) as declined")
        ->selectRaw("count(case when belong_to = 2 and from_store_id = $store_id and deleted_at IS NULL then 1 end) as total")
        ->first();
    }
}
