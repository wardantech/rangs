<?php

namespace App\Http\Controllers\Inventory;

use Session;
use Redirect;
use Response;
use Validator;
use DataTables;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\Inventory\Bin;
use App\Models\Inventory\Rack;
use App\Models\Inventory\Parts;
use App\Models\Inventory\Store;
use App\Models\Inventory\Source;
use App\Models\Employee\Employee;
use Illuminate\Support\Facades\DB;
use App\Models\Inventory\Inventory;
use App\Http\Controllers\Controller;
use App\Models\Inventory\PartsModel;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\Inventory\PartCategory;
use App\Models\Inventory\InventoryStock;
use App\Models\Inventory\PriceManagement;
use App\Models\Inventory\RackBinManagement;
use App\Models\Inventory\ProductSourcingVendor;


class InventoryController extends Controller
{
    public function index()
    {
        try{
            $auth = Auth::user();
            $user_role = $auth->roles->first();
            if ($user_role->name == 'Super Admin' || $user_role->name == 'Admin') {
                $inventoryArr = Inventory::with('source', 'store', 'productVendor')
                ->latest()->get();
            } else {
                $employee=Employee::where('user_id',Auth::user()->id)->first();
                // $mystore=Store::where('outlet_id',$employee->outlet_id)->first();
                $mystore=Store::where('id',$employee->store_id )->first();
                $inventoryArr = Inventory::where('store_id',$mystore->id)->with('source', 'store', 'productVendor')
                ->latest()->get();
            }

            return view('inventory.inventory.index',compact('inventoryArr'));
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
            if ($user_role->name == 'Super Admin' || $user_role->name == 'Admin') {
                $mystore='';
            } else {
                $employee=Employee::where('user_id',Auth::user()->id)->first();
                $mystore=Store::where('id',$employee->store_id )->first();
            }
            $stores = Store::where('status', 1)->where('user_id', null)->whereNull('deleted_at')->orderBy('name')->get();

            $vendors = ProductSourcingVendor::where('status', 1)
                        ->whereNull('deleted_at')
                        ->orderBy('name')
                        ->pluck('name','id')->toArray();
            $sources = Source::where('status', 1)->orderBy('name')->get();

            return view('inventory.inventory.create',compact('stores','vendors','sources','mystore','user_role'));
        } catch (\Exception $e) {
            $bug = $e->getMessage();
            return redirect()->back()->with('error', $bug);
        }
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'invoice_number' => 'required|string',
            'po_number'      => 'nullable|string',
            'lc_number'      => 'nullable|string',
            'receive_date'   => 'required',
            'vendor_id'      => 'nullable',
            'source_id'      => 'nullable',
            'store_id'       => 'required',
            'parts_id'       => 'required',
        ]);

        DB::beginTransaction();

        try{
            $receive=Inventory::create([
                'invoice_number' =>  $request->invoice_number,
                'po_number' =>  $request->po_number,
                'lc_number' =>  $request->lc_number,
                'receive_date' => $request->receive_date,
                'vendor_id' => $request->vendor_id,
                'source_id' => $request->source_id,
                'store_id' =>  $request->store_id,
                'description' => $request->note,
                'created_by' => Auth::id(),
            ]);

            //Managing Stock
           foreach($request->parts_id as $key=>$value){
                if($request->parts_id[$key] > 0){
                    InventoryStock::create([
                        'receive_id' =>  $receive->id,
                        'belong_to' =>  1, //1=Central WareHouse
                        'vendor_id' => $request->vendor_id,
                        'price_management_id' => $request->price_management_id[$key],
                        'cost_price_usd' => $request->cost_price_usd[$key],
                        'cost_price_bdt' => $request->cost_price_bdt[$key],
                        'selling_price_bdt' => $request->selling_price_bdt[$key],
                        'store_id' =>  $request->store_id,
                        'part_id' => $request->parts_id[$key],
                        'bin_id' => $request->bin_id[$key],
                        'rack_id' => $request->rack_id[$key],
                        'stock_in' => $request->quantity[$key],
                        'created_by' => Auth::id(),
                    ]);
                }
            }
            DB::commit();
            return redirect('inventory')
            ->with('success', __('Parts Received Successfully'));
        } catch (\Exception $e) {
            DB::rollback();
            $bug = $e->getMessage();
            return redirect()->back()->with('error', $bug);
        }
    }

    public function edit(Request $request)
    {
        try{
            $id = $request->id;
            //get id wise data
            $inventory = Inventory::find($id);
            $inventoryStocks=InventoryStock::where('receive_id', $inventory->id)->get();
            $stores = Store::where('status', 1)->orderBy('name')->get();
            $sources = Source::where('status', 1)->orderBy('name')->get();
            $parts = Parts::where('status', 1)->orderBy('name')->get();
            $selectParts= [];

            foreach($inventoryStocks as $inventoryStock){
                $selectPart = Parts::where('id', $inventoryStock->part_id)->first();
                array_push($selectParts, $selectPart);
            }

            $vendors = ProductSourcingVendor::where('status', 1)->orderBy('name')->get();

            return view('inventory.inventory.edit')->with(compact('inventory','stores','parts', 'selectParts','vendors','sources','inventoryStocks'));
        } catch (\Exception $e) {
            $bug = $e->getMessage();
            return redirect()->back()->with('error', $bug);
        }
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'invoice_number' => 'required|string',
            'po_number'      => 'nullable|string',
            'lc_number'      => 'nullable|string',
            'receive_date'   => 'required',
            'vendor_id'      => 'nullable',
            'source_id'      => 'nullable',
            'part_id' => 'required',
        ]);

        DB::beginTransaction();

        try{
                $inventory = Inventory::find($id);

                $inventory->update([
                    'invoice_number' => $request->invoice_number,
                    'po_number'      => $request->po_number,
                    'lc_number'      =>  $request->lc_number,
                    'receive_date'   => $request->receive_date,
                    'vendor_id'      => $request->vendor_id,
                    'source_id'      => $request->source_id,
                    'description'    => $request->note,
                    'updated_by'     => Auth::id(),
                ]);

                $inventoryStocks = InventoryStock::where('receive_id', $inventory->id)->get();
                foreach($inventoryStocks as $key=>$value){
                    $previousInventoryStocks = InventoryStock::where('id', $value->id)->first();
                        $previousInventoryStocks->update([
                            'cost_price_usd' => $request->cost_price_usd[$key],
                            'cost_price_bdt' => $request->cost_price_bdt[$key],
                            'selling_price_bdt' => $request->selling_price_bdt[$key],
                            'stock_in'      => $request->quantity[$key],  
                        ]);
                }
                DB::commit();
                return redirect('inventory')
                ->with('success', __('Parts Receive Updated Successfully'));

        }
        catch(\Exception $e){
                DB::rollback();
                $bug = $e->getMessage();
                return redirect()->back()->with('error', $bug);
        }

    }

        public function destroy(Request $request, $id)
        {
            try {
                $target = Inventory::find($id);
                if($target !=null){
                    $inventoryStocks= InventoryStock::where('receive_id', $target->id)->get();
                    foreach($inventoryStocks as $inventoryStock){
                        if($inventoryStock != null){
                            $inventoryStock->delete();
                        }
                    }
                    $target->delete();
                }

                return back()->with('success', __('Received Parts Deleted Successfully'));
            } catch (\Exception $e) {
                $bug = $e->getMessage();
                return redirect()->back()->with('error', $bug);
            }
        }

        public function show(Request $request, $id)
        {
            try{
                $inventory=Inventory::with('source', 'store', 'productVendor')->find($id);
                $inventory_details=InventoryStock::where('receive_id', $inventory->id)->get();
                $racks = Rack::where('status', 1)->orderBy('name')->get();
                $bins = Bin::where('status', 1)->orderBy('name')->get();

                return view('inventory.inventory.show',compact('inventory','inventory_details','racks','bins'));
            } catch (\Exception $e) {
                $bug = $e->getMessage();
                return redirect()->back()->with('error', $bug);
            }
        }

        //Stock Report
        public function stock(Request $request)
        {
            try{
                $employee=Employee::where('user_id',Auth::user()->id)->first();
                if($employee==null){
                    return redirect()->back()->with('error', __("Sorry! you don't have the access.")); 
                }

                // $mystore=Store::where('outlet_id',$employee->outlet_id)->first();
                $mystore=Store::where('id',$employee->store_id )->first();
                if ($mystore == null) {
                    return redirect()->back()->with('error', __("Sorry! you don't have the access."));
                }

                $raws = Parts::whereRelation('inventoryStock', function($q) use ($mystore) {
                    $q->where('store_id',$mystore->id)->whereRaw('stock_in - stock_out > 0');
                })
                ->where('status', 1)->orderBy('name');
                
                if(request()->ajax()){
                    return DataTables::of($raws)
                            ->addColumn('partcategory', function ($raws) {

                                if ($raws->partCategory !=null ) {
                                    $partCategory = $raws->partCategory->name;
                                } else {
                                    $partCategory ='null';
                                }
                                return $partCategory;
                                })

                            ->addColumn('partmodel', function ($raws) {

                                    if ($raws->partModel !=null ) {
                                        $partModel = $raws->partModel->name;
                                    } else {
                                        $partModel ='null';
                                    }
                                    return $partModel;
                                    })

                                    ->addColumn('rack', function ($raws) use ($mystore) {
                                        $rack=RackBinManagement::where('parts_id',$raws->id)->where('store_id',$mystore->id)->first();
                                        $get_rack=isset($rack->rack) ? $rack->rack->name : '';
                                        return $get_rack;
                                    })

                                    ->addColumn('bin', function ($raws) use ($mystore) {
                                        $bin=RackBinManagement::where('parts_id',$raws->id)->where('store_id',$mystore->id)->first();
                                        $get_bin=isset($bin->bin) ? $bin->bin->name : '';
                                        return $get_bin;
                                    })

                                    ->addColumn('selling_price_bdt', function ($raws) {
    
                                        $price=PriceManagement::where('part_id', $raws->id)->latest()->first();
                                        $s_price_bdt=isset($price) ? $price->selling_price_bdt : 0;
                                        return $s_price_bdt;
                                    })

                                    ->addColumn('cost_price_usd', function ($raws) {
    
                                        $price=PriceManagement::where('part_id', $raws->id)->latest()->first();
                                        $usd_price = isset($price) ? $price->cost_price_usd : 0;
                                            return $usd_price;
                                    })

                                    ->addColumn('cost_price_bdt', function ($raws) {
    
                                        $price=PriceManagement::where('part_id', $raws->id)->latest()->first();
                                        $c_price_bdt=isset($price) ? $price->cost_price_bdt : 0;
                                            return $c_price_bdt;
                                    })
                                    
                                    ->addColumn('last_cost_price_bdt', function ($raws) use ($mystore){
    
                                        $inventoryStock= InventoryStock::where('part_id', $raws->id)->where('store_id',$mystore->id)->where('stock_in', '>', 0)->latest()->first();
                                        $last_cost_price_bdt=$inventoryStock->cost_price_bdt ? $inventoryStock->cost_price_bdt : 0;
                                            return $last_cost_price_bdt;
                                    })
                                    ->addColumn('last_cost_price_usd', function ($raws) use ($mystore){
    
                                        $inventoryStock= InventoryStock::where('part_id', $raws->id)->where('store_id',$mystore->id)->where('stock_in', '>', 0)->latest()->first();
                                        $last_cost_price_usd=$inventoryStock->cost_price_usd ? $inventoryStock->cost_price_usd : 0;
                                            return $last_cost_price_usd;
                                    })
                                    ->addColumn('stockin', function ($raws) use ($mystore) {       
                                        $ins = InventoryStock::where('part_id', $raws->id)->where('store_id',$mystore->id)
                                        ->sum('stock_in');
                                            return $ins;
                                    })

                                    ->addColumn('stockout', function ($raws) use ($mystore) {       
                                        $outs = InventoryStock::where('part_id', $raws->id)->where('store_id',$mystore->id)
                                        ->sum('stock_out');
                                            return $outs;
                                    })

                                    ->addColumn('balance', function ($raws) use ($mystore) {  
                                        $ins = InventoryStock::where('part_id', $raws->id)->where('store_id',$mystore->id)
                                                ->sum('stock_in');
        
                                        $outs = InventoryStock::where('part_id', $raws->id)->where('store_id',$mystore->id)
                                                ->sum('stock_out');
                                            $balance=$ins - $outs;
                                            return $balance;
                                    })

                                    ->addColumn('stockvalueusd', function ($raws) use ($mystore) {  
                                            $price=PriceManagement::where('part_id', $raws->id)->latest()->first();
                                            $usd_price = isset($price) ? $price->cost_price_usd : 0;

                                            $ins = InventoryStock::where('part_id', $raws->id)->where('store_id',$mystore->id)
                                                ->sum('stock_in');
        
                                            $outs = InventoryStock::where('part_id', $raws->id)->where('store_id',$mystore->id)
                                                ->sum('stock_out');
                                                $balance=$ins - $outs;
                                                $total_usd=number_format($usd_price * $balance,3);
                                                return $total_usd;
                                    })
                                    
                                    ->addColumn('stockvaluebdt', function ($raws) use ($mystore) {  
                                            $price=PriceManagement::where('part_id', $raws->id)->latest()->first();
                                            $bdt_price= isset($price) ? $price->cost_price_bdt : 0;
    
                                            $ins = InventoryStock::where('part_id', $raws->id)->where('store_id',$mystore->id)
                                                ->sum('stock_in');
        
                                            $outs = InventoryStock::where('part_id', $raws->id)->where('store_id',$mystore->id)
                                                ->sum('stock_out');
                                                    $balance=$ins - $outs;
                                                    $total_bdt=number_format($bdt_price * $balance,3);
                                                    return $total_bdt;
                                    })
                                    ->addColumn('last_issued', function ($raws) use ($mystore) {  
                                            $allocation_date=DB::table('allocation_details')
                                            ->where('parts_id', $raws->id)
                                            ->orderBy('id', 'desc')->first();
                                            $date=null;
                                            if($allocation_date){
                                                $date=Carbon::parse($allocation_date->created_at)->format('m/d/Y');
                                            }

                                            return $date;
                                    })
                                    ->addColumn('action', function ($raws) use ($mystore) {
                                            if(Auth::user()->can('show')) {
                                                return '<div class="table-actions text-center">
                                                <a href="'.Route('inventory.show-inventory-details', [$raws->id, $mystore->id] ).'" title="View"><i class="ik ik-eye f-16 mr-15 text-blue"></i></a>
                                                </div>';
                                            }
                                    })
                                    ->addIndexColumn()
                                    ->rawColumns(['rack','bin','price','action'])
                                    ->make(true);
                }
                
                return view('inventory.stock.stock',compact('raws','mystore'));
            } catch (\Exception $e) {
                $bug = $e->getMessage();
                return redirect()->back()->with('error', $bug);
            }

        }
        public function stockInHandGet(Request $request)
        {
            try{
                $stores = Store::where('status', 1)->orderBy('name')->get();
                $raws = Parts::whereRelation('inventoryStock', function($q) {
                    $q->whereRaw('stock_in - stock_out > 0');
                })
                ->where('status', 1)->orderBy('name');

                if(request()->ajax()){
                    return DataTables::of($raws)
                            ->addColumn('partmodel', function ($raws) {

                                    if ($raws->partModel !=null ) {
                                        $partModel = $raws->partModel->name;
                                    } else {
                                        $partModel ='null';
                                    }
                                    return $partModel;
                                })
                                ->addColumn('selling_price_bdt', function ($raws) {
    
                                    $price=PriceManagement::where('part_id', $raws->id)->latest()->first();
                                    $s_price_bdt=isset($price) ? $price->selling_price_bdt : 0;
                                    return $s_price_bdt;
                                })

                                ->addColumn('cost_price_usd', function ($raws) {

                                    $price=PriceManagement::where('part_id', $raws->id)->latest()->first();
                                    $usd_price = isset($price) ? $price->cost_price_usd : 0;
                                    return $usd_price;
                                })

                                ->addColumn('cost_price_bdt', function ($raws) {

                                    $price=PriceManagement::where('part_id', $raws->id)->latest()->first();
                                    $c_price_bdt=isset($price) ? $price->cost_price_bdt : 0;
                                    return $c_price_bdt;
                                })
                                
                                ->addColumn('stockvalueusd', function ($raws) {  
                                    $price=PriceManagement::where('part_id', $raws->id)->latest()->first();
                                    $usd_price= isset($price) ? $price->cost_price_usd : 0;

                                    $ins = $raws->InventoryStock->sum('stock_in');

                                    $outs = $raws->InventoryStock->sum('stock_out');
                                        
                                    $balance=$ins - $outs;
                                    $total_usd=$usd_price * $balance;
                                    return $total_usd;
                                })
                            
                                ->addColumn('stockvaluebdt', function ($raws) {  
                                        $price=PriceManagement::where('part_id', $raws->id)->latest()->first();
                                        $bdt_price= isset($price) ? $price->cost_price_bdt : 0;

                                        $ins = $raws->InventoryStock->sum('stock_in');

                                        $outs = $raws->InventoryStock->sum('stock_out');
                                        $balance=$ins - $outs;
                                        $total_bdt=$bdt_price * $balance;
                                        return $total_bdt;
                                })                                      
                                ->addColumn('balance', function ($raws) {  
                                    $ins = $raws->InventoryStock->sum('stock_in');

                                    $outs = $raws->InventoryStock->sum('stock_out');
                                    $balance=$ins - $outs;
                                    return $balance;
                                })                                    
                                
                                    ->addIndexColumn()
                                    ->rawColumns(['partmodel','balance'])
                                    ->make(true);
                }
                return view('inventory.stock.stock-in-hand',compact('stores','raws'));
            } catch (\Exception $e) {
                $bug = $e->getMessage();
                return redirect()->back()->with('error', $bug);
            }
        }

        //Global Filtering
        public function stockInHandPostAll(Request $request)
        {
            try{
                $createDate = InventoryStock::select('created_at')->whereNull('deleted_at')->first()->created_at;
                $createDateFormat = $createDate->format('Y-m-d');

                $part='';
                if($request->part != null){
                    $part = Parts::findOrFail($request->part);
                }
                

                $stores = Store::where('status', 1)->latest()->get();
                $query1 = array(
                    'store_id',
                    'SUM(stock_in) AS stock_in',
                    'SUM(stock_out) AS stock_out'
                );

                    if($part != null && $request->store && $request->start_date && $request->end_date){
                        $stocks = InventoryStock::with('store')
                            ->where('part_id',$part->id)
                            ->Where('store_id', $request->store)
                            ->whereBetween('created_at', [$createDateFormat, $request->end_date])
                            ->selectRaw(implode(',', $query1))
                            ->groupBy('store_id')
                            ->get();

                        return view('inventory.stock.all-stock-in-hand-details',compact('part','stocks','stores'));
                    }else if($request->store != null){
                        $store = Store::where('id',$request->store)->first();
                            return view('inventory.stock.index',compact('store'));

                    }else if($request->store == null && $request->start_date && $request->end_date ){
                        $stocks = InventoryStock::with('store')
                            ->where('part_id',$part->id)
                            // ->Where('store_id', $request->store)
                            // ->orWhereBetween('created_at', [$createDateFormat, $request->end_date])
                            ->selectRaw(implode(',', $query1))
                            ->groupBy('store_id')
                            ->get();
                        return view('inventory.stock.all-stock-in-hand-details',compact('part','stocks','stores'));
                    }else{
                        $stocks = InventoryStock::with('store')
                            ->where('part_id', $part->id)
                            ->orWhere('store_id', $request->store)
                            ->orWhereBetween('created_at', [$createDateFormat, $request->end_date])
                            ->selectRaw(implode(',', $query1))
                            ->groupBy('store_id')
                            ->get();

                        return view('inventory.stock.all-stock-in-hand-details',compact('part','stocks','stores'));
                    }
            } catch (\Exception $e) {
                $bug = $e->getMessage();
                return redirect()->back()->with('error', $bug);
            }
        }

        public function stockDetails(){

        }

        private function __filter($request)
        {
            $query = array();
            if ($request->part_id != null) {
                $query['part_id'] = $request->part_id;
            }

            $stock = DB::table('inventory_stocks')
            ->join('inventorys','inventorys.id','=','inventory_stocks.receive_id')
            ->join('stores','stores.id','=','inventory_stocks.store_id')
            ->join('parts','parts.id','=','inventory_stocks.part_id')
            ->join('bins','bins.id','=','inventory_stocks.bin_id')
            ->join('racks','racks.id','=','inventory_stocks.rack_id')
            ->join('parts_models','parts_models.id','=','inventory_stocks.parts_model_id')
                ->select('inventory_stocks.id','inventory_stocks.stock_in','inventory_stocks.stock_out'
                ,'inventorys.bdt','inventorys.selling_price','stores.name as storename','parts.name as partname','parts_models.name as modelname'
                ,'bins.name as binname','racks.name as rackname')
                ->sum('inventory_stocks.stock_in');
            return $stock;
        }

        public function getPrice($part_id, $model_id)
        {
            $getPrice = PriceManagement::where('status', 1)->latest()->get();

            return response()->json($getPrice);
        }

        public function stockOutlet()
        {
            try{
                $auth = Auth::user();
                $user_role = $auth->roles->first();
                $employee=Employee::where('user_id', Auth::user()->id)->first();

                if($employee==null){
                    return redirect()->back()->with('error', __("Sorry! you don't have the access.")); 
                }
                $mystore=Store::where('id',$employee->store_id )->first();

                if (empty($mystore)) {
                    return redirect()->back()->with('error', __("Sorry! you don't have the access.")); 
                }else{
                    
                    $raws = Parts::whereRelation('inventoryStock', function($q) use ($mystore) {
                        $q->where('store_id',$mystore->id)->whereRaw('stock_in - stock_out > 0');
                    })
                    ->where('status', 1)->orderBy('name');

                    if(request()->ajax()){
                        return DataTables::of($raws)
                                ->addColumn('partcategory', function ($raws) {

                                    if ($raws->partCategory !=null ) {
                                        $partCategory = $raws->partCategory->name;
                                    } else {
                                        $partCategory ='null';
                                    }
                                    return $partCategory;
                                    })

                                ->addColumn('partmodel', function ($raws) {

                                        if ($raws->partModel !=null ) {
                                            $partModel = $raws->partModel->name;
                                        } else {
                                            $partModel ='null';
                                        }
                                        return $partModel;
                                        })

                                        ->addColumn('rack', function ($raws) use ($mystore) {
                                            $rack=RackBinManagement::where('parts_id',$raws->id)->where('store_id',$mystore->id)->first();
                                            $get_rack=isset($rack->rack) ? $rack->rack->name : '';
                                            return $get_rack;
                                        })

                                        ->addColumn('bin', function ($raws) use ($mystore) {
                                            $bin=RackBinManagement::where('parts_id',$raws->id)->where('store_id',$mystore->id)->first();
                                            $get_bin=isset($bin->bin) ? $bin->bin->name : '';
                                            return $get_bin;
                                        })

                                        ->addColumn('stockin', function ($raws) use ($mystore) {       
                                            $ins = InventoryStock::where('part_id', $raws->id)->where('store_id',$mystore->id)
                                            ->sum('stock_in');
                                                return $ins;
                                        })

                                        ->addColumn('stockout', function ($raws) use ($mystore) {       
                                            $outs = InventoryStock::where('part_id', $raws->id)->where('store_id',$mystore->id)
                                            ->sum('stock_out');
                                                return $outs;
                                        })

                                        ->addColumn('balance', function ($raws) use ($mystore) {  
                                            $ins = InventoryStock::where('part_id', $raws->id)->where('store_id',$mystore->id)
                                                    ->sum('stock_in');
            
                                            $outs = InventoryStock::where('part_id', $raws->id)->where('store_id',$mystore->id)
                                                    ->sum('stock_out');
                                                $balance=$ins - $outs;
                                                return $balance;
                                        })

                                        
                                        ->addColumn('action', function ($raws) use ($mystore) {
                                                if(Auth::user()->can('show')) {
                                                    return '<div class="table-actions text-center">
                                                    <a href="'.Route('branch.stock.details', [$raws->id, $mystore->id] ).'" title="View"><i class="ik ik-eye f-16 mr-15 text-blue"></i></a>
                                                    </div>';
                                                }
                                        })
                                        ->addIndexColumn()
                                        ->rawColumns(['rack','bin','action'])
                                        ->make(true);
                    }
                
                }
                return view('inventory.stock.outlet_stock',compact('mystore'));
            } catch (\Exception $e) {
                $bug = $e->getMessage();
                return redirect()->back()->with('error', $bug);
            }
        }

        public function stockForTechnician()
        {
            try{
                $employee=Employee::where('user_id',Auth::user()->id)->first();
                if($employee==null){
                    return redirect()->back()->with('error', __("Sorry! you don't have the access.")); 
                }

                $mystore=Store::where('user_id',Auth::user()->id)->first();
                if (empty($mystore)) {
                    return redirect()->back()->with('error', __("Sorry! you don't have the access.")); 
                }else {
                    $raws = Parts::whereRelation('inventoryStock', function($q) use ($mystore) {
                    $q->where('store_id',$mystore->id)->whereRaw('stock_in - stock_out > 0');
                    })
                    ->where('status', 1)->orderBy('name');

                    if(request()->ajax()){
                        return DataTables::of($raws)
                                ->addColumn('partcategory', function ($raws) {

                                    if ($raws->partCategory !=null ) {
                                        $partCategory = $raws->partCategory->name;
                                    } else {
                                        $partCategory ='null';
                                    }
                                    return $partCategory;
                                    })

                            ->addColumn('partmodel', function ($raws) {

                                    if ($raws->partModel !=null ) {
                                        $partModel = $raws->partModel->name;
                                    } else {
                                        $partModel ='null';
                                    }
                                    return $partModel;
                                    })

                                    ->addColumn('stockin', function ($raws) use ($mystore) {       
                                        $ins = InventoryStock::where('part_id', $raws->id)->where('store_id',$mystore->id)
                                        ->sum('stock_in');
                                            return $ins;
                                    })

                                    ->addColumn('stockout', function ($raws) use ($mystore) {       
                                        $outs = InventoryStock::where('part_id', $raws->id)->where('store_id',$mystore->id)
                                        ->sum('stock_out');
                                            return $outs;
                                    })

                                    ->addColumn('balance', function ($raws) use ($mystore) {  
                                        $ins = InventoryStock::where('part_id', $raws->id)->where('store_id',$mystore->id)
                                                ->sum('stock_in');
        
                                        $outs = InventoryStock::where('part_id', $raws->id)->where('store_id',$mystore->id)
                                                ->sum('stock_out');
                                            $balance=$ins - $outs;
                                            return $balance;
                                    })
                                    
                                    ->addColumn('action', function ($raws) use ($mystore) {
                                            if(Auth::user()->can('show')) {
                                                return '<div class="table-actions text-center">
                                                <a href="'.Route('technician.stock_details', [$raws->id, $mystore->id] ).'" title="View"><i class="ik ik-eye f-16 mr-15 text-blue"></i></a>
                                                </div>';
                                            }
                                    })
                                    ->addIndexColumn()
                                    ->rawColumns(['action'])
                                    ->make(true);
                    }
                }
                return view('inventory.stock.technician_stock',compact('mystore'));
            } catch (\Exception $e) {
                $bug = $e->getMessage();
                return redirect()->back()->with('error', $bug);
            }
        }

        public function inventoryDetails($id, $store_id)
        {
            try{
                $partDetails = Parts::findOrFail($id);
                $details = InventoryStock::where('part_id', $id)
                ->where('store_id', $store_id)
                ->orderBy('created_at', 'asc')
                ->get();

                return view('inventory.stock.parts_details', compact('details', 'partDetails'));
            } catch (\Exception $e) {
                $bug = $e->getMessage();
                return redirect()->back()->with('error', $bug);
            }
        }

        public function stockForTechnicianDetails($id, $store_id)
        {
            try{
                $partDetails = Parts::where('id', $id)->first();
                $details = InventoryStock::where('part_id', $id)
                ->where('store_id', $store_id)
                ->orderBy('created_at', 'asc')
                ->get();
                return view('inventory.stock.technician_details', compact('details', 'partDetails'));
            } catch (\Exception $e) {
                $bug = $e->getMessage();
                return redirect()->back()->with('error', $bug);
            }
        }

        public function getPartReceiveRow(Request $request){
            $part_id = $request->parts_id;
            $racks=Rack::where('status', 1)->where('store_id', $request->get('store_id'))->get();

            $part_id_array = [];
            $model_id_array = [];
            $partInfo_collect = [];
            $rackbinInfo= [];
            $priceInfo= [];
            if($part_id!=null){
            foreach($part_id as $key=>$pr_id){
                $rackbin=RackBinManagement::where('parts_id',$pr_id)->where('store_id',$request->store_id)->first();
                $partsInfo=Parts::where('id', $pr_id)->first();
                $price=PriceManagement::where('part_id', $pr_id)->latest()->first();
                array_push($partInfo_collect,$partsInfo);
                array_push($rackbinInfo, $rackbin);
                array_push($priceInfo, $price);
            }
        }
            $html = view('inventory.inventory.parts_receive_row', compact('partInfo_collect','racks','rackbinInfo', 'priceInfo'))->render();
            return response()->json(compact('html'));
        }

        public function outletInventoryDetails($id, $store_id)
        {
            try{
                $partDetails = Parts::where('id', $id)->first();
                $details = InventoryStock::where('part_id', $id)
                        ->where('store_id', $store_id)
                        ->orderBy('created_at', 'asc')
                        ->get();
                return view('inventory.stock.outlet_details', compact('details', 'partDetails'));
            } catch (\Exception $e) {
                $bug = $e->getMessage();
                return redirect()->back()->with('error', $bug);
            }
        }

        public function sampleExcel(){
            return Response::download(public_path('sample/part_stock_sample.xlsx', 'part_stock_sample.xlsx'));
        }
    
        public function import(Request $request)
        {
            try{
                Excel::import(new InventoryStock, $request->file('import_file'));
                return back()->with('success', __('Data Uploaded Successfully'));
            }catch (\Exception $e) {
                return redirect()->back()->with('error', $e->getMessage());
            }
        }
        public function stockInHandPostAllByStore(Request $request, $id)
        {
            if(request()->ajax()){
                $raws = Parts::whereRelation('inventoryStock', function($q) use ($id){
                    $q->where('store_id', $id)
                    ->whereRaw('stock_in - stock_out > 0');
                })
                ->where('status', 1)->orderBy('name');

                return DataTables::of($raws)

                    ->addColumn('partmodel', function ($raws) {

                        if ($raws->partModel !=null ) {
                            $partModel = $raws->partModel->name;
                        } else {
                            $partModel ='null';
                        }
                        return $partModel;
                    })
                    ->addColumn('selling_price_bdt', function ($raws) {
    
                        $price=PriceManagement::where('part_id', $raws->id)->latest()->first();
                        $s_price_bdt=isset($price) ? $price->selling_price_bdt : 0;
                        return $s_price_bdt;
                    })

                    ->addColumn('cost_price_usd', function ($raws) {

                        $price=PriceManagement::where('part_id', $raws->id)->latest()->first();
                        $usd_price = isset($price) ? $price->cost_price_usd : 0;
                            return $usd_price;
                    })

                    ->addColumn('cost_price_bdt', function ($raws) {

                        $price=PriceManagement::where('part_id', $raws->id)->latest()->first();
                        $c_price_bdt=isset($price) ? $price->cost_price_bdt : 0;
                            return $c_price_bdt;
                    })
                    ->addColumn('stockvalueusd', function ($raws) use ($id) {  
                        $price=PriceManagement::where('part_id', $raws->id)->latest()->first();
                        $usd_price= isset($price) ? $price->cost_price_usd : 0;

                        $ins = InventoryStock::where('part_id', $raws->id)->where('store_id',$id)
                            ->sum('stock_in');

                        $outs = InventoryStock::where('part_id', $raws->id)->where('store_id',$id)
                            ->sum('stock_out');
                            $balance=$ins - $outs;
                            $total_usd=$usd_price * $balance;
                            return $total_usd;
                    })
                
                    ->addColumn('stockvaluebdt', function ($raws) use ($id) {  
                        $price=PriceManagement::where('part_id', $raws->id)->latest()->first();
                        $bdt_price= isset($price) ? $price->cost_price_bdt : 0;

                        $ins = InventoryStock::where('part_id', $raws->id)->where('store_id',$id)
                            ->sum('stock_in');

                        $outs = InventoryStock::where('part_id', $raws->id)->where('store_id',$id)
                            ->sum('stock_out');
                                $balance=$ins - $outs;
                                $total_bdt=$bdt_price * $balance;
                                return $total_bdt;
                    }) 
                    ->addColumn('balance', function ($raws) use($id) {  
                        $ins = InventoryStock::where('part_id', $raws->id)
                        ->where('store_id', $id)
                        ->sum('stock_in');

                        $outs = InventoryStock::where('part_id', $raws->id)
                        ->where('store_id', $id)
                        ->sum('stock_out');
                        
                        $balance=$ins - $outs;
                        return $balance;
                    })                                    
                    
                        ->addIndexColumn()
                        ->rawColumns(['balance','partmodel'])
                        ->make(true);
            }  
        }
        public function excelStock($id)
        {
            $store = Store::findOrFail('id',$id);
            $raws = Parts::whereRelation('inventoryStock', function($q) use ($id){
                $q->where('store_id', $id)
                ->whereRaw('stock_in - stock_out > 0');
            })
            ->where('status', 1)->orderBy('name');
            return Excel::download(new SingleStockExport($id), 'Stock '.$store->name.'.xlsx');
        }

    public function receivedItems(Request $request)
    {
        try{
            $receivedItems=DB::table('inventory_stocks')
            ->join('inventorys','inventory_stocks.receive_id', '=', 'inventorys.id')
            ->leftJoin('sources','inventorys.source_id', '=', 'sources.id')
            ->leftJoin('stores','inventorys.store_id', '=', 'stores.id')
            ->leftJoin('product_sourcing_vendors','inventorys.vendor_id', '=', 'product_sourcing_vendors.id')
            ->leftJoin('parts','inventory_stocks.part_id', '=', 'parts.id')
            ->leftJoin('parts_models', 'parts.part_model_id', '=', 'parts_models.id')
            ->leftJoin('racks','inventorys.rack_id', '=', 'racks.id')
            ->leftJoin('bins','inventorys.bin_id', '=', 'bins.id')
            ->select(
                'inventorys.receive_date',
                'inventorys.invoice_number',
                'product_sourcing_vendors.name as vendor_name',
                'sources.name as source_name',
                'stores.name as store_name',
                'parts.name as parts_name',
                'parts.code as parts_code',
                'parts_models.name as parts_model',
                'racks.name as rack_name',
                'bins.name as bin_name',
                'inventory_stocks.stock_in as received_qnty',
                'inventory_stocks.cost_price_usd',
                'inventory_stocks.cost_price_bdt',
                'inventory_stocks.selling_price_bdt as unit_price_bdt'
            )
            ->get();
        if (request()->ajax()) {
            return DataTables::of($receivedItems)

                ->addColumn('receive_date', function ($receivedItem) {
                    // $requisition_date = Carbon::parse($receivedItem->requisition_date)->format('m/d/Y');
                    return $receivedItem->receive_date;
                })
                ->addColumn('invoice_number', function ($receivedItem) {
                    // $allocation_date=$receivedItem->allocation_date->format('m/d/Y');
                    return $receivedItem->invoice_number;
                })

                ->addColumn('vendor_name', function ($receivedItem) {
                    $vendor_name=$receivedItem->vendor_name;
                    return $vendor_name;
                })

                ->addColumn('source_name', function ($receivedItem) {
                    $source_name=$receivedItem->source_name;
                    return $source_name;
                })
                ->addColumn('store_name', function ($receivedItem) {
                    $store_name = $receivedItem->store_name;
                        return $store_name; 
                })
                ->addColumn('parts_code', function ($receivedItem) {
                    $parts_code = $receivedItem->parts_code;
                        return $parts_code; 
                })
                ->addColumn('parts_name', function ($receivedItem) {
                    $parts_name = $receivedItem->parts_name;
                        return $parts_name; 
                })
                ->addColumn('parts_model', function ($receivedItem) {
                    $parts_model = $receivedItem->parts_model;
                        return $parts_model; 
                })
                ->addColumn('rack_name', function ($receivedItem) {
                    $rack_name=$receivedItem->rack_name;
                    return $rack_name;
                })
                ->addColumn('bin_name', function ($receivedItem) {
                    $bin_name=$receivedItem->bin_name; 
                    return $bin_name;
                })
                ->addColumn('received_qnty', function ($receivedItem) {
                    $received_qnty=$receivedItem->received_qnty; 
                    return $received_qnty;
                })
                ->addColumn('cost_price_bdt', function ($receivedItem) {
                    $cost_price_bdt=$receivedItem->cost_price_bdt; 
                    return $cost_price_bdt;
                })
                ->addColumn('cost_price_usd', function ($receivedItem) {
                    $cost_price_usd=$receivedItem->cost_price_usd; 
                    return $cost_price_usd;
                })
                ->addColumn('unit_price_bdt', function ($receivedItem) {
                    $unit_price_bdt=$receivedItem->unit_price_bdt; 
                    return $unit_price_bdt;
                })
                ->addIndexColumn()
                ->make(true);
        }
        return view('inventory.inventory.received-items');
    } catch (\Exception $e) {
        $bug = $e->getMessage();
        dd($bug);
        return redirect()->back()->with('error', $bug);
    }

    }

}
