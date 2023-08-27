<?php

namespace App\Http\Controllers\Inventory;

use Illuminate\Http\Request;
use App\Models\Inventory\Bin;
use App\Models\Inventory\Rack;
use App\Models\Inventory\Store;
use Yajra\DataTables\DataTables;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class RackController extends Controller
{
    public function index()
    {
        try{
            $racks = Rack::with('store')->latest();
            if (request()->ajax()) {
                return DataTables::of($racks)

                    ->addColumn('storename', function ($racks) {
                        $data = isset($racks->store) ? $racks->store->name : null;
                        return $data;
                    })

                    ->addColumn('status', function ($racks) {

                        if ($racks->status == true) {
                            $status = '<div class="text-center">
                                            <a href="' . route('inventory.racks.status', $racks->id) . '" title="Status" class="btn btn-sm btn-success">
                                                <i class="fas fa-arrow-up"></i>
                                            </a>
                                        </div>';
                        } else {
                            $status = '<div class="text-center">
                                        <a href="' . route('inventory.racks.status', $racks->id) . '" title="Status" class="btn btn-sm btn-secondary">
                                            <i class="fas fa-arrow-down"></i>
                                        </a>
                                    </div>';
                        }
                        return $status;
                    })

                    ->addColumn('action', function ($racks) {
                        if (Auth::user()->can('edit') && Auth::user()->can('delete')) {
                            return '<div class="table-actions text-center">
                                            <a href="' . route('inventory.racks.edit', $racks->id) . '" title="Edit"><i class="ik ik-edit-2 f-16 mr-15 text-green"></i></a>
                                            <a type="submit" onclick="showDeleteConfirm(' . $racks->id . ')" title="Delete"><i class="ik ik-trash-2 f-16 text-red"></i></a>
                                            </div>';
                        } elseif (Auth::user()->can('edit')) {
                            return '<div class="table-actions">
                                            <a href="' . route('inventory.racks.edit', $racks->id) . '" title="Edit"><i class="ik ik-edit-2 f-16 mr-15 text-green"></i></a>
                                            </div>';
                        } elseif (Auth::user()->can('delete')) {
                            return '<div class="table-actions">
                                            <a type="submit" onclick="showDeleteConfirm(' . $racks->id . ')" title="Delete"><i class="ik ik-trash-2 f-16 text-red"></i></a>
                                            </div>';
                        }
                    })
                    ->addIndexColumn()
                    ->rawColumns(['storename', 'action', 'status'])
                    ->make(true);
            }

            return view('inventory.rack.index', compact('racks'));
        } catch (\Exception $e) {
            $bug = $e->getMessage();
            return redirect()->back()->with('error', $bug);
        }
    }

    public function create()
    {
        try{
            $stores = Store::where('status', 1)->orderBy('name')->get();

            return view('inventory.rack.create', [
                'stores' => $stores
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
            'name' => 'required|unique:racks,name,NULL,id,deleted_at,NULL',
        ]);
        try{
            $rack=new Rack;
            $rack->store_id = $request->store_id;
            $rack->name = $request->name;
            $rack->save();

            return redirect()->route('inventory.racks.index')
                    ->with('success', 'Rack Added Successfully.');
        } catch (\Exception $e) {
            $bug = $e->getMessage();
            return redirect()->back()->with('error', $bug);
        }
    }

    public function edit($id)
    {
        try{
            $stores = Store::where('status', 1)->orderBy('name')->get();
            $rack=Rack::find($id);
            return view('inventory.rack.edit', [
                'stores' => $stores,
                'rack'   => $rack
            ]);
        } catch (\Exception $e) {
            $bug = $e->getMessage();
            return redirect()->back()->with('error', $bug);
        }
    }

    public function update(Request $request)
    {
        $this->validate($request, [
            'store_id' => 'required',
            'name' => 'required|unique:racks,name,' . $request->id
        ]);
        try{
            $rack=Rack::find($request->id);
            $rack->store_id = $request->store_id;
            $rack->name     = $request->name;
            $rack->save();

            return redirect()->route('inventory.racks.index')
                    ->with('success', 'Rack updated successfully.');
        } catch (\Exception $e) {
            $bug = $e->getMessage();
            return redirect()->back()->with('error', $bug);
        }
    }

    public function destroy($id)
    {
        try{
            $rack = Rack::find($id);
            $bin=Bin::where('rack_id',$rack->id)->get();
            if(count($bin) > 0){
                return response()->json([
                    'success' => false,
                    'message' => "Sorry! Can't Delete. This Rack is used in Bin Management",
                ]);
            }else{
                $rack->delete();
                return response()->json([
                    'success' => true,
                    'message' => 'Rack Deleted Successfully.',
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
            $rack = Rack::findOrFail($id);

            if($rack->status == true) {
                $rack->update([
                    'status' => false
                ]);

                return back()->with('success', __('Rack inactive now'));
            }elseif ($rack->status == false) {
                $rack->update([
                    'status' => true
                ]);

                return back()->with('success', __('Rack active now'));
            }

            return back()->with('error', __('Action decline'));
        }catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}
