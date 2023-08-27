<?php

namespace App\Http\Controllers\Inventory;

use DB;
use Auth;
use Carbon\Carbon;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\Inventory\Parts;
use App\Models\Inventory\Store;
use Yajra\DataTables\DataTables;
use App\Models\Customer\Customer;
use App\Models\Employee\Employee;
use App\Models\Inventory\PartSell;
use App\Http\Controllers\Controller;
use App\Models\Inventory\PartCategory;
use App\Models\Inventory\InventoryStock;
use App\Models\Inventory\PartSellDetails;
use App\Models\Inventory\PriceManagement;

class DirectPartsSellController extends Controller
{
    public function index()
    {
        try{
            $auth = Auth::user();
            $user_role = $auth->roles->first();
            if ($user_role->name == 'Super Admin' || $user_role->name == 'Admin') {
                $partSells=PartSell::with('store', 'customer')->orderBy('id', 'desc');

            }else{
                $employee=Employee::where('user_id',Auth::user()->id)->first();
                // $mystore=Store::where('outlet_id',$employee->outlet_id)->first();
                $mystore=Store::where('id',$employee->store_id )->first();
                if ($mystore != null) {
                    $partSells=PartSell::with('store', 'customer')->where('store_id', $mystore->id)->orderBy('id', 'desc');
                }else{
                    return redirect()->back()->with('error', __("Sorry! you don't have the access."));
                }
            }
            if (request()->ajax()) {
                return DataTables::of($partSells)
                    ->addColumn('parts_name', function ($partSells) {
                        $partSellDetails = PartSellDetails::where('partSell_id', $partSells->id)->get();
                        $res='Not Found';
                        if(!empty($partSellDetails)){
                            $data = [];
                            $part_name = '';
                            foreach($partSellDetails as $detail){
                                    $data[] =$detail->part->code.'-'. $detail->part->name.' = '.$detail->quantity .' Pcs ';
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
                    ->addColumn('dateFormat', function ($partSells) {
                        $data = Carbon::parse($partSells->date)->format('m/d/Y');
                        return $data;
                    })

                    ->addColumn('storeName', function ($partSells) {
                        $store = isset($partSells->store) ? $partSells->store->name : null;
                        return $store;
                    })

                    ->addColumn('customerName', function ($partSells) {
                        $customer = isset($partSells->customer) ? $partSells->customer->name : null;
                        return $customer;
                    })

                    ->addColumn('customerPhone', function ($partSells) {
                        $customer_phone = isset($partSells->customer_phone) ? $partSells->customer_phone : null;
                        return $customer_phone;
                    })

                    ->addColumn('customerAddress', function ($partSells) {
                        $customer_address = isset($partSells->customer_address) ? $partSells->customer_address : null;
                        return $customer_address;
                    })

                    ->addColumn('action', function ($partSells) {
                        if (Auth::user()->can('edit') && Auth::user()->can('delete') && Auth::user()->can('show')) {
                            return '<div class="table-actions text-center" style="display: flex;">
                                            <a href="' . route('sell.show.direct-parts-sell', $partSells->id) . '" title="Edit"><i class="ik ik-eye f-16 mr-15 text-blue"></i></a>
                                            <a href="' . route('sell.edit.direct-parts-sell', $partSells->id) . '" title="Edit"><i class="ik ik-edit-2 f-16 mr-15 text-green"></i></a>
                                            <a type="submit" onclick="showDeleteConfirm(' . $partSells->id . ')" title="Delete"><i class="ik ik-trash-2 f-16 text-red"></i></a>
                                            </div>';
                        } elseif (Auth::user()->can('show')) {
                            return '<div class="table-actions">
                                            <a href="' . route('sell.show.direct-parts-sell', $partSells->id) . '" title="Edit"><i class="ik ik-eye f-16 mr-15 text-blue"></i></a>
                                            </div>';
                        } elseif (Auth::user()->can('edit')) {
                        return '<div class="table-actions">
                                            <a href="' . route('sell.edit.direct-parts-sell', $partSells->id) . '" title="Edit"><i class="ik ik-edit-2 f-16 mr-15 text-green"></i></a>
                                            </div>';
                        }elseif (Auth::user()->can('delete')) {
                            return '<div class="table-actions">
                                            <a type="submit" onclick="showDeleteConfirm(' . $partSells->id . ')" title="Delete"><i class="ik ik-trash-2 f-16 text-red"></i></a>
                                            </div>';
                        }
                    })
                    ->addIndexColumn()
                    ->rawColumns(['parts_name','dateFormat', 'storeName', 'customerName', 'customerPhone', 'customerAddress','action'])
                    ->make(true);
            }
            return view('inventory.direct_parts_sell.index', compact('partSells'));
        } catch (\Exception $e) {
            $bug = $e->getMessage();
            return redirect()->back()->with('error', $bug);
        }
    }

    public function create()
    {
        try{
            $auth = Auth::user();
            $user_role = $auth->roles->first();

            $partsCategories=PartCategory::where('status', 1)->get();
            $stores= Store::where('status', 1)->get();
            //
            $partSell=PartSell::latest()->first();
            if(!empty($partSell)){
                $trim=trim($partSell->mr_no,"MR-NO-");
                $sl=$trim + 1;
                $mr_no="MR-NO-".$sl;
            }else{
                $mr_no="MR-NO-"."1";
            }

            $employee=Employee::where('user_id', Auth::id())->first();
            $mystore='';
            $customers=Customer::all();

            if ($employee != null) {
                // $mystore=Store::where('outlet_id',$employee->outlet_id)->first();
                $mystore=Store::where('id',$employee->store_id )->first();
            } else {
                $employee=Auth::user();
            }
            
            return view('inventory.direct_parts_sell.create', compact('partsCategories','stores', 'employee', 'customers', 'mr_no','user_role','mystore'));
        } catch (\Exception $e) {
            $bug = $e->getMessage();
            return redirect()->back()->with('error', $bug);
        }
    }

    public function getPartSellRow(Request $request)
    {
        $part_id = $request->parts_id;
        $stock_collect = [];
        $partInfo_collect = [];
        $priceInfo= [];
        if($part_id!=null){
            $auth = Auth::user();
            $user_role = $auth->roles->first();
            foreach($part_id as $key=>$pr_id){
                $stock_in = InventoryStock::where('part_id', $pr_id)->where('store_id', $request->store_id)->sum('stock_in');
                $stock_out = InventoryStock::where('part_id', $pr_id)->where('store_id', $request->store_id)->sum('stock_out');

                $partsInfo=Parts::where('id', $pr_id)->first();
                $price=PriceManagement::where('part_id', $pr_id)->latest('id')->first();
                $stock_in_hand = $stock_in - $stock_out;
                array_push($stock_collect,$stock_in_hand);
                array_push($partInfo_collect,$partsInfo);
                array_push($priceInfo, $price);
            }
        }

        $html = view('inventory.direct_parts_sell.part_sell_row', compact('partInfo_collect','stock_collect', 'priceInfo'))->render();
        return response()->json(compact('html'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'mr_no'              => 'required',
            'sales_by'           => 'required',
            'date'               => 'required',
            'store_id'           => 'required|numeric',
            'customer_id'        => 'required|numeric',
            'customer_phone'     => 'required',
            'customer_address'   => 'required',
            'spare_parts_amount' => 'required',
            'discount'           => 'required',
            'net_amount'         => 'required',
            'quantity'           => 'required',
            'selling_price'      => 'required',
            'amount'             => 'required',
        ]);

        DB::beginTransaction();
        try {
            $partSell=PartSell::create([
                'mr_no'             => $request->mr_no,
                'sales_by'           => $request->sales_by,
                'date'               => $request->date,
                'store_id'           => $request->store_id,
                'customer_id'        => $request->customer_id,
                'customer_phone'     => $request->customer_phone,
                'customer_address'   => $request->customer_address,
                'spare_parts_amount' => $request->spare_parts_amount,
                'discount'           => $request->discount,
                'net_amount'         => $request->net_amount,

            ]);

            foreach($request->parts_id as $key=>$part_id){
                PartSellDetails::create([
                    'partSell_id'   => $partSell->id,
                    'part_id'       => $request->part_id[$key],
                    'quantity'      => $request->quantity[$key],
                    'selling_price' => $request->selling_price[$key],
                    'amount'        => $request->amount[$key],
                ]);
                $auth = Auth::user();
                $user_role = $auth->roles->first();

                $employee=Employee::where('user_id', Auth::user()->id)->first();
                if ($user_role->name == 'Technician') {
                    $mystore=Store::where('user_id',Auth::user()->id)->first();
                }elseif ($user_role->name == 'Admin' || $user_role->name == 'Super Admin') {
                    $mystore=Store::where('id',$request->store_id)->first();
                }
                else{
                    // $mystore=Store::where('outlet_id', $employee->outlet_id)->first();
                    $mystore=Store::where('id',$employee->store_id )->first();
                }


                if($mystore->user_id){
                    $belongTo = 3;
                }
                if($mystore->outlet_id && $mystore->name != "Central Warehouse"){
                    $belongTo = 2;
                }
                if($mystore->name === "Central Warehouse"){
                    $belongTo = 1;
                }

                InventoryStock::create([
                    'part_sell_id' => $partSell->id,
                    'belong_to' => $belongTo,
                    'store_id'  => $request->store_id,
                    'part_id' => $request->part_id[$key],
                    'stock_out' => $request->quantity[$key],
                    'is_consumed' => 1,
                    'type' => 2,
                    'created_by' => Auth::id(),
                ]);
            }

        DB::commit();
        return redirect()->route('sell.direct-parts-sell-index')->with('success', __('New Parts Sell created successfully.'));
        } catch (\Exception $e) {
            DB::rollback();
            $bug = $e->getMessage();
            return redirect()->back()->with('error', $bug);
        }
    }

    public function edit($id)
    {
        try{
            $partSell=PartSell::findOrFail($id);
            $partSellDetails= PartSellDetails::where('partSell_id', $id)->get();
            $customers=Customer::all();
            $auth = Auth::user();
            $user_role = $auth->roles->first();
            $employee=Employee::where('user_id', Auth::id())->first();
            $mystore='';
            $customers=Customer::all();

            if ($employee != null) {
                // $mystore=Store::where('outlet_id',$employee->outlet_id)->first();
                $mystore=Store::where('id',$employee->store_id )->first();
            } else {
                $employee=Auth::user();
            }
            $selectedParts=[];
            foreach($partSellDetails as $partSellDetail){
                $part=Parts::where('id', $partSellDetail->part_id)->first();
                array_push($selectedParts, $part);
            }
            $partsCategories=PartCategory::where('status', 1)->get();
            $parts=Parts::where('status', 1)->get();
            $stores= Store::where('status', 1)->get();
            return view('inventory.direct_parts_sell.edit', compact('selectedParts', 'partSell', 'partSellDetails', 'stores', 'user_role', 'mystore', 'partsCategories', 'parts', 'customers'));
        } catch (\Exception $e) {
            $bug = $e->getMessage();
            return redirect()->back()->with('error', $bug);
        }
    }

    public function getPartSellRowForEdit(Request $request){
        $part_id = $request->parts_id;

            $partSell=PartSell::find($request->part_sell_id);

            $part_id_array = [];
            $model_id_array = [];
            $stock_collect = [];
            $partInfo_collect = [];
            $priceInfo= [];
            $selectedQuantity= [];
            $amount= [];

            if($part_id!=null){
            foreach($part_id as $key=>$pr_id){

                $stock_in = InventoryStock::where('part_id', $pr_id)->where('store_id', $request->store_id)->sum('stock_in');
                $stock_out = InventoryStock::where('part_id', $pr_id)->where('store_id', $request->store_id)->sum('stock_out');

                $partsInfo=Parts::where('id', $pr_id)->first();
                $singleSelectedQuantity= PartSellDetails::where('partSell_id', $request->part_sell_id)->where('part_id', $pr_id)->select('quantity')->first();
                $singleRowQuantity= $singleSelectedQuantity;

                array_push($selectedQuantity, $singleRowQuantity);
                $singleAmount= PartSellDetails::where('partSell_id', $request->part_sell_id)->where('part_id', $pr_id)->select('amount')->first();
                array_push($amount, $singleAmount);
                $price=PriceManagement::where('part_id', $pr_id)->latest('id')->first();
                $stock_in_hand = $stock_in - $stock_out;
                array_push($stock_collect,$stock_in_hand);
                array_push($partInfo_collect,$partsInfo);
                array_push($priceInfo, $price);
            }
        }
        // dd($priceInfo);
            $html = view('inventory.direct_parts_sell.part_sell_row_for_edit', compact('partInfo_collect','stock_collect', 'priceInfo', 'selectedQuantity', 'partSell', 'amount'))->render();
            return response()->json(compact('html'));
    }

    public function update($id, Request $request)
    {
        // dd($request->all());
        $this->validate($request, [
            'mr_no'              => 'required',
            'sales_by'           => 'required',
            'date'               => 'required',
            'store_id'           => 'required|numeric',
            'customer_id'        => 'required|numeric',
            'customer_phone'     => 'required',
            'customer_address'   => 'required',
            'spare_parts_amount' => 'required',
            'discount'           => 'required',
            'net_amount'         => 'required',
            'quantity'           => 'required',
            'selling_price'      => 'required',
            'amount'             => 'required',
        ]);

        DB::beginTransaction();
        try {

            $partSell=PartSell::find($id);
            $partSell->update([
                'mr_no'              => $request->mr_no,
                'sales_by'           => $request->sales_by,
                'date'               => $request->date,
                'store_id'           => $request->store_id,
                'customer_id'        => $request->customer_id,
                'customer_phone'     => $request->customer_phone,
                'customer_address'   => $request->customer_address,
                'spare_parts_amount' => $request->spare_parts_amount,
                'discount'           => $request->discount,
                'net_amount'         => $request->net_amount,
            ]);

            foreach($request->part_id as $key=>$value){
                
                if($request->part_id[$key] > 0){
                    $partSellDetails = PartSellDetails::where('partSell_id', $partSell->id)->where('part_id', $request->part_id[$key])
                    ->update([
                        'partSell_id'   => $partSell->id,
                        'part_id'       => $request->part_id[$key],
                        'quantity'      => $request->quantity[$key],
                        'selling_price' => $request->selling_price[$key],
                        'amount'        => $request->amount[$key],
                    ]);
                    
                    $inventoryStock = InventoryStock::where('part_sell_id', $partSell->id)->where('part_id', $request->part_id[$key])
                    ->update([
                        'part_sell_id' => $partSell->id,
                        'part_id' => $request->part_id[$key],
                        'stock_out' => $request->quantity[$key],
                    ]);
                }
            }

            DB::commit();
            return redirect()->route('sell.direct-parts-sell-index')->with('success', __('Parts Sell Updated Successfully.'));
        } catch (\Exception $e) {
            DB::rollback();
             $bug = $e->getMessage();
             return redirect()->back()->with('error', $bug);
        }
    }

    public function show($id)
    {
        try{
            $partSell=PartSell::with('store', 'customer')->find($id);
            $partSellDetails= PartSellDetails::where('partSell_id', $partSell->id)->get();

            return view('inventory.direct_parts_sell.show',compact('partSell','partSellDetails'));
        } catch (\Exception $e) {
            $bug = $e->getMessage();
            return redirect()->back()->with('error', $bug);
        }
    }

    public function destroy($id){
        try {
            $PartSell=PartSell::findOrFail($id);
            if($PartSell){
                $InventoryStocks=InventoryStock::where('part_sell_id',$PartSell->id)->get();
                foreach ($InventoryStocks as $key => $InventoryStock) {
                    $InventoryStock->delete();
                }
                $PartSell->delete();
            }
            return response()->json([
                'success' => true,
                'message' => 'Parts Sell Deleted Successfully.',
            ]);
        } catch (\Exception $e) {
            $bug = $e->getMessage();
            return response()->json([
                'success' => false,
                'message' => $bug,
            ]);
        }
    }

    public function getCustomerInfo(Request $request){

        $customer_id= $request->customer_id;
        $customer= Customer::where('id', $customer_id)->first();

        return response()->json([
            'customer'          => $customer
        ]);
    }
}
