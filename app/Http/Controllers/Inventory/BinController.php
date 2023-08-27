<?php

namespace App\Http\Controllers\Inventory;

use DB;
use Response;
use Illuminate\Http\Request;
use App\Models\Inventory\Bin;
use App\Models\Inventory\Rack;
use App\Models\Inventory\Store;
use Yajra\DataTables\DataTables;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\Inventory\InventoryStock;
use App\Models\Inventory\RackBinManagement;

class BinController extends Controller
{
    public function index()
    {
        try{
            $bins = Bin::with('store', 'rack')->latest();
            if (request()->ajax()) {
                return DataTables::of($bins)

                    ->addColumn('storeName', function ($bins) {
                        $data = isset($bins->store) ? $bins->store->name : null;
                        return $data;
                    })

                    ->addColumn('rackName', function ($bins) {
                        $data = isset($bins->rack) ? $bins->rack->name : null;
                        return $data;
                    })

                    ->addColumn('status', function ($bins) {

                        if ($bins->status == true) {
                            $status = '<div class="text-center">
                                            <a href="' . route('inventory.bins.status', $bins->id) . '" title="Status" class="btn btn-sm btn-success">
                                                <i class="fas fa-arrow-up"></i>
                                            </a>
                                        </div>';
                        } else {
                            $status = '<div class="text-center">
                                        <a href="' . route('inventory.bins.status', $bins->id) . '" title="Status" class="btn btn-sm btn-secondary">
                                            <i class="fas fa-arrow-down"></i>
                                        </a>
                                    </div>';
                        }
                        return $status;
                    })

                    ->addColumn('action', function ($bins) {
                        if (Auth::user()->can('edit') && Auth::user()->can('delete')) {
                            return '<div class="table-actions text-center">
                                            <a href="' . route('inventory.bins.edit', $bins->id) . '" title="Edit"><i class="ik ik-edit-2 f-16 mr-15 text-green"></i></a>
                                            <a type="submit" onclick="showDeleteConfirm(' . $bins->id . ')" title="Delete"><i class="ik ik-trash-2 f-16 text-red"></i></a>
                                            </div>';
                        } elseif (Auth::user()->can('edit')) {
                            return '<div class="table-actions">
                                            <a href="' . route('inventory.bins.edit', $bins->id) . '" title="Edit"><i class="ik ik-edit-2 f-16 mr-15 text-green"></i></a>
                                            </div>';
                        } elseif (Auth::user()->can('delete')) {
                            return '<div class="table-actions">
                                            <a type="submit" onclick="showDeleteConfirm(' . $bins->id . ')" title="Delete"><i class="ik ik-trash-2 f-16 text-red"></i></a>
                                            </div>';
                        }
                    })
                    ->addIndexColumn()
                    ->rawColumns(['storeName', 'rackName', 'status','action'])
                    ->make(true);
            }
            return view('inventory.bin.index', compact('bins'));
        } catch (\Exception $e) {
            $bug = $e->getMessage();
            return redirect()->back()->with('error', $bug);
        }
    }

    public function create()
    {
        try{
            $stores = Store::where('status', 1)->orderBy('name')->get();
            $racks = Rack::where('status', 1)->orderBy('name')->get();
            return view('inventory.bin.create', [
                'stores' => $stores,
                'racks'  => $racks
            ]);
        } catch (\Exception $e) {
            $bug = $e->getMessage();
            return redirect()->back()->with('error', $bug);
        }
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'store_id' => 'required',
            'rack_id' => 'required',
            'name' => 'required|unique:bins,name,NULL,id,deleted_at,NULL',
        ]);

        try{
            $bin=new Bin;
            $bin->store_id = $request->store_id;
            $bin->rack_id  = $request->rack_id;
            $bin->name     = $request->name;
            $bin->save();

            return redirect()->route('inventory.bins.index')
                ->with('success', 'New bin added successfully.');
        } catch (\Exception $e) {
            $bug = $e->getMessage();
            return redirect()->back()->with('error', $bug);
        }
    }

    public function edit($id)
    {
        try{
            $bin = Bin::findOrFail($id);
            $stores = Store::where('status', 1)->orderBy('name')->get();
            $racks = Rack::where('store_id',$bin->store_id)->where('status', 1)->orderBy('name')->get();
            return view('inventory.bin.edit', [
                'bin'    =>$bin,
                'stores' => $stores,
                'racks'  => $racks
            ]);
        } catch (\Exception $e) {
            $bug = $e->getMessage();
            return redirect()->back()->with('error', $bug);
        }
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'store_id' => 'required',
            'rack_id' => 'required',
            'name' => 'required|unique:bins,name,' . $id,
        ]);

        try{
            $bin = Bin::findOrFail($id);
            $bin->store_id = $request->store_id;
            $bin->rack_id = $request->rack_id;
            $bin->name = $request->name;
            $bin->save();

            return redirect()->route('inventory.bins.index')
                    ->with('success', 'Bin updated successfully.');
        } catch (\Exception $e) {
            $bug = $e->getMessage();
            return redirect()->back()->with('error', $bug);
        }
    }

    public function destroy($id)
    {
        try{
            $bin = Bin::findOrFail($id);
            $rackBinManagement=RackBinManagement::where('bin_id',$bin->id)->get();
            $inventoryStock=InventoryStock::where('bin_id',$bin->id)->get();
            if(count($rackBinManagement) > 0 || count($inventoryStock) > 0){
                return response()->json([
                    'success' => false,
                    'message' => "Sorry! Can't Delete. This bin is used in Rack Bin Management / Inventory Receive Section",
                ]);
            }else{
                $bin->delete();
                return response()->json([
                    'success' => true,
                    'message' => 'Bin Deleted Successfully.',
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

    public function getRack($id)
    {
        $racks = DB::table('racks')
            ->where('status', 1)
            ->where('racks.store_id', $id)
            ->where('deleted_at', null)
            ->orderBy('name')
            ->get();
        return response()->json($racks);
    }

    public function getBin($id)
    {
        $bins = DB::table('bins')
            ->where('status', 1)
            ->where('bins.rack_id', $id)
            ->where('deleted_at', null)
            ->orderBy('name')
            ->get();

        return response()->json($bins);
    }
    //Bin Bulk Entry
    public function sampleExcel()
    {
        try{
        return Response::download(public_path('sample/bin_sample_excel.xlsx', 'bin_sample_excel.xlsx'));
        } catch (\Exception $e) {
            $bug = $e->getMessage();
            return redirect()->back()->with('error', $bug);
        }
    }

    public function import(Request $request)
    {
        try{
        Excel::import(new Bin, $request->file('import_file'));
        return redirect()->back()->with('success','Uploaded Successfully');
        } catch (\Exception $e) {
            $bug = $e->getMessage();
            return redirect()->back()->with('error', $bug);
        }
    }

    public function activeInactive($id)
    {
        try {
            $bin = Bin::findOrFail($id);

            if($bin->status == false) {
                $bin->update([
                    'status' => true
                ]);

                return back()->with('success', __('Bin active now'));
            }elseif ($bin->status == true) {
                $bin->update([
                    'status' => false
                ]);

                return back()->with('success', __('Bin inactive now'));
            }

            return back()->with('error', __('Action decline'));
        }catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}
