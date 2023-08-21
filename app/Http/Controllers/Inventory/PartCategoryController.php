<?php

namespace App\Http\Controllers\Inventory;

use DB;
use Response;
use DataTables;
use Illuminate\Http\Request;
use App\Models\Inventory\Parts;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\Inventory\PartCategory;


class PartCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try{
            $partCategories=PartCategory::latest()->get();
            // $partCategories=PartCategory::latest()->paginate(15);
            // return view('inventory.part_category.index', compact('partCategories'));
            if(request()->ajax()){
                return DataTables::of($partCategories)
                ->addColumn('status', function ($partCategories) {

                                    if ($partCategories->status == true) {
                                        $status = '<div class="text-center">
                                            <a href="'.route('inventory.part-category.status', $partCategories->id).'" title="Status" class="btn btn-sm btn-success">
                                                <i class="fas fa-arrow-up"></i>
                                            </a>
                                        </div>';
                                    } else {
                                        $status ='<div class="text-center">
                                        <a href="'.route('inventory.part-category.status', $partCategories->id).'" title="Status" class="btn btn-sm btn-secondary">
                                            <i class="fas fa-arrow-down"></i>
                                        </a>
                                    </div>';
                                    }
                                    return $status;
                                })

                                ->addColumn('action', function ($partCategories) {
                                    if(Auth::user()->can('edit') && Auth::user()->can('delete')) {
                                        return '<div class="table-actions">
                                        <a href="'.route('inventory.part-category.edit', $partCategories->id).'" title="Edit"><i class="ik ik-edit-2 f-16 mr-15 text-green"></i></a>
                                        <a type="submit" onclick="showDeleteConfirm(' . $partCategories->id . ')" title="Delete"><i class="ik ik-trash-2 f-16 text-red"></i></a>
                                        </div>';
                                    }elseif(Auth::user()->can('edit')) {
                                        return '<div class="table-actions">
                                        <a href="'.route('inventory.part-category.edit', $partCategories->id).'" title="Edit"><i class="ik ik-edit-2 f-16 mr-15 text-green"></i></a>
                                        </div>';
                                    }elseif(Auth::user()->can('delete')) {
                                        return '<div class="table-actions">
                                        <a type="submit" onclick="showDeleteConfirm(' . $partCategories->id . ')" title="Delete"><i class="ik ik-trash-2 f-16 text-red"></i></a>
                                        </div>';
                                    }
                                })
                                ->addIndexColumn()
                                ->rawColumns(['action','status'])
                                ->make(true);
            }
            return view('inventory.part_category.index');
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
        $request->validate([
    		'name' => 'required|unique:part_categories,name,NULL,id,deleted_at,NULL',
        ]);

        try{
            PartCategory::create($request->all());
            return redirect()->back()->with('success', 'Part Category inserted successfully');
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
            $partCategory = PartCategory::findOrFail($id);
            return view('inventory.part_category.edit', compact('partCategory'));
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
        $request->validate([
    		'name' => 'required|unique:part_categories,name,' . $id,
        ]);

        try{
            PartCategory::findOrFail($id)->update([
                'name' => $request->name,
            ]);
            return redirect()->route('inventory.part-category.index')->with('success', 'Part category updated successfully');
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
    public function destroy(Request $request,$id)
    {
        if ($request->ajax()){
            try {
                $PartCategory=PartCategory::findOrFail($id);
                $partsDetails = Parts::where('part_category_id',$PartCategory->id)->get();
                if(count($partsDetails) > 0){
                    return response()->json([
                        'success' => false,
                        'message' => "Sorry! Can't Delete. This Part Category is used in Part Management",
                    ]);
                }else{
                    $PartCategory->delete();
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

    public function getPartModel($id)
    {
        $partModels=DB::table('parts_models')
                ->where('parts_models.part_category_id', $id)
                ->where('parts_models.status', true)
                ->where('deleted_at', null)
                ->orderBy('name')
                ->get();

        return response()->json([
            'partModels' => $partModels
        ]);
    }

    public function getPart($id){
        // $parts = Parts::where('part_category_id', $request->part_category_id)->get();
        $parts=DB::table('parts')
                ->where('status', 1)
                ->where('parts.part_model_id', $id)->get();

        return response()->json([
            'parts' => $parts
        ]);
    }

    public function sampleExcel()
    {
        try{
        return Response::download(public_path('sample/part_category_sample_excel.xlsx', 'part_category_sample_excel.xlsx'));
        } catch (\Exception $e) {
            $bug = $e->getMessage();
            return redirect()->back()->with('error', $bug);
        }
    }

    public function import(Request $request)
    {
        try{
        Excel::import(new PartCategory, $request->file('import_file'));
        return redirect()->back()->with('success','Uploaded Successfully');
        } catch (\Exception $e) {
            $bug = $e->getMessage();
            return redirect()->back()->with('error', $bug);
        }
    }

    public function aciveInactive(Request $request, $id)
    {
        try {
            $partCategory = PartCategory::findOrFail($id);

            if($partCategory->status == 0) {
                $partCategory->update([
                    'status' => 1
                ]);

                return back()->with('success', __('Part Category Status Update Successfully.'));
            }elseif ($partCategory->status == 1) {
                $partCategory->update([
                    'status' => 0
                ]);

                return back()->with('success', __('Part Category Status Update Successfully.'));
            }

            return back()->with('error', __('Action decline'));
        }catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}
