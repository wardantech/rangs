<?php

namespace App\Http\Controllers\Inventory;

use DB;
use Response;
use DataTables;
use App\Jobs\PartCsvProcess;
use Illuminate\Http\Request;
use App\Models\Inventory\Parts;
use Illuminate\Validation\Rule;
use App\Models\Inventory\Category;
use Illuminate\Support\Facades\Bus;
use App\Http\Controllers\Controller;
use App\Models\Inventory\PartsModel;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\Inventory\PartCategory;
use App\Models\Inventory\InventoryStock;

class PartsController extends Controller
{
    public function index()
    {
        try{
            $partsDetails = Parts::with('category')->latest();
            $categories = Category::where('status', 1)->orderBy('name')->get();
            if(request()->ajax()){
                return DataTables::of($partsDetails)
                            ->addColumn('productcategory', function ($partsDetails) {

                                if ($partsDetails->category !=null ) {
                                    $productcategory = $partsDetails->category->name;
                                } else {
                                    $productcategory ='null';
                                }
                                return $productcategory;
                                })
                                ->addColumn('partcategory', function ($partsDetails) {

                                    if ($partsDetails->partCategory !=null ) {
                                        $partCategory = $partsDetails->partCategory->name;
                                    } else {
                                        $partCategory ='null';
                                    }
                                    return $partCategory;
                                    })

                                ->addColumn('partmodel', function ($partsDetails) {

                                        if ($partsDetails->partModel !=null ) {
                                            $partModel = $partsDetails->partModel->name;
                                        } else {
                                            $partModel ='null';
                                        }
                                        return $partModel;
                                        })
                                ->addColumn('status', function ($partsDetails) {

                                    if ($partsDetails->status == true) {
                                        $status = '<div class="text-center">
                                            <a href="'.route('inventory.parts.status', $partsDetails->id).'" title="Status" class="btn btn-sm btn-success">
                                                <i class="fas fa-arrow-up"></i>
                                            </a>
                                        </div>';
                                    } else {
                                        $status ='<div class="text-center">
                                        <a href="'.route('inventory.parts.status', $partsDetails->id).'" title="Status" class="btn btn-sm btn-secondary">
                                            <i class="fas fa-arrow-down"></i>
                                        </a>
                                    </div>';
                                    }
                                    return $status;
                                    })

                                ->addColumn('types', function ($partsDetails) {

                                        if ($partsDetails->type == 1) {
                                            $type = 'General';
                                        } else {
                                            $type ='Special';
                                        }
                                        return $type;
                                })
                                
                                ->addColumn('action', function($partsDetails) {
                                        if(Auth::user()->can('edit') && Auth::user()->can('delete')) {
                                            return '<div class="table-actions text-center" style="display:flex">
                                            <a href="'.route('inventory.parts.edit', $partsDetails->id).'" title="Edit"><i class="ik ik-edit-2 f-16 mr-15 text-green"></i></a>
                                            <a type="submit" onclick="showDeleteConfirm(' . $partsDetails->id . ')" title="Delete"><i class="ik ik-trash-2 f-16 text-red"></i></a>
                                            </div>';
                                        }elseif(Auth::user()->can('edit')) {
                                            return '<div class="table-actions">
                                            <a href="'.route('inventory.parts.edit', $partsDetails->id).'" title="Edit"><i class="ik ik-edit-2 f-16 mr-15 text-green"></i></a>
                                            </div>';
                                        }elseif(Auth::user()->can('delete')) {
                                            return '<div class="table-actions">
                                            <a type="submit" onclick="showDeleteConfirm(' . $partsDetails->id . ')" title="Delete"><i class="ik ik-trash-2 f-16 text-red"></i></a>
                                            </div>';
                                        }
                                })
                                ->addIndexColumn()
                                ->rawColumns(['productcategory','partcategory','partmodel','action','types','status'])
                                ->make(true);
            }
            return view('inventory.parts.index',compact('partsDetails', 'categories'));
        } catch (\Exception $e) {
            $bug = $e->getMessage();
            return redirect()->back()->with('error', $bug);
        }
    }

    public function create()
    {
        try{
            $partCategories = PartCategory::where('status', 1)->orderBy('name')->get();
            $productCategories = Category::where('status', 1)->orderBy('name')->get();
            $partModels = PartsModel::where('status', 1)
                    ->orderBy('name')->get();
            return view('inventory.parts.create', [
                'productCategories' => $productCategories,
                'partCategories' => $partCategories,
                'partModels' => $partModels
            ]);
        } catch (\Exception $e) {
            $bug = $e->getMessage();
            return redirect()->back()->with('error', $bug);
        } 
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'name'         => 'required',
    		'part_category_id'    => 'required',
    		'product_category_id' => 'required',
    		'part_model_id'       => 'required',
            'code' => [
    		    'required',
                Rule::unique('parts')
                    ->where('part_category_id', $request->part_category_id)
                    ->where('code', $request->code)
            ],
            'unit'                => 'required',
            'type'                => 'required',
        ]);

        try {
            $parts = new Parts;
            $parts->product_category_id = $request->product_category_id;
            $parts->part_model_id       = $request->part_model_id;
            $parts->part_category_id    = $request->part_category_id;
            $parts->code                = $request->code;
            $parts->name                = $request->name;
            $parts->type                = $request->type;
            $parts->unit                = $request->unit;
            // $parts->status              = $request->status;
            $parts->created_by          = Auth::id();

            $parts->save();

            return redirect()->route('inventory.parts.index')->with('success', __('Part created successfully.'));
        } catch (\Exception $e) {
            $bug = $e->getMessage();
            return redirect()->back()->with('error', $bug);
        }


        return Response::json(['success' => true, 'message' =>''], 200);
    }

    public function edit($id)
    {
        try{
            $parts = Parts::findOrFail($id);
            $partCategories = PartCategory::where('status', 1)->orderBy('name')->get();
            $productCategories = Category::where('status', 1)->orderBy('name')->get();
            $partModels = PartsModel::where('status', 1)
                        ->orderBy('name')->get();

            return view('inventory.parts.edit', [
                'productCategories' => $productCategories,
                'partCategories' => $partCategories,
                'partModels' => $partModels,
                'parts' => $parts
            ]);
        } catch (\Exception $e) {
            $bug = $e->getMessage();
            return redirect()->back()->with('error', $bug);
        }
    }

    public function update(Request $request, $id)
    {
        $request->validate([
    		'name' => 'required',
    		'part_category_id' => 'required',
    		'product_category_id' => 'required',
    		'part_model_id' => 'required',
            'code' => 'required|unique:parts,code,' . $id,
        ]);

    try {

            $parts=Parts::find($id);
            $parts->product_category_id = $request->product_category_id;
            $parts->part_model_id       = $request->part_model_id;
            $parts->part_category_id    = $request->part_category_id;
            $parts->code                = $request->code;
            $parts->name                = $request->name;
            $parts->type                = $request->type;
            $parts->unit                = $request->unit;
            // $parts->status              = $request->status;
            $parts->updated_by          = Auth::id();
    
            $parts->save();

        return redirect()->route('inventory.parts.index')->with('success', __('Part updated successfully.'));
    } catch (\Exception $e) {

        $bug = $e->getMessage();
        return redirect()->back()->with('error', $bug);
    }
    }

    public function destroy(Request $request,$id)
    {
        if ($request->ajax()){
            try{
                $parts = Parts::findOrFail($id);
                $inventoryStock=InventoryStock::where('part_id',$parts->id)->get();
                if(count($inventoryStock) > 0){
                    return response()->json([
                        'success' => false,
                        'message' => "Sorry! Can't Delete. This Part is used in Inventory",
                    ]);
                }else{
                    $parts->delete();
                    return response()->json([
                        'success' => true,
                        'message' => 'Item Deleted Successfully.',
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
    }

    public function search(Request $request)
    {
        try{
            $id = $request->categoryId;
            $partsDetails = Parts::where('product_category_id', $id)
                            ->latest()->get();
            return view('inventory.parts.parts_category',compact('partsDetails'));
        } catch (\Exception $e) {
            $bug = $e->getMessage();
            return redirect()->back()->with('error', $bug);
        }
    }

    public function aciveInactive(Request $request, $id)
    {
        try {
            $parts = Parts::findOrFail($id);

            if($parts->status == false) {
                $parts->update([
                    'status' => true
                ]);

                return back()->with('success', __('Parts active now'));
            }elseif ($parts->status == true) {
                $parts->update([
                    'status' => false
                ]);

                return back()->with('success', __('Parts inactive now'));
            }

            return back()->with('error', __('Action decline'));
        }catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function sampleExcel(){
        return Response::download(public_path('sample/parts_sample_excel.xlsx', 'parts_sample_excel.xlsx'));
    }

    public function import(Request $request)
    {
        // request()->validate([
        //     'file' => 'required|mimes:csv,txt'
        // ]);
        try{
            Excel::import(new Parts, $request->file('import_file'));
            return back()->with('success', __('Data Uploaded Successfully'));
        }catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function parts(Request $request)
    {
        $input = $request->all();
        if (!empty($input['query'])) {

            $data = Parts::where('status', 1)
                ->where("code", "LIKE", "%{$input['query']}%")
                ->orWhere('name', "LIKE", "%{$input['query']}%")
                ->get();
        } else {

            $data = Parts::where('status', 1)
            ->limit(10)
            ->get();
        }

        $parts = [];

        if (count($data) > 0) {

            foreach ($data as $part) {
                $parts[] = array(
                    "id" => $part->id,
                    "text" => $part->code."-".$part->name."-".$part->partModel->name,
                );
            }
        }
        return response()->json($parts);
    }
    
}
