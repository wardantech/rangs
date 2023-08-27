<?php

namespace App\Http\Controllers\Inventory;

use Session;
use Redirect;
use Response;
use Validator;
use DataTables;
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
                $mystore=Store::where('outlet_id',$employee->outlet_id)->first();
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
                $mystore=Store::where('outlet_id',$employee->outlet_id)->first();
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

                $mystore=Store::where('outlet_id',$employee->outlet_id)->first();
                if ($mystore == null) {
                    return redirect()->back()->with('error', __("Sorry! you don't have the access."));
                }

                $raws = Parts::with('inventoryStock')->whereHas('inventoryStock', function($q) use ($mystore) {
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
                                            $balance=abs($ins - $outs );
                                            return $balance;
                                    })

                                    ->addColumn('stockvalueusd', function ($raws) use ($mystore) {  
                                            $price=PriceManagement::where('part_id', $raws->id)->latest()->first();
                                            $usd_price = isset($price) ? $price->cost_price_usd : 0;

                                            $ins = InventoryStock::where('part_id', $raws->id)->where('store_id',$mystore->id)
                                                ->sum('stock_in');
        
                                            $outs = InventoryStock::where('part_id', $raws->id)->where('store_id',$mystore->id)
                                                ->sum('stock_out');
                                                $balance=abs($ins - $outs );
                                                $total_usd=$usd_price * $balance;
                                                return $total_usd;
                                    })
                                    
                                    ->addColumn('stockvaluebdt', function ($raws) use ($mystore) {  
                                            $price=PriceManagement::where('part_id', $raws->id)->latest()->first();
                                            $bdt_price= isset($price) ? $price->cost_price_bdt : 0;
    
                                            $ins = InventoryStock::where('part_id', $raws->id)->where('store_id',$mystore->id)
                                                ->sum('stock_in');
        
                                            $outs = InventoryStock::where('part_id', $raws->id)->where('store_id',$mystore->id)
                                                ->sum('stock_out');
                                                    $balance=abs($ins - $outs );
                                                    $total_bdt=$bdt_price * $balance;
                                                    return $total_bdt;
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
                
                return view('inventory.stock.index',compact('raws','mystore'));
            } catch (\Exception $e) {
                $bug = $e->getMessage();
                return redirect()->back()->with('error', $bug);
            }

        }
        public function stockInHandGet(Request $request)
        {
            try{
                $stores = Store::where('status', 1)->orderBy('name')->get();
                $raws = Parts::with('inventoryStock')->whereHas('inventoryStock', function($q) {
                    $q->whereRaw('stock_in - stock_out > 0');
                })
                ->where('status', 1)->orderBy('name');
                // dd($raws);
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
                                    
                                ->addColumn('balance', function ($raws) {  
                                    $ins = InventoryStock::where('part_id', $raws->id)
                                                ->sum('stock_in');
        
                                    $outs = InventoryStock::where('part_id', $raws->id)
                                                ->sum('stock_out');
                                    $balance=abs($ins - $outs );
                                    return $balance;
                                })                                    
                                
                                    ->addIndexColumn()
                                    ->rawColumns(['partmodel'])
                                    ->make(true);
                }
                return view('inventory.stock.stock-in-hand',compact('stores','raws'));
            } catch (\Exception $e) {
                $bug = $e->getMessage();
                return redirect()->back()->with('error', $bug);
            }
        }


        public function stockInHandPost(Request $request)
        {
            try{
                $search = $request->input('part');
                $part=Parts::where('code', 'LIKE', "%$search%")
                ->orWhere('name','LIKE',"%$search%")
                ->first();

                $stocks = InventoryStock::
                with(['store','part','model','employee'])
                ->where('part_id',$part->id)
                ->select('store_id', DB::raw('sum(stock_in) as stock_in'), DB::raw('sum(stock_out) as stock_out'))
                ->groupBy('store_id')
                ->get();

                return view('inventory.stock.stock-in-hand-details',compact('stocks','partsModel'));
            } catch (\Exception $e) {
                $bug = $e->getMessage();
                return redirect()->back()->with('error', $bug);
            }
        }

        //Global Filtering
        public function stockInHandPostAll(Request $request)
        {
            // $this->validate($request, [
            //     'part' => 'required'
            // ]);
            // dd($request->all());
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

                // if ($part != null ) {
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
                        $mystore=$request->store;
                        $store=Store::where('id',$request->store)->first();
                        // $raws = Parts::with('inventoryStock')->whereHas('inventoryStock', function($q) use ($mystore) {
                        //     $q->where('store_id',$mystore)->whereRaw('stock_in - stock_out > 0');
                        //     })
                        //     ->where('status', 1)->orderBy('name');
                        //     dd($raws);
                        $raws = Parts::where('status', 1)->orderBy('name')->get();
                            $stocks = [];
                            foreach ($raws as $key => $value) {
                                $item = [];
                                $details = InventoryStock::where(
                                    'part_id', $value->id
                                )
                                ->where('store_id',$mystore)
                                ->first();
            
                                $ins = InventoryStock::where(
                                    'part_id', $value->id
                                )
                                ->where('store_id',$mystore)
                                ->sum('stock_in');
            
                                $outs = InventoryStock::where(
                                    'part_id', $value->id
                                )
                                ->where('store_id',$mystore)
                                ->sum('stock_out');
                                
                                $rackbin=RackBinManagement::where('parts_id',$value->id)->where('store_id',$mystore)->first();
                                // $item['store'] = $details->store->name;
                                $item['store_id'] = $mystore;
                                $item['id'] = $value->id;
                                $item['model_name'] = $value->partModel->name ?? null;
                                $item['category'] = $value->partCategory->name ?? null;
                                $item['source'] = isset($details->inventory->source) ? $details->inventory->source->name :'';
                                $item['parts_code'] = $value->code;
                                $item['parts_name'] = $value->name;
                                $item['rack'] = isset($rackbin->rack) ? $rackbin->rack->name : '';
                                $item['bin'] =  isset($rackbin->bin) ? $rackbin->bin->name : '';
                                $item['selling_price'] = isset($details->price->selling_price_bdt) ? $details->price->selling_price_bdt :'0';
                                $item['cost_price_usd'] = isset($details->price->cost_price_usd) ? $details->price->cost_price_usd :'0';
                                $item['cost_price_bdt'] = isset($details->price->cost_price_bdt) ? $details->price->cost_price_bdt :'0';
                                $item['in'] = $ins;
                                $item['out'] = $outs;
                                $item['stock'] = abs($ins - $outs );
            
                                if($item['stock'] != 0){
                                    array_push($stocks, $item);
                                }
                            }
                            return view('inventory.stock.index',compact('stocks','store'));
                    }else if($request->store == null && $request->start_date && $request->end_date ){
                        $stocks = InventoryStock::with('store')
                            ->where('part_id',$part->id)
                            // ->Where('store_id', $request->store)
                            // ->orWhereBetween('created_at', [$createDateFormat, $request->end_date])
                            ->selectRaw(implode(',', $query1))
                            ->groupBy('store_id')
                            ->get();

                        // dd(Parts::find($request->part));
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
                // }else {
                //     return redirect()->back()->with('error', __('Sorry Unavailable Part'));
                // }
            } catch (\Exception $e) {
                $bug = $e->getMessage();
                return redirect()->back()->with('error', $bug);
            }
        }

        public function stockInHandByPartModel(Request $request)
        {

            $search = $request->input('part');

            $purchase_info=[];
            $part=Parts::where('name', 'LIKE', "%$search%")
            ->first();
            $inventoryStocks = InventoryStock::where('part_id',$part->id)->get();
            foreach ($inventoryStocks as $key => $inventoryStock) {
                $item['inventorystock_id'] = $inventoryStock->id;
                if($inventoryStock->store){
                    $item['store'] = $inventoryStock->store->name ?? null;
                }
                $item['model_name'] = $inventoryStock->model->name ?? null;
                $item['parts_name'] = $inventoryStock->part->name ?? null;

                $item['in'] = $inventoryStock->stock_in;
                $item['out'] =  $inventoryStock->stock_out;
                $item['stock'] = abs($inventoryStock->stock_in - $inventoryStock->stock_out );
                array_push($purchase_info, $item);
            }
            return response()->json($purchase_info);

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

                $mystore=Store::where('outlet_id',$employee->outlet_id)->first();

                if (empty($mystore)) {
                    return redirect()->back()->with('error', __("Sorry! you don't have the access.")); 
                }else{
                    
                $raws = Parts::with('inventoryStock')->whereHas('inventoryStock', function($q) use ($mystore) {
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
                                            $balance=abs($ins - $outs );
                                            return $balance;
                                    })

                                    ->addColumn('stockvalueusd', function ($raws) use ($mystore) {  
                                            $price=PriceManagement::where('part_id', $raws->id)->latest()->first();
                                            $usd_price= isset($price) ? $price->cost_price_usd : 0;

                                            $ins = InventoryStock::where('part_id', $raws->id)->where('store_id',$mystore->id)
                                                ->sum('stock_in');
        
                                            $outs = InventoryStock::where('part_id', $raws->id)->where('store_id',$mystore->id)
                                                ->sum('stock_out');
                                                $balance=abs($ins - $outs );
                                                $total_usd=$usd_price * $balance;
                                                return $total_usd;
                                    })
                                    
                                    ->addColumn('stockvaluebdt', function ($raws) use ($mystore) {  
                                            $price=PriceManagement::where('part_id', $raws->id)->latest()->first();
                                            $bdt_price= isset($price) ? $price->cost_price_bdt : 0;
    
                                            $ins = InventoryStock::where('part_id', $raws->id)->where('store_id',$mystore->id)
                                                ->sum('stock_in');
        
                                            $outs = InventoryStock::where('part_id', $raws->id)->where('store_id',$mystore->id)
                                                ->sum('stock_out');
                                                    $balance=abs($ins - $outs );
                                                    $total_bdt=$bdt_price * $balance;
                                                    return $total_bdt;
                                    })
                                    
                                    ->addColumn('action', function ($raws) use ($mystore) {
                                            if(Auth::user()->can('show')) {
                                                return '<div class="table-actions text-center">
                                                <a href="'.Route('branch.stock.details', [$raws->id, $mystore->id] ).'" title="View"><i class="ik ik-eye f-16 mr-15 text-blue"></i></a>
                                                </div>';
                                            }
                                    })
                                    ->addIndexColumn()
                                    ->rawColumns(['rack','bin','price','action'])
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
                    $raws = Parts::with('inventoryStock')->whereHas('inventoryStock', function($q) use ($mystore) {
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
                                            $balance=abs($ins - $outs );
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
            // request()->validate([
            //     'file' => 'required|mimes:csv,txt,xlxs'
            // ]);
            try{
            //     $csv    = file($request->import_file);
            //     $chunks = array_chunk($csv,1000);
                
            //     $header = [];
            //     $batch  = Bus::batch([])->dispatch();
            //     foreach ($chunks as $key => $chunk) {
            //         $data = array_map('str_getcsv', $chunk);
            //             if($key == 0){
            //                 $header = $data[0];
            //                 unset($data[0]);
            //             }
            //             $batch->add(new PartCsvProcess($data, $header));
            //         }
            //     return $chunks;
                // dd($batch);
                Excel::import(new InventoryStock, $request->file('import_file'));
                return back()->with('success', __('Data Uploaded Successfully'));
            }catch (\Exception $e) {
                return redirect()->back()->with('error', $e->getMessage());
            }
        }
}
