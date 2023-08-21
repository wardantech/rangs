<?php

namespace App\Http\Controllers\Inventory;

use Response;
use DataTables;
use Illuminate\Http\Request;
use App\Models\Inventory\Bin;
use App\Models\Inventory\Rack;
use App\Models\Inventory\Parts;
use App\Models\Inventory\Store;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\Inventory\RackBinManagement;

class RackBinManagementController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            $rackBinManagement = RackBinManagement::with('store', 'parts', 'rack', 'bin')
                ->latest();
            if (request()->ajax()) {
                return DataTables::of($rackBinManagement)

                    ->addColumn('storeName', function ($rackBinManagement) {
                        $data = isset($rackBinManagement->store) ? $rackBinManagement->store->name : null;
                        return $data;
                    })

                    ->addColumn('partName', function ($rackBinManagement) {
                        $data = isset($rackBinManagement->parts) ? $rackBinManagement->parts->name : null;
                        return $data;
                    })

                    ->addColumn('rack', function ($rackBinManagement) {
                        $data = isset($rackBinManagement->rack) ? $rackBinManagement->rack->name : null;
                        return $data;
                    })

                    ->addColumn('bin', function ($rackBinManagement) {
                        $data = isset($rackBinManagement->bin) ? $rackBinManagement->bin->name : null;
                        return $data;
                    })
                    ->addColumn('action', function ($rackBinManagement) {
                        if (Auth::user()->can('edit') && Auth::user()->can('delete')) {
                            return '<div class="table-actions text-center">
                                            <a href="' . route('inventory.rack-bin-management.edit', $rackBinManagement->id) . '" title="Edit"><i class="ik ik-edit-2 f-16 mr-15 text-green"></i></a>
                                            <a type="submit" onclick="showDeleteConfirm(' . $rackBinManagement->id . ')" title="Delete"><i class="ik ik-trash-2 f-16 text-red"></i></a>
                                            </div>';
                        } elseif (Auth::user()->can('edit')) {
                            return '<div class="table-actions">
                                            <a href="' . route('inventory.rack-bin-management.edit', $rackBinManagement->id) . '" title="Edit"><i class="ik ik-edit-2 f-16 mr-15 text-green"></i></a>
                                            </div>';
                        } elseif (Auth::user()->can('delete')) {
                            return '<div class="table-actions">
                                            <a type="submit" onclick="showDeleteConfirm(' . $rackBinManagement->id . ')" title="Delete"><i class="ik ik-trash-2 f-16 text-red"></i></a>
                                            </div>';
                        }
                    })
                    ->addIndexColumn()
                    ->rawColumns(['storeName', 'partName', 'rack', 'bin', 'action'])
                    ->make(true);
            }

            return view('inventory.rack_bin_management.index', compact('rackBinManagement'));
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
        try {

            $stores = Store::select('id', 'name', 'status')->where('status', 1)->get();

            return view('inventory.rack_bin_management.create', compact('stores'));
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
        $request->validate(
            [
                'store_id' => 'required',
                'parts_id' => [
                    'required',
                    Rule::unique('rack_bin_management')
                        ->where('parts_id', $request->parts_id)
                        ->where('rack_id', $request->rack_id)
                        ->where('bin_id', $request->bin_id)
                ],
                'rack_id' => 'required',
                'bin_id' => 'required',
            ],
            [
                'parts_id.unique' => 'Your selected parts already stored in rack or bin.'
            ]

        );

        try {
            RackBinManagement::create([
                'store_id' => $request->store_id,
                'parts_id' => $request->parts_id,
                'rack_id' => $request->rack_id,
                'bin_id' => $request->bin_id,
            ]);

            return redirect()->route('inventory.rack-bin-management.index')
                ->with('success', __('Successfully Assign Rack Bin For The Part'));
        } catch (\Exception $e) {
            $bug = $e->getMessage();
            return redirect()->back()->with('error', $bug);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\RackBinManagement  $rackBinManagement
     * @return \Illuminate\Http\Response
     */
    public function edit(RackBinManagement $rackBinManagement)
    {
        try {
            $racks = Rack::select('id', 'name', 'status')->where('status', 1)->get();
            $bins = Bin::select('id', 'name', 'status')->where('status', 1)->get();
            $parts = Parts::select('id', 'name', 'code', 'status')->where('status', 1)->get();
            $stores = Store::select('id', 'name', 'status')->where('status', 1)->get();

            return view('inventory.rack_bin_management.edit', compact('parts', 'stores', 'rackBinManagement', 'racks', 'bins'));
        } catch (\Exception $e) {
            $bug = $e->getMessage();
            return redirect()->back()->with('error', $bug);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\RackBinManagement  $rackBinManagement
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, RackBinManagement $rackBinManagement)
    {
        $request->validate(
            [
                'store_id' => 'required',
                'parts_id' => [
                    'required',
                    Rule::unique('rack_bin_management')
                        ->where('parts_id', $request->parts_id)
                        ->where('rack_id', $request->rack_id)
                        ->where('bin_id', $request->bin_id)
                        ->ignore($rackBinManagement->id)
                ],
                'rack_id' => 'required',
                'bin_id' => 'required',
            ],
            [
                'parts_id.unique' => 'Your selected parts already stored in rack or bin.'
            ]
        );

        try {
            $rackBinManagement->update([
                'store_id' => $request->store_id,
                'parts_id' => $request->parts_id,
                'rack_id' => $request->rack_id,
                'bin_id' => $request->bin_id,
            ]);

            return redirect()->route('inventory.rack-bin-management.index')
                ->with('success', __('Successfully Update Assign Rack Bin For The Part'));
        } catch (\Exception $e) {
            $bug = $e->getMessage();
            return redirect()->back()->with('error', $bug);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\RackBinManagement  $rackBinManagement
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $rackBinManagement = RackBinManagement::find($id);
            if ($rackBinManagement != null) {
                $rackBinManagement->delete();
                return response()->json([
                    'success' => true,
                    'message' => "Successfully Deleted Assigned Rack Bin For The Part",
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
        // Bulk Entry
        public function sampleExcel()
        {
            try{
            return Response::download(public_path('sample/rack_bin_sample_excel.xlsx', 'part_category_sample_excel.xlsx'));
            } catch (\Exception $e) {
                $bug = $e->getMessage();
                return redirect()->back()->with('error', $bug);
            }
        }

        public function import(Request $request)
        {
            try{
            Excel::import(new RackBinManagement, $request->file('import_file'));
            return redirect()->back()->with('success','Uploaded Successfully');
            } catch (\Exception $e) {
                $bug = $e->getMessage();
                return redirect()->back()->with('error', $bug);
            }
        }
}
