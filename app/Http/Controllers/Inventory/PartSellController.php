<?php

namespace App\Http\Controllers\Inventory;

use DB;
use Session;
use Redirect;
use Validator;
use DataTables;
use Illuminate\Http\Request;
use App\Models\Outlet\Outlet;

// use Illuminate\Support\Facades\DB;
use App\Models\Inventory\Parts;
use App\Models\Inventory\Store;
use App\Http\Controllers\Controller;
use App\Models\Inventory\PartsModel;
use App\Models\Inventory\InventoryStock;


class PartSellController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('inventory.part_sell.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        try{
            $outlates = Outlet::where('status', 1)->latest()->get();
            $stores = Store::where('status', 1)->latest()->get();
            $parts=Parts::where('status', 1)->get();
            return view('inventory.part_sell.create', compact('outlates', 'stores', 'parts'));
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
        //
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

    public function partsStockDetails(Request $request)
    {
        $part_model_id = $request->model_id;
        $part_id_array = [];
        $model_id_array = [];
        foreach($part_model_id as $key=>$id){
            $create_id = explode("-",$id);
            $part_id = $create_id[0];
            $model_id = $create_id[1];
            array_push($part_id_array,$part_id);
            array_push($model_id_array,$model_id);
        }

        $stock_collect = [];
        $partInfo_collect = [];
        $rack_info=[];
        $bin_info=[];
        foreach($part_id_array as $key=>$pr_id){
            $model_id = $model_id_array[$key];

            $stock_in = InventoryStock::where('part_id',$pr_id)->where('belong_to',1)->where('parts_model_id',$model_id)->sum('stock_in');
            $stock_out = InventoryStock::where('part_id',$pr_id)->where('belong_to',1)->where('parts_model_id',$model_id)->sum('stock_out');
            $rack=DB::table('inventory_stocks')
                    ->where('inventory_stocks.part_id', $pr_id)
                    ->where('inventory_stocks.parts_model_id', $model_id)
                    ->join('racks', 'inventory_stocks.rack_id', '=', 'racks.id')
                    ->select('racks.name')
                    ->first();

            $partsInfo=PartsModel::where('id', $model_id)->with('part')->first();
            $stock_in_hand = $stock_in - $stock_out;
            array_push($stock_collect,$stock_in_hand);
            array_push($partInfo_collect,$partsInfo);
            array_push($rack_info,$rack);
        }

        $html = view('inventory.part_sell.parts_info_view', compact('partInfo_collect','stock_collect', 'rack_info', 'bin_info'))->render();
        return response()->json(compact('html'));
    }
}
