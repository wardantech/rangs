<?php

namespace App\Http\Controllers\Inventory;

use Session;
use Redirect;
use Response;
use Validator;
use DataTables;
use Illuminate\Http\Request;
use App\Models\Inventory\Parts;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\Inventory\PartsModel;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\Inventory\PartCategory;
use App\Models\Requisition\Requisition;
use App\Models\Inventory\InventoryStock;
use App\Models\Inventory\RackBinManagement;
use App\Models\Requisition\RequisitionDetails;

class ModelController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try{
            $partsModels = PartsModel::with('part', 'category')->latest();
            if (request()->ajax()) {
                return DataTables::of($partsModels)

                    ->addColumn('partcategory', function ($partsModels) {
                        $data = isset($partsModels->category) ? $partsModels->category->name : null;
                        return $data;
                    })

                    ->addColumn('status', function ($partsModels) {

                        if ($partsModels->status == true) {
                            $status = '<div class="text-center">
                                            <a href="' . route('inventory.parts_model.status', $partsModels->id) . '" title="Status" class="btn btn-sm btn-success">
                                                <i class="fas fa-arrow-up"></i>
                                            </a>
                                        </div>';
                        } else {
                            $status = '<div class="text-center">
                                        <a href="' . route('inventory.parts_model.status', $partsModels->id) . '" title="Status" class="btn btn-sm btn-secondary">
                                            <i class="fas fa-arrow-down"></i>
                                        </a>
                                    </div>';
                        }
                        return $status;
                    })

                    ->addColumn('action', function ($partsModels) {
                        if (Auth::user()->can('edit') && Auth::user()->can('delete')) {
                            return '<div class="table-actions">
                                        <a href="' . route('inventory.parts_model.edit', $partsModels->id) . '" title="Edit"><i class="ik ik-edit-2 f-16 mr-15 text-green"></i></a>
                                        <a type="submit" onclick="showDeleteConfirm(' . $partsModels->id . ')" title="Delete"><i class="ik ik-trash-2 f-16 text-red"></i></a>
                                        </div>';
                        } elseif (Auth::user()->can('edit')) {
                            return '<div class="table-actions">
                                        <a href="' . route('inventory.parts_model.edit', $partsModels->id) . '" title="Edit"><i class="ik ik-edit-2 f-16 mr-15 text-green"></i></a>
                                        </div>';
                        } elseif (Auth::user()->can('delete')) {
                            return '<div class="table-actions">
                                            <a type="submit" onclick="showDeleteConfirm(' . $partsModels->id . ')" title="Delete"><i class="ik ik-trash-2 f-16 text-red"></i></a>
                                            </div>';
                        }
                    })
                    ->addIndexColumn()
                    ->rawColumns(['action', 'status', 'partcategory'])
                    ->make(true);
            }
            return view('inventory.model.index', compact('partsModels'));
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
            $partCategory = PartCategory::where('status', 1)->orderBy('name')
                        ->pluck('name','id')->toArray();

            return view('inventory.model.create',compact('partCategory'));
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
        $rules = [
            'part_category_id' => 'required|integer',
            'name' => [
    		    'required',
                Rule::unique('parts_models')
                    ->where('part_category_id', $request->part_category_id)
                    ->where('name', $request->name)
                ],
            ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return redirect()
                            ->back()
                            ->withInput($request->all())
                            ->withErrors($validator);
        }

        try{
            $partsModel=new PartsModel;
            $partsModel->part_category_id    = $request->part_category_id;
            $partsModel->name = $request->name;
            $partsModel->created_by = Auth::id();
            $partsModel->save();
            return redirect('inventory/parts_model')->with('success', 'Parts Model Created Successfully');

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
            $partsModel = PartsModel::findOrFail($id);
            $partCategory = PartCategory::where('status', 1)->orderBy('name')
                            ->pluck('name','id')->toArray();

            return view('inventory.model.edit', compact('partsModel','partCategory'));
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
        $rules = [
            'part_category_id' => 'required|integer',
            'name' => [
    		    'required',
                Rule::unique('parts_models')
                    ->where('part_category_id', $request->part_category_id)
                    ->where('name', $request->name)
                    ->ignore($id)
                ],
            ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return redirect()
                            ->back()
                            ->withInput($request->all())
                            ->withErrors($validator);
        }
        try{
            $PartsModel=PartsModel::findOrFail($id);
            $PartsModel->update([
                'part_category_id' => $request->part_category_id,
                'name' => $request->name,
                'updated_by' => Auth::id()
            ]);
            return redirect('inventory/parts_model')
                ->with('success', __('label.PARTS MODEL UPDATED SUCCESSFULLY'));

        } catch (\Exception $e) {
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
            $PartsModel=PartsModel::findOrFail($id);
            $Parts = Parts::where('part_model_id',$PartsModel->id)->get();
            if(count($Parts) > 0){
                return response()->json([
                    'success' => false,
                    'message' => "Sorry! Can't Delete. This Part Model is used in Part Management",
                ]);
            }else{
                $PartsModel->delete();
                return response()->json([
                    'success' => true,
                    'message' => 'Part Model Deleted Successfully.',
                ]);
            }
        } catch (\Exception $e) {
            $bug = $e->getMessage();
            return response()->json([
                'success' => false,
                'message' => $bug,
            ]);
        }
    }

    public function getModel(Request $request)
    {
        // $partsModel = PartsModel::with('part')->where('part_category_id', $request->id)->get();
        $partModels=PartsModel::where('part_category_id', $request->id)->get();

        return response()->json([
            'partModels' => $partModels
        ]);
    }

    public function getPart(Request $request)
    {
        // $partsModel = PartsModel::with('part')->where('part_category_id', $request->id)->get();
        $parts=Parts::where('status', 1)->where('part_model_id', $request->id)->get();

        return response()->json([
            'parts' => $parts
        ]);
    }

    public function getStockDetails(Request $request){
        $stock_in = InventoryStock::where('part_id',$request->id)->where('parts_model_id',$request->model_id)->sum('stock_in');
        $stock_out = InventoryStock::where('part_id',$request->id)->where('parts_model_id',$request->model_id)->sum('stock_out');
        $stock_in_hand = $stock_in - $stock_out;
        return response()->json([
            'stockInHand'          => $stock_in_hand
        ]);
    }

    public function getStocInfo(Request $request){
        //return $request->all();
        $stock_in = InventoryStock::where('part_id',$request->id)->where('belong_to',1)->where('parts_model_id',$request->model_id)->sum('stock_in');
        $stock_out = InventoryStock::where('part_id',$request->id)->where('belong_to',1)->where('parts_model_id',$request->model_id)->sum('stock_out');
        $stock_in_hand = $stock_in - $stock_out;
        return response()->json([
            'stockIn'          => $stock_in_hand
        ]);
    }

    public function getPartsModel(Request $request){
        $partsModel=PartsModel::whereIn('part_id', $request->id)->with('part')->get();
        return response()->json([
            'partsModel'          => $partsModel
        ]);
    }

    public function getPartsStock(Request $request){
        //return $request->all();
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
        foreach($part_id_array as $key=>$pr_id){
            $model_id = $model_id_array[$key];

            $stock_in = InventoryStock::where('part_id',$pr_id)->where('parts_model_id',$model_id)->sum('stock_in');
            $stock_out = InventoryStock::where('part_id',$pr_id)->where('parts_model_id',$model_id)->sum('stock_out');

            $partsInfo=PartsModel::where('id', $model_id)->with('part')->first();
            $stock_in_hand = $stock_in - $stock_out;
            array_push($stock_collect,$stock_in_hand);
            array_push($partInfo_collect,$partsInfo);
        }

        $html = view('requisition.requisition.parts_info', compact('partInfo_collect','stock_collect'))->render();
        return response()->json(compact('html'));
        //return  $partInfo_collect;
    }
    //
    public function getPartsStockForOutlet(Request $request){

        $part_id = $request->parts_id;
        $store_id = $request->from_store_id;
        $part_id_array = [];
        $model_id_array = [];

        $rackbinInfo = [];
        $stock_collect = [];
        $partInfo_collect = [];
        foreach($part_id as $key=>$pr_id){
            $rackbin=RackBinManagement::where('parts_id',$pr_id)->where('store_id',$store_id)->first();
            $stock_in = InventoryStock::where('part_id',$pr_id)->where('store_id',$store_id)->sum('stock_in');
            $stock_out = InventoryStock::where('part_id',$pr_id)->where('store_id',$store_id)->sum('stock_out');

            $partsInfo=Parts::where('id', $pr_id)->first();
            $stock_in_hand = $stock_in - $stock_out;
            array_push($rackbinInfo, $rackbin);
            array_push($stock_collect,$stock_in_hand);
            array_push($partInfo_collect,$partsInfo);
        }
        $html = view('requisition.requisition.parts_info', compact('partInfo_collect','stock_collect','rackbinInfo'))->render();
        return response()->json(compact('html'));
    }

    public function getPartsStockForOutletEdit(Request $request)
    {
        $part_id = $request->parts_id;
        $requistion_id = $request->requistion_id;
        $store_id = $request->from_store_id;

        $old_parts_id = [];
        $previous_parts_id = RequisitionDetails::where('requisition_id', $requistion_id)->get();

        foreach($previous_parts_id as $key=>$parts_id){
            $id = $parts_id->parts_id;
            array_push($old_parts_id,$id);
        }

        $collectRequiredQuantity = [];
        foreach($part_id as $key => $id){

            if(in_array($id,$old_parts_id)){
                $required = RequisitionDetails::where('requisition_id',$requistion_id)->where('parts_id',$id)->select('required_quantity')->first();
                $data = $required->required_quantity;
            }else{
                $data = '';
            }
            array_push($collectRequiredQuantity,$data);
        }

        $part_id_array = [];
        $model_id_array = [];
        $stock_collect = [];
        $partInfo_collect = [];

        foreach($part_id as $key=>$pr_id){
            $stock_in = InventoryStock::where('part_id',$pr_id)->where('store_id',$store_id)->sum('stock_in');
            $stock_out = InventoryStock::where('part_id',$pr_id)->where('store_id',$store_id)->sum('stock_out');
            $partsInfo=Parts::where('id', $pr_id)->first();

            $stock_in_hand = $stock_in - $stock_out;
            array_push($stock_collect,$stock_in_hand);
            array_push($partInfo_collect,$partsInfo);
        }

        $html = view('requisition.requisition.edit_parts_info', compact('partInfo_collect','stock_collect', 'collectRequiredQuantity'))->render();
        return response()->json(compact('html'));
    }
    //Bulk Data Upload
    public function sampleExcel()
    {
        try{
        return Response::download(public_path('sample/part_model_sample_excel.xlsx', 'part_model_sample_excel.xlsx'));
        } catch (\Exception $e) {
            $bug = $e->getMessage();
            return redirect()->back()->with('error', $bug);
        }
    }

    public function import(Request $request)
    {
        try{
        Excel::import(new PartsModel, $request->file('import_file'));
        return redirect()->back()->with('success','Uploaded Successfully');
        } catch (\Exception $e) {
            $bug = $e->getMessage();
            return redirect()->back()->with('error', $bug);
        }
    }

    //parts model status active/inactive
    public function activeInactive($id)
    {
        try {
            $partsModel = PartsModel::findOrFail($id);

            if($partsModel->status == false) {
                $partsModel->update([
                    'status' => true
                ]);

                return back()->with('success', __('Parts model active now'));
            }elseif ($partsModel->status == true) {
                $partsModel->update([
                    'status' => false
                ]);

                return back()->with('success', __('Parts model inactive now'));
            }

            return back()->with('error', __('Action decline'));
        }catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}
