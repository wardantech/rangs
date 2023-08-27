<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Inventory\Inventory;
use App\Models\Inventory\Store;
use App\Models\Inventory\Parts;
use App\Models\Inventory\PartsModel;
use App\Models\Inventory\Bin;
use App\Models\Inventory\Rack;
use App\Models\Inventory\ProductSourcingVendor;
use App\Models\Inventory\InventoryStock;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Inventory\PriceManagement;
use DataTables;
use Validator;
use Session;
use Redirect;

class InventoryController extends Controller
{
    public function index(){

        // $inventoryArr=Inventory::join('stores','stores.id','=','inventorys.store_id')
        // ->join('parts','parts.id','=','inventorys.part_id')
        // ->join('bins','bins.id','=','inventorys.bin_id')
        // ->join('racks','racks.id','=','inventorys.rack_id')
        // ->join('parts_models','parts_models.id','=','inventorys.model_id')
        // ->select('inventorys.id','inventorys.order_date','inventorys.receive_date','inventorys.invoice_number',
        // 'inventorys.sending_date','inventorys.quantity','inventorys.bdt','inventorys.selling_price',
        // 'stores.name as storename','parts.name as partname','bins.name as binname'
        // ,'parts_models.name as modelname'
        // ,'bins.name as binname','racks.name as rackname')
        // ->get();
        $inventoryArr=Inventory::latest()->get();
        return view('inventory.inventory.index',compact('inventoryArr'));

    }

    public function create(){

        $stores = Store::where('status', 1)->pluck('name','id')->toArray();
        $models = PartsModel::where('status', 1)->pluck('name','id')->toArray();
        $parts = Parts::where('status', 1)->pluck('name','id')->toArray();
        $bins = Bin::where('status', 1)->pluck('name','id')->toArray();
        $racks = Rack::where('status', 1)->pluck('name','id')->toArray();
        $vendors = ProductSourcingVendor::where('status', 1)->pluck('name','id')->toArray();
        return view('inventory.inventory.create',compact('stores','parts','bins','racks','vendors','models'));
    }

    public function store(Request $request){
        $rules = [
            'store_id' => 'required',
            'part_id' => 'required',
            'model_id' => 'required',
            'invoice' => 'required',
            'sending_date' => 'required',
            'order_date' => 'required',
            'receive_date' => 'required',
            // 'bin_id[]' => 'required',
            // 'rack_id' => 'required',
            // 'vendor_id' => 'required',
            // 'usd' => 'required',
            // 'bdt' => 'required',
            // 'selling_price' => 'required',
            ];
        $validator = Validator::make($request->all(), $rules);

        // if ($validator->fails()) {
        //     return redirect('/inventory/create')
        //                     ->withInput($request->all())
        //                     ->withErrors($validator);
        // }

        DB::beginTransaction();

        try{
            $receive=Inventory::create([
                'store_id' =>  $request->store_id,
                'model_id' =>  $request->model_id,
                'invoice_number' =>  $request->invoice_number,
                'part_id' => $request->part_id,
                'sending_date'=>$request->sending_date,
                'order_date'=>$request->order_date,
                'receive_date' => $request->receive_date,
                'bin_id' => json_encode($request->bin_id),
                'rack_id' => $request->rack_id,
                'quantity' => $request->quantity,
                'usd' => $request->usd,
                'bdt' => $request->bdt,
                'selling_price' => $request->selling_price,
                'vendor_id' => $request->vendor_id,
                'description' => $request->vendor_id,
                'created_by' => Auth::id(),
            ]);

            //Managing Stock
            InventoryStock::create([
                'receive_id' =>  $receive->id,
                'belong_to' =>  1, //1=Central WareHouse
                'price_management_id' =>  $request->price_management_id,
                'store_id' =>  $request->store_id,
                'parts_model_id' => $request->model_id,
                'part_id' => $request->part_id,
                'bin_id' => json_encode($request->bin_id),
                'rack_id' => $request->rack_id,
                'vendor_id' => $request->vendor_id,
                'stock_in' => $request->quantity,
                'created_by' => Auth::id(),
            ]);

            DB::commit();
            return redirect('inventory')
            ->with('success', __('label.NEW_INVENTORY_CREATED'));
            }catch(\Exception $e){
                dd($e);
                DB::rollback();
                return redirect()->back()->with('error','Something Went Wrong!');
            }
    }

    public function edit(Request $request) {
        $id = $request->id;
        //get id wise data
        $target = Inventory::find($id);
        $models = PartsModel::where('status', 1)->pluck('name','id')->toArray();
        $stores = Store::where('status', 1)->pluck('name','id')->toArray();
        $parts = Parts::where('status', 1)->pluck('name','id')->toArray();
        $bins = Bin::where('status', 1)->pluck('name','id')->toArray();
        $racks = Rack::where('status', 1)->pluck('name','id')->toArray();
        $vendors = ProductSourcingVendor::where('status', 1)->pluck('name','id')->toArray();

        return view('inventory.inventory.edit')->with(compact('models','target','stores','parts','bins','racks','vendors'));
    }

    public function update(Request $request, $id) {

        $rules = [
                'store_id' => 'required',
                'part_id' => 'required',
                'order_date' => 'required',
                'receive_date' => 'required',
                'bin_id' => 'required',
                'rack_id' => 'required',
                // 'vendor_id' => 'required',
                'usd' => 'required',
                'bdt' => 'required',
                'selling_price' => 'required',
                ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return Redirect::to('/inventory/'.$id.'/edit')
                                ->withInput($request->all())
                                ->withErrors($validator);
            }

            DB::beginTransaction();

            try{
            $inventory = Inventory::find($id);

            $inventory->update([
                'store_id' =>  $request->store_id,
                'model_id' =>  $request->model_id,
                'invoice_number' =>  $request->invoice_number,
                'part_id' => $request->part_id,
                'sending_date'=>$request->sending_date,
                'order_date'=>$request->order_date,
                'receive_date' => $request->receive_date,
                'bin_id' => json_encode($request->bin_id),
                'rack_id' => $request->rack_id,
                'quantity' => $request->quantity,
                'usd' => $request->usd,
                'bdt' => $request->bdt,
                'selling_price' => $request->selling_price,
                'vendor_id' => $request->vendor_id,
                'description' => $request->vendor_id,
                'updated_by' => Auth::id(),
            ]);

            //Managing Stock
            $inventoryStock = InventoryStock::where('receive_id',$inventory->receive_id)->first();
            $inventoryStock->update([
                'receive_id' =>  $inventoryStock->id,
                'store_id' =>  $request->store_id,
                'parts_model_id' => $request->model_id,
                'part_id' => $request->part_id,
                'bin_id' => json_encode($request->bin_id),
                'rack_id' => $request->rack_id,
                'vendor_id' => $request->vendor_id,
                'stock_in' => $request->quantity,
                'updated_by' => Auth::id(),
            ]);

            DB::commit();
            return redirect('inventory')
            ->with('success', __('label.INVENTORY_UPDATED_SUCCESSFULLY'));
            }catch(\Exception $e){
                DB::rollback();
                return redirect()->back()->with('error', $e);
            }

        }

        public function destroy(Request $request, $id) {

            $target = Inventory::find($id);
                try {
                    $target->delete();
                    return redirect('inventory')->with('success', __('label.INVENTORY_REMOVED'));

                } catch (\Exception $e) {
                    $bug = $e->getMessage();

                    return redirect()->back()->with('error', $bug);
                }

        }

        public function show(Request $request, $id)
        {
            $target=Inventory::find($id);
            return view('inventory.inventory.show',compact('target'));

        }

        //Stock Report
        public function stock(Request $request)
        {
            $raws = PartsModel::where('status', 1)->get();
            $stocks = [];
            foreach ($raws as $key => $value) {
                $item = [];
                $details = InventoryStock::where(
                    'parts_model_id', $value->id
                )
                    ->first();

                $ins = InventoryStock::where(
                    'parts_model_id', $value->id
                )
                    ->sum('stock_in');

                $outs = InventoryStock::where(
                    'parts_model_id', $value->id
                )
                    ->sum('stock_out');

                // $item['store'] = $details->store->name;
                $item['id'] = $value->id;
                $item['model_name'] = $value->name;
                $item['parts_name'] = $value->part->name;
                $item['in'] = $ins;
                $item['out'] = $outs;
                $item['stock'] = abs($ins - $outs );
                array_push($stocks, $item);
            }
            return view('inventory.stock.index',compact('stocks'));

        }
        public function stockInHandGet(Request $request)
        {
            // $stores = Store::pluck('name','id')->toArray();
            $raws = PartsModel::where('status', 1)->get();
            $stocks = [];
            foreach ($raws as $key => $value) {
                $item = [];
                $details = InventoryStock::where(
                    'parts_model_id', $value->id
                )
                    ->first();

                $ins = InventoryStock::where(
                    'parts_model_id', $value->id
                )
                    ->sum('stock_in');

                $outs = InventoryStock::where(
                    'parts_model_id', $value->id
                )
                ->where('belong_to',3)
                    ->sum('stock_out');
                // $item['store'] = $details->store->name;
                $item['model_name'] = $value->name;
                $item['parts_name'] = $value->part->name;
                $item['in'] = $ins;
                $item['out'] = $outs;
                $item['stock'] = abs($ins - $outs );
                // dd($outs);
                array_push($stocks, $item);
            }
            return view('inventory.stock.stock-in-hand',compact('stocks'));

        }
        public function stockInHandPost(Request $request)
        {

            // SELECT *,(SELECT SUM(stock_in) FROM `inventory_stocks` WHERE parts_model_id= 14 AND store_id = stores.id) as stock_in,(SELECT SUM(stock_out) FROM `inventory_stocks` WHERE parts_model_id= 14 AND store_id=stores.id) as stock_out FROM stores;

        // $search = $request->input('part_model');
        // $stocks	=[];
        // $partsModel=PartsModel::where('name', 'LIKE', "%$search%")
        // ->first();

        // $stocks=InventoryStock::where('parts_model_id',14)
        // ->join('stores','stores.id','=','inventory_stocks.store_id')
        // ->join('parts','parts.id','=','inventory_stocks.part_id')
        // ->join('parts_models','parts_models.id','=','inventory_stocks.parts_model_id')
        // ->select('stores.name as storename','parts.name as partname','parts_models.name as modelname',
        // 'inventory_stocks.stock_in as stockin','inventory_stocks.stock_out as stockout'
        // )
        // // ->sum('inventory_stocks.stock_in');
        // ->get();
        // dd($stocks);
            // $stores = Store::all();
            $search = $request->input('part_model');
            $partsModel=PartsModel::where('name', 'LIKE', "%$search%")
            ->first();
            // foreach ($stores as $key => $store) {
            //     $Arr= InventoryStock::where('parts_model_id',$partsModel->id)
            //     ->where('store_id,',$store->id)
            //     ->sum('stock_in');
            //     $item = [];
            //     $item['stock_in'] = $Arr->stock_in;
            //     $item['stock_out'] = $Arr->stock_out;
            //     array_push($stocks, $item);
            // }

            // $stocks = InventoryStock::where('parts_model_id',$partsModel->id);
            // dd($stocks->sum('stock_in'));
            // dd($stocks->sum('stock_out'));
            // $arr = InventoryStock::where('parts_model_id',$partsModel->id)->select('parts_model_id', DB::raw('sum(stock_in) as stock_in'), DB::raw('sum(stock_out) as stock_out'))->groupBy('parts_model_id');
            $stocks = InventoryStock::
            with(['store','part','model'])
            ->where('parts_model_id',$partsModel->id)
            ->select('store_id', DB::raw('sum(stock_in) as stock_in'), DB::raw('sum(stock_out) as stock_out'))
            ->groupBy('store_id')->get();
            // $stocks=$arr->with(['store','part','model']);
// dd($stocks);
            // // $stocks = [];
            // foreach ($stocks as $key => $value) {
            //     $item = [];
            //     $item['model_name'] = $value->name;
            //     $item['parts_name'] = $value->part->name;
            //     $item['in'] = $ins;
            //     $item['out'] = $outs;
            //     $item['stock'] = abs($ins - $outs );
            //     // dd($outs);
            //     array_push($stocks, $item);
            // }

            // $query = array();
            // if ($partsModel != null) {
            //     $query['parts_model_id'] = $partsModel->party_id;
            // }
            // // $inventoryStocks = InventoryStock::where('parts_model_id',$partsModel->id)->orderBy('store_id')->sum('stock_in');
            // $inventoryStocks = InventoryStock::where($query)->select('party_id', DB::raw('sum(stock_in) as stock_in'), DB::raw('sum(stock_out) as stock_out'))->groupBy('store_id');
            // $stock = $inventoryStocks->with(['store', 'part']);
            // dd($stock);
            // $raws = PartsModel::all();
            // $stocks = [];

            // return view('inventory.stock.stock-in-hand',compact('stocks'));
            return view('inventory.stock.stock-in-hand-details',compact('stocks','partsModel'));

        }
        public function stockInHandByPartModel(Request $request)
        {

            $search = $request->input('part_model');

            $purchase_info=[];
            $partsModel=PartsModel::where('name', 'LIKE', "%$search%")
            ->first();
            $inventoryStocks = InventoryStock::where('parts_model_id',$partsModel->id)->get();
            foreach ($inventoryStocks as $key => $inventoryStock) {
                $item['inventorystock_id'] = $inventoryStock->id;
                if($inventoryStock->store){
                    $item['store'] = $inventoryStock->store->name;
                }
                $item['model_name'] = $inventoryStock->model->name;
                $item['parts_name'] = $inventoryStock->part->name;
                $item['in'] = $inventoryStock->stock_in;
                $item['out'] =  $inventoryStock->stock_out;
                $item['stock'] = abs($inventoryStock->stock_in - $inventoryStock->stock_out );
                array_push($purchase_info, $item);
            }
            return response()->json($purchase_info);
            //

            // $raws = PartsModel::all();
            // $stocks = [];
            // foreach ($raws as $key => $value) {
            //     $item = [];
            //     $details = InventoryStock::where(
            //         'parts_model_id', $value->id
            //     )
            //         ->first();

            //     $ins = InventoryStock::where(
            //         'parts_model_id', $value->id
            //     )
            //         ->sum('stock_in');

            //     $outs = InventoryStock::where(
            //         'parts_model_id', $value->id
            //     )
            //     ->where('belong_to',3)
            //         ->sum('stock_out');
            //     // $item['store'] = $details->store->name;
            //     $item['model_name'] = $value->name;
            //     $item['parts_name'] = $value->part->name;
            //     $item['in'] = $ins;
            //     $item['out'] = $outs;
            //     $item['stock'] = abs($ins - $outs );
            //     // dd($outs);
            //     array_push($stocks, $item);
            // }
            // return view('inventory.stock.stock-in-hand',compact('stocks'));

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
            $getPrice = PriceManagement::where('status', 1)->get();

            return response()->json($getPrice);
        }

        public function stockOutlet(){
            $raws = PartsModel::where('status', 1)->get();
            $stocks = [];
            foreach ($raws as $key => $value) {
                $item = [];
                $details = InventoryStock::where(
                    'parts_model_id', $value->id
                )->first();

                $ins = InventoryStock::where(
                    'parts_model_id', $value->id
                )->where('belong_to',2)->sum('stock_in');

                $outs = InventoryStock::where(
                    'parts_model_id', $value->id
                )->where('belong_to',2)->sum('stock_out');

                // $item['store'] = $details->store->name;
                $item['model_name'] = $value->name;
                $item['parts_name'] = $value->part->name;
                $item['in'] = $ins;
                $item['out'] = $outs;
                $item['stock'] = abs($ins - $outs );
                array_push($stocks, $item);
            }
            return view('inventory.stock.outlet_stock',compact('stocks'));
        }

        public function stockForTechnician(){
            $raws = PartsModel::where('status', 1)->get();
            $stocks = [];
            foreach ($raws as $key => $value) {
                $item = [];
                $details = InventoryStock::where(
                    'parts_model_id', $value->id
                )->first();

                $ins = InventoryStock::where(
                    'parts_model_id', $value->id
                )->where('belong_to',3)->sum('stock_in');

                $outs = InventoryStock::where(
                    'parts_model_id', $value->id
                )->where('belong_to',3)->sum('stock_out');

                // $item['store'] = $details->store->name;
                $item['id'] = $value->id;
                $item['model_name'] = $value->name;
                $item['parts_name'] = $value->part->name;
                $item['in'] = $ins;
                $item['out'] = $outs;
                $item['stock'] = abs($ins - $outs );
                array_push($stocks, $item);
            }
            return view('inventory.stock.technician_stock',compact('stocks'));
        }

        public function inventoryDetails($id)
        {
            $partDetails = PartsModel::where('id', $id)->first();
            $details = InventoryStock::where('belong_to', 1)
                    ->where('parts_model_id', $id)
                    ->get();

            return view('inventory.stock.parts_details', compact('details', 'partDetails'));
        }

        public function stockForTechnicianDetails($id)
        {
            $partDetails = PartsModel::where('id', $id)->first();
            $details = InventoryStock::where('belong_to', 3)
                    ->where('parts_model_id', $id)
                    ->get();
            return view('inventory.stock.technician_details', compact('details', 'partDetails'));
        }
}
