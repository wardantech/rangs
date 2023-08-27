<?php

namespace App\Http\Controllers\PO;

use Session;
use Redirect;
use Validator;
use Illuminate\Http\Request;
use App\Models\Outlet\Outlet;
use App\Models\Inventory\Parts;
use App\Models\Inventory\Store;
use App\Models\PO\PurchaseOrder;
use Yajra\DataTables\DataTables;
use App\Models\Employee\Employee;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\Inventory\PartsModel;
use Illuminate\Support\Facades\Auth;
use App\Models\PO\PurchaseOrderDetails;
use App\Models\Inventory\InventoryStock;

class PurchaseOrderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try{
            $purchaseOrders = PurchaseOrder::where('belong_to',1)->orderBy('id', 'desc');
            if (request()->ajax()) {
                return DataTables::of($purchaseOrders)

                    ->addColumn('po_date', function ($purchaseOrders) {
                        return $purchaseOrders->date->format('m/d/Y');
                    })

                    ->addColumn('status', function ($purchaseOrders) {
                        if($purchaseOrders->status == 1){
                            return '<span class="badge badge-danger">Pending</span>';
                        }elseif($purchaseOrders->status == 2){
                             return '<span class="badge badge-success">Issued</span>';
                        }elseif($purchaseOrders->status == 3){
                            return '<span class="badge badge-info">Received</span>';
                        }elseif($purchaseOrders->status == 4){
                            return '<span class="badge badge-warning">Decline</span>';
                        }
                    })

                    ->addColumn('action', function ($purchaseOrders) {
                        if (Auth::user()->can('edit') && Auth::user()->can('delete') && Auth::user()->can('show')) {
                            return '<div class="table-actions text-center" style="display: flex;">
                                            <a href="' . route('purchase.requisitions.show', $purchaseOrders->id) . '" title="Show"><i class="ik ik-eye f-16 mr-15 text-blue"></i></a>
                                            <a href="' . route('purchase.requisitions.edit', $purchaseOrders->id) . '" title="Edit"><i class="ik ik-edit-2 f-16 mr-15 text-green"></i></a>
                                            <a type="submit" onclick="showDeleteConfirm(' . $purchaseOrders->id . ')" title="Delete"><i class="ik ik-trash-2 f-16 text-red"></i></a>
                                            </div>';
                        } elseif (Auth::user()->can('edit')) {
                            return '<div class="table-actions">
                                            <a href="' . route('purchase.requisitions.edit', $purchaseOrders->id) . '" title="Edit"><i class="ik ik-edit-2 f-16 mr-15 text-green"></i></a>
                                            </div>';
                        } elseif (Auth::user()->can('show')) {
                            return '<div class="table-actions">
                                           <a href="' . route('purchase.requisitions.show', $purchaseOrders->id) . '" title="Show"><i class="ik ik-eye f-16 mr-15 text-blue"></i></a>
                                            </div>';
                        } elseif (Auth::user()->can('delete')) {
                            return '<div class="table-actions">
                                            <a type="submit" onclick="showDeleteConfirm(' . $purchaseOrders->id . ')" title="Delete"><i class="ik ik-trash-2 f-16 text-red"></i></a>
                                            </div>';
                        }
                    })
                    ->addIndexColumn()
                    ->rawColumns(['po_date', 'status','action'])
                    ->make(true);
            }
            return view('po.index', compact('purchaseOrders'));
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
                $mystore=Store::where('outlet_id', $employee->outlet_id)->first();
                if ($mystore == null) {
                    return redirect()->back()->with('error', __('Sorry! you dont have the access.'));
                }
            }

            $stores = Store::where('status', 1)->orderBy('name')->get();


            $purchaseOrder = PurchaseOrder::latest()->first();
            if(!empty($purchaseOrder)){
                $trim=trim($purchaseOrder->po_number,"PO-");
                $sl=$trim + 1;
                $sl_number="PO-".$sl;
            }else{
                $sl_number="PO-"."1";
            }
            $parts = Parts::where('status', 1)->orderBy('name')->get();
            return view('po.create', compact('user_role', 'stores','purchaseOrder', 'parts','sl_number','mystore'));
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
            'parts_id' => 'required',
            'from_store_id' => 'required',
            'po_number' => 'required|unique:purchase_orders,po_number,NULL,id,deleted_at,NULL',
        ]);

        try {
            $purchase = PurchaseOrder::create([
                'date' => $request->date,
                'po_number' => $request->po_number,
                'store_id' => $request->from_store_id,
                // 'store_id' => $request->from_store_id,
                'belong_to' => 1,
                'status' => 1,
                'created_by' => Auth::id(),
            ]);
            // dd($purchase);
            if($purchase) {
                foreach($request->part_id as $key => $id) {
                    if($id != null &&  $id > 0) {
                        $details['purchase_order_id'] = $purchase->id;
                        $details['part_id'] = $id;
                        $details['stock_in_hand'] = $request->stock_in_hand[$key];
                        $details['required_qnty'] = $request->required_quantity[$key];
                        PurchaseOrderDetails::create($details);
                    }
                }
            }

            return redirect()->route('purchase.requisitions.index')->with('success', __('New Purchase Requisition Created Successfully.'));
        } catch (\Exception $e) {
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
            $purchaseOrder = PurchaseOrder::with('purchaseOrderDetails')->find($id);
            return view('po.show', compact('purchaseOrder'));
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
            $purchaseOrder = PurchaseOrder::with('purchaseOrderDetails')->find($id);
            $purchaseOrderdetails = PurchaseOrderDetails::where('purchase_order_id', $purchaseOrder->id)->get();

            $selectParts = [];
            foreach($purchaseOrderdetails as $key=>$detail){

                $selectPart = Parts::where('id', $detail->part_id)->first();

                array_push($selectParts, $selectPart);
            }
            $parts=Parts::where('status', 1)->get();

            return view('po.edit', compact('purchaseOrder','purchaseOrderdetails','parts','selectParts'));
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
            'date' => 'required',
            'parts_id' => 'required',
            'from_store_id' => 'required',
            'po_number' => 'required|unique:purchase_orders,po_number,' . $id,
        ]);
        DB::beginTransaction();
        try {
            $purchaseOrder = PurchaseOrder::findOrFail($id);
            $purchaseOrderdetails = PurchaseOrderDetails::where('purchase_order_id', $purchaseOrder->id)->get();

            if($purchaseOrder != null){
                $purchaseOrder->update([
                    'updated_by' => Auth::id(),
                ]);
                foreach($request->parts_id as $key => $id){
                    $purchaseOrderdetails = PurchaseOrderDetails::where('purchase_order_id', $purchaseOrder->id)
                                                                ->where('part_id',$id)->first();
                    $purchaseOrderdetails->update([
                        'required_qnty'      => $request->required_quantity[$key],
                    ]);

                }
            }
            DB::commit();
            return redirect()->route('purchase.requisitions.index')->with('success', __('Purchase Requisition Updated Successfully.'));
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
        try {
            $purchase = PurchaseOrder::find($id);
            if ($purchase) {
                $purchase_details = PurchaseOrderDetails::where('purchase_order_id',$purchase->id)->get();
                foreach ($purchase_details as $key => $value) {
                    $value->delete();
                }
                $purchase->delete();
            }
            return response()->json([
                'success' => true,
                'message' => 'Purchase Requisition Deleted Successfully.',
            ]);
        } catch (\Exception $e) {
            $bug = $e->getMessage();
            return response()->json([
                'success' => false,
                'message' => $bug,
            ]);
        }
    }

    public function getPartsStockForOutlet(Request $request)
    {
        $part_id = $request->parts_id;
        $part_id_array = [];
        $model_id_array = [];
        // foreach($part_model_id as $key=>$id){
        //     $create_id = explode("-",$id);
        //     $part_id = $create_id[0];
        //     $model_id = $create_id[1];
        //     array_push($part_id_array,$part_id);
        //     array_push($model_id_array,$model_id);
        // }

        $stock_collect = [];
        $partInfo_collect = [];
        foreach($part_id as $key=>$pr_id){
            // $model_id = $model_id_array[$key];

            $stock_in = InventoryStock::where('part_id',$pr_id)
                    ->where('belong_to',1)
                    ->sum('stock_in');

            $stock_out = InventoryStock::where('part_id',$pr_id)
                    ->where('belong_to',1)
                    ->sum('stock_out');

            $partsInfo=Parts::where('id', $pr_id)->first();
            $stock_in_hand = $stock_in - $stock_out;
            array_push($stock_collect,$stock_in_hand);
            array_push($partInfo_collect,$partsInfo);
        }

        $html = view('requisition.requisition.parts_info', compact('partInfo_collect','stock_collect'))->render();
        return response()->json(compact('html'));
        //return  $partInfo_collect;
    }

    public function purchaseRequisitationDetails(Request $request)
    {
        $details = PurchaseOrderDetails::with('po')->where('purchase_order_id',$request->id)->get();
        return response()->json([
            'detail' => $details
        ]);
    }
}
