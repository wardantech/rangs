<?php

namespace App\Http\Controllers\Product;

use Session;
use Redirect;
use Response;
use Validator;
use DataTables;
use Illuminate\Http\Request;
use App\Models\Inventory\Brand;
use App\Models\Inventory\Category;
use App\Models\Product\BrandModel;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\ProductPurchase\Purchase;

class BrandModelController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try{
            $brandmodels = BrandModel::with('brand','category')->latest();
            if (request()->ajax()) {
                return DataTables::of($brandmodels)

                    ->addColumn('brandname', function ($brandmodels) {
                        $data = isset($brandmodels->brand) ? $brandmodels->brand->name : null;
                            return $data;
                    })

                    ->addColumn('brandcategory', function ($brandmodels) {
                            $data = isset($brandmodels->category) ? $brandmodels->category->name : null;
                            return $data;
                    })

                    ->addColumn('status', function ($brandmodels) {

                        if ($brandmodels->status == true) {
                            $status = '<div class="text-center">
                                            <a href="' . route('product.brand_model.status', $brandmodels->id) . '" title="Status" class="btn btn-sm btn-success">
                                                <i class="fas fa-arrow-up"></i>
                                            </a>
                                        </div>';
                        } else {
                            $status = '<div class="text-center">
                                        <a href="' . route('product.brand_model.status', $brandmodels->id) . '" title="Status" class="btn btn-sm btn-secondary">
                                            <i class="fas fa-arrow-down"></i>
                                        </a>
                                    </div>';
                        }
                        return $status;
                    })

                    ->addColumn('action', function ($brandmodels) {
                        if (Auth::user()->can('edit') && Auth::user()->can('delete')) {
                            return '<div class="table-actions text-center">
                                            <a href="' . route('product.brand_model.edit', $brandmodels->id) . '" title="Edit"><i class="ik ik-edit-2 f-16 mr-15 text-green"></i></a>
                                            <a type="submit" onclick="showDeleteConfirm(' . $brandmodels->id . ')" title="Delete"><i class="ik ik-trash-2 f-16 text-red"></i></a>
                                            </div>';
                        } elseif (Auth::user()->can('edit')) {
                            return '<div class="table-actions">
                                            <a href="' . route('product.brand_model.edit', $brandmodels->id) . '" title="Edit"><i class="ik ik-edit-2 f-16 mr-15 text-green"></i></a>
                                            </div>';
                        } elseif (Auth::user()->can('delete')) {
                            return '<div class="table-actions">
                                            <a type="submit" onclick="showDeleteConfirm(' . $brandmodels->id . ')" title="Delete"><i class="ik ik-trash-2 f-16 text-red"></i></a>
                                            </div>';
                        }
                    })
                    ->addIndexColumn()
                    ->rawColumns(['brandname','brandcategory', 'action', 'status'])
                    ->make(true);
            }
            return view('product.index',compact('brandmodels'));
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
            $categories = Category::where('status', 1)->orderBy('name')->pluck('name','id')->toArray();
            return view('product.create',compact('categories'));
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
        Session::put('brand_id', $request->brand_id);


        $this->validate($request, [
            'category_id' => 'required',
            'brand_id' => 'required',
            'model_name'=>'required|unique:brand_models,model_name,NULL,id,deleted_at,NULL',
            'code'=>'required|unique:brand_models,code,NULL,id,deleted_at,NULL',
        ]);

        DB::beginTransaction();

        try{
            BrandModel::create([
                'product_category_id' =>  $request->category_id,
                'brand_id' =>  $request->brand_id,
                'model_name' =>  $request->model_name,
                'code' =>  $request->code,
                'created_by' => Auth::id(),
            ]);

            DB::commit();
            Session::forget('brand_id');
            return redirect('product/brand_model')
            ->with('success', __('label.NEW_BRAND_MODEL_CREATED'));
        }catch(\Exception $e){
            DB::rollback();
            return redirect()->back()->with('error','Something Went Wrong!');
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
            $partsModel=BrandModel::findOrFail($id);
            $categories=Category::where('status', 1)->orderBy('name')->pluck('name','id')->toArray();
            $brands=Brand::pluck('name','id')->toArray();
            return view('product.edit',compact('partsModel','categories','brands'));
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
            'product_category_id' => 'required',
            'brand_id' => 'required',
            'model_name' => 'required|unique:brand_models,model_name,' . $id,
            'code' => 'required|unique:brand_models,code,'. $id,
        ]);

        DB::beginTransaction();

        try{
            $partsModel=BrandModel::findOrFail($id);
            $partsModel->update([
                'product_category_id' =>  $request->product_category_id,
                'brand_id' =>  $request->brand_id,
                'model_name' =>  $request->model_name,
                'code' =>  $request->code,
                'updated_by' => Auth::id(),
            ]);

            DB::commit();
            return redirect('product/brand_model')
            ->with('success', __('label.BRAND_MODEL_UPDATED'));
        }catch(\Exception $e){
            DB::rollback();
            return redirect()->back()->with('error','Something Went Wrong!');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        if ($request->ajax()) {
            try {
                $BrandModel = BrandModel::findOrFail($id);
                $purchases = Purchase::where('brand_model_id', $BrandModel->id)->get();
                if (count($purchases) > 0) {
                    return response()->json([
                        'success' => false,
                        'message' => "Sorry! Can't Delete. This Brand Model is used in Purchase Management",
                    ]);
                } else {
                    $BrandModel->delete();
                    return response()->json([
                        'success' => true,
                        'message' => 'Brand Model Deleted Successfully.',
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

    public function sampleExcel(){
        try{
            return Response::download(public_path('sample/brand_model_sample_excel.xlsx', 'brand_model_sample_excel.xlsx'));
        } catch (\Exception $e) {
            $bug = $e->getMessage();
            return redirect()->back()->with('error', $bug);
        }
    }

    public function import(Request $request)
    {
        // dd($request->file('import_file'));
        try{
            Excel::import(new BrandModel, $request->file('import_file'));
            return back();
        } catch (\Exception $e) {
            $bug = $e->getMessage();
            return redirect()->back()->with('error', $bug);
        }
    }

    public function activeInactive($id)
    {
        try {
            $brandModel = BrandModel::findOrFail($id);

            if($brandModel->status == false) {
                $brandModel->update([
                    'status' => true
                ]);
                return back()->with('success', __('Brand model active now'));
            }elseif ($brandModel->status == true) {
                $brandModel->update([
                    'status' => false
                ]);
                return back()->with('success', __('Brand model inactive now'));
            }

            return back()->with('error', __('Action decline'));
        }catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}
