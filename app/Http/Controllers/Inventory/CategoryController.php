<?php

namespace App\Http\Controllers\Inventory;

use Response;
use Illuminate\Http\Request;
use App\Models\Inventory\Brand;
use Yajra\DataTables\DataTables;
use App\Models\Inventory\Category;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\ProductPurchase\Purchase;

class CategoryController extends Controller
{
    public function index()
    {
        try {
            $categories = Category::orderBy('id', 'desc');
            if (request()->ajax()) {
                return DataTables::of($categories)

                    ->addColumn('status', function ($categories) {
                        if ($categories->status == true) {
                            $status = '<div class="text-center">
                                            <a href="' . route('product.category.status', $categories->id) . '" title="Status" class="btn btn-sm btn-success">
                                                <i class="fas fa-arrow-up"></i>
                                            </a>
                                        </div>';
                        } else {
                            $status = '<div class="text-center">
                                        <a href="' . route('product.category.status', $categories->id) . '" title="Status" class="btn btn-sm btn-secondary">
                                            <i class="fas fa-arrow-down"></i>
                                        </a>
                                    </div>';
                        }
                        return $status;
                    })

                    ->addColumn('action', function ($categories) {
                        if (Auth::user()->can('edit') && Auth::user()->can('delete')) {
                            return '<div class="table-actions text-center">
                                            <a  onclick="editCategory(' . $categories->id . ')" class="edit-btn" data-id="' . $categories->id . '"  data-name="' . $categories->id . '" data-toggle="modal" data-target="#categoryModal"><i class="ik ik-edit f-16 mr-15 text-blue" aria-hidden="true"></i></a>

                                            <a type="submit" onclick="showDeleteConfirm(' . $categories->id . ')" title="Delete"><i class="ik ik-trash-2 f-16 text-red"></i></a>
                                            </div>';
                        } elseif (Auth::user()->can('edit')) {
                            return '<div class="table-actions">
                                            <a  onclick="editCategory(' . $categories->id . ')" class="edit-btn" data-id="' . $categories->id . '"  data-name="' . $categories->id . '" data-toggle="modal" data-target="#categoryModal"><i class="ik ik-edit f-16 mr-15 text-blue" aria-hidden="true"></i></a>

                                            </div>';
                        } elseif (Auth::user()->can('delete')) {
                            return '<div class="table-actions">
                                            <a type="submit" onclick="showDeleteConfirm(' . $categories->id . ')" title="Delete"><i class="ik ik-trash-2 f-16 text-red"></i></a>
                                            </div>';
                        }
                    })
                    ->addIndexColumn()
                    ->rawColumns(['status', 'action'])
                    ->make(true);
            }
            return view('inventory.category.index', compact('categories'));
        } catch (\Exception $e) {
            $bug = $e->getMessage();
            return redirect()->back()->with('error', $bug);
        }
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:categories,name',
        ]);

        try {
            Category::create([
                'name' => $request->name,
            ]);
            return redirect()->back()->with('success', 'Category created successfully');
        } catch (\Exception $e) {
            $bug = $e->getMessage();
            return redirect()->back()->with('error', $bug);
        }
    }

    public function edit(Request $request)
    {
        $category  = Category::findOrfail($request->id);
        return Response()->json($category);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|unique:categories,name,' . $id,
        ]);
        try {
            Category::findOrFail($request->category_id)->update([
                'name' => $request->name,
            ]);

            return redirect()->route('product.category.index')->with('success', 'Category updated successfully');
        } catch (\Exception $e) {
            $bug = $e->getMessage();
            return redirect()->back()->with('error', $bug);
        }
    }

    public function destroy($id)
    {
        try {
            $category = Category::findOrFail($id); //Product Category
            $brand = Brand::where('product_category_id', $category->id)->get();
            $purchase = Purchase::where('product_category_id', $category->id)->get();
            $Ticket = DB::table('tickets')
                ->where('deleted_at', NULL)
                ->where('product_category_id', 'LIKE', '%' . $category->id . '%')
                ->get();

            if (count($brand) > 0 || count($purchase) > 0 || count($Ticket) > 0) {
                return response()->json([
                    'success' => false,
                    'message' => "Sorry! Can't Delete. This Category is used in Brand / Purchase  Management / Ticket",
                ]);
            } else {
                $category->delete();
                return response()->json([
                    'success' => true,
                    'message' => "Category deleted successfully.",
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

    public function sampleExcel()
    {
        try {
            return Response::download(public_path('sample/product_category_sample_excel.xlsx', 'product_category_sample_excel.xlsx'));
        } catch (\Exception $e) {
            $bug = $e->getMessage();
            return redirect()->back()->with('error', $bug);
        }
    }

    public function import(Request $request)
    {
        try {
            Excel::import(new Category, $request->file('import_file'));
            return back()->with('success', __('Data Uploaded Successfully'));
        } catch (\Exception $e) {
            $bug = $e->getMessage();
            return redirect()->back()->with('error', $bug);
        }
    }

    public function activeInactive($id)
    {
        try {
            $category = Category::findOrFail($id);
            if ($category->status == false) {
                $category->update([
                    'status' => true,
                ]);

                return back()->with('success', __('Category active now'));
            } elseif ($category->status == true) {
                $category->update([
                    'status' => false,
                ]);

                return back()->with('success', __('Category inactive now'));
            }

            return back()->with('error', __('Action decline'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
    
    public function cateGories(Request $request)
    {
        $input = $request->all();
        if (!empty($input['query'])) {
            $data = Category::select(["id","name"])
                ->where('status', 1)
                ->where("name", "LIKE", "%{$input['query']}%")
                ->get();
        } else {
            $data = Category::select(["id","name"])
            ->where('status', 1)
            ->limit(10)
            ->get();
        }

        $categories = [];
        if (count($data) > 0) {
            foreach ($data as $category) {
                $categories[] = array(
                    "id" => $category->id,
                    "text" => $category->name,
                );
            }
        }
        return response()->json($categories);
    }
}
