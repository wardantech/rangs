<?php

namespace App\Http\Controllers\Inventory;

use Response;
use Illuminate\Http\Request;
use App\Rules\BrandNameCheck;
use App\Models\Inventory\Brand;
use Illuminate\Validation\Rule;
use App\Models\Inventory\Category;
use App\Models\Product\BrandModel;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\ProductPurchase\Purchase;
use Yajra\DataTables\Facades\DataTables;

class BrandController extends Controller
{
     public function index()
     {
        try{
            $brands = Brand::with('category')->latest();
            if (request()->ajax()) {
                return DataTables::of($brands)

                    ->addColumn('brandcategory', function ($brands) {
                            $data = isset($brands->category) ? $brands->category->name : null;
                            return $data;
                    })

                    ->addColumn('status', function ($brands) {

                        if ($brands->status == true) {
                            $status = '<div class="text-center">
                                            <a href="' . route('product.brand.status', $brands->id) . '" title="Status" class="btn btn-sm btn-success">
                                                <i class="fas fa-arrow-up"></i>
                                            </a>
                                        </div>';
                        } else {
                            $status = '<div class="text-center">
                                        <a href="' . route('product.brand.status', $brands->id) . '" title="Status" class="btn btn-sm btn-secondary">
                                            <i class="fas fa-arrow-down"></i>
                                        </a>
                                    </div>';
                        }
                        return $status;
                    })

                    ->addColumn('action', function ($brands) {
                        if (Auth::user()->can('edit') && Auth::user()->can('delete')) {
                            return '<div class="table-actions text-center">
                                            <a href="' . route('product.brand.edit', $brands->id) . '" title="Edit"><i class="ik ik-edit-2 f-16 mr-15 text-green"></i></a>
                                            <a type="submit" onclick="showDeleteConfirm(' . $brands->id . ')" title="Delete"><i class="ik ik-trash-2 f-16 text-red"></i></a>
                                            </div>';
                        } elseif (Auth::user()->can('edit')) {
                            return '<div class="table-actions">
                                            <a href="' . route('product.brand.edit', $brands->id) . '" title="Edit"><i class="ik ik-edit-2 f-16 mr-15 text-green"></i></a>
                                            </div>';
                        } elseif (Auth::user()->can('delete')) {
                            return '<div class="table-actions">
                                            <a type="submit" onclick="showDeleteConfirm(' . $brands->id . ')" title="Delete"><i class="ik ik-trash-2 f-16 text-red"></i></a>
                                            </div>';
                        }
                    })
                    ->addIndexColumn()
                    ->rawColumns(['brandcategory', 'action', 'status'])
                    ->make(true);
            }
            return view('inventory.brand.index',compact('brands'));
        } catch (\Exception $e) {
            $bug = $e->getMessage();
            return redirect()->back()->with('error', $bug);
        }
     }

    public function create()
    {
        try{
            $brands = Brand::latest()->get();
            $categories = Category::where('status', 1)->orderBy('name')->get();
            return view('inventory.brand.create',compact('brands','categories'));
        } catch (\Exception $e) {
            $bug = $e->getMessage();
            return redirect()->back()->with('error', $bug);
        }
    }

     public function store(Request $request)
     {
        $request->validate([
    		'category_id' => 'required',
    		'name' => [
    		    'required',
                Rule::unique('brands')
                    ->where('product_category_id', $request->category_id)
                    ->where('name', $request->name)
            ],
            'code' => 'required|unique:brands',
        ]);

        try{
            Brand::create([
                'product_category_id' => $request->category_id,
                'name' => $request->name,
                'code' => $request->code
            ]);
            return redirect()->route('product.brand.index')
                    ->with('success', 'Brand Created Successfully');
        } catch (\Exception $e) {
            $bug = $e->getMessage();
            return redirect()->back()->with('error', $bug);
        }
    }

    public function edit(Request $request,$id)
    {
        try{
            $brand = Brand::findOrFail($id);
            $categories = Category::where('status', 1)->select('name','id', 'status')
                        ->orderBy('name')
                        ->get();
            return view('inventory.brand.edit',compact('brand','categories'));
        } catch (\Exception $e) {
            $bug = $e->getMessage();
            return redirect()->back()->with('error', $bug);
        }
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'category_id' => 'required',
    		'name' => [
    		    'required',
                Rule::unique('brands')
                    ->where('product_category_id', $request->category_id)
                    ->where('name', $request->name)
                    ->ignore($id)
            ],
            'code' => 'required|unique:brands,code,'. $id,
        ]);
        try{
            Brand::findOrFail($id)->update([
                'product_category_id' => $request->category_id,
                'name' => $request->name,
                'code' => $request->code,
            ]);
            return redirect()->route('product.brand.index')
                    ->with('success', 'Brand updated successfully');
        } catch (\Exception $e) {
            $bug = $e->getMessage();
            return redirect()->back()->with('error', $bug);
        }
    }

    public function destroy($id)
    {
        try {
            $brand = Brand::findOrFail($id);
            $brandmodels = BrandModel::where('brand_id', $brand->id)->get();
            $purchase = Purchase::where('brand_id', $brand->id)->get();
            if (count($brandmodels) > 0 || count($purchase) > 0) {
                return response()->json([
                    'success' => false,
                    'message' => "Sorry! Can't Delete. This Brand is used in Brand Model / Purchase Management",
                ]);
            } else {
                $brand->delete();
                return response()->json([
                    'success' => true,
                    'message' => 'Brand Deleted Successfully.',
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

    public function activeInactive($id)
    {
        try {
            $brand = Brand::findOrFail($id);

            if ($brand->status == false) {
                $brand->update([
                    'status' => true
                ]);
                return back()->with('success', __('Brand active now'));
            } elseif ($brand->status == true) {
                $brand->update([
                    'status' => false
                ]);
                return back()->with('success', __('Brand inactive now'));
            }

            return back()->with('error', __('Action decline'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }


    public function sampleExcel()
    {
        try{
            return Response::download(public_path('sample/brand_sample_excel.xlsx', 'brand_sample_excel.xlsx'));
        } catch (\Exception $e) {
            $bug = $e->getMessage();
            return redirect()->back()->with('error', $bug);
        }
    }

    public function import(Request $request)
    {
        try{
            Excel::import(new Brand, $request->file('import_file'));
            return back();
        } catch (\Exception $e) {
            $bug = $e->getMessage();
            return redirect()->back()->with('error', $bug);
        }
    }

}
