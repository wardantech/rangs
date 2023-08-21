<?php

namespace App\Http\Controllers\Inventory;

use DB;
use Response;
use Validator;
use DataTables;
use Illuminate\Http\Request;
use App\Models\Outlet\Outlet;
use App\Models\Inventory\Rack;
use App\Models\Inventory\Store;
use App\Models\Employee\Employee;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class StoreController extends Controller
{
    public function index()
    {
        try{
            $stores = Store::with('outlet', 'user')->latest();
            if (request()->ajax()) {
                return DataTables::of($stores)

                    ->addColumn('outletbranch', function ($stores) {
                        $branch = isset($stores->outlet) ? $stores->outlet->name : null;
                        return $branch;
                    })

                    ->addColumn('status', function ($stores) {
                        if ($stores->status == true) {
                            $status = '<div class="text-center">
                                            <a href="' . route('inventory.store.status', $stores->id) . '" title="Status" class="btn btn-sm btn-success">
                                                <i class="fas fa-arrow-up"></i>
                                            </a>
                                        </div>';
                        } else {
                            $status = '<div class="text-center">
                                        <a href="' . route('inventory.store.status', $stores->id) . '" title="Status" class="btn btn-sm btn-secondary">
                                            <i class="fas fa-arrow-down"></i>
                                        </a>
                                    </div>';
                        }
                        return $status;
                    })

                    ->addColumn('action', function ($stores) {
                        if (Auth::user()->can('edit') && Auth::user()->can('delete')) {
                            return '<div class="table-actions text-center">
                                            <a href="' . route('inventory.store.edit', $stores->id) . '" title="Edit"><i class="ik ik-edit-2 f-16 mr-15 text-green"></i></a>
                                            <a type="submit" onclick="showDeleteConfirm(' . $stores->id . ')" title="Delete"><i class="ik ik-trash-2 f-16 text-red"></i></a>
                                            </div>';
                        } elseif (Auth::user()->can('edit')) {
                            return '<div class="table-actions">
                                            <a href="' . route('inventory.store.edit', $stores->id) . '" title="Edit"><i class="ik ik-edit-2 f-16 mr-15 text-green"></i></a>
                                            </div>';
                        } elseif (Auth::user()->can('delete')) {
                            return '<div class="table-actions">
                                            <a type="submit" onclick="showDeleteConfirm(' . $stores->id . ')" title="Delete"><i class="ik ik-trash-2 f-16 text-red"></i></a>
                                            </div>';
                        }
                    })
                    ->addIndexColumn()
                    ->rawColumns(['outletbranch', 'action', 'status'])
                    ->make(true);
            }
            return view('inventory.store.index', compact('stores'));
        } catch (\Exception $e) {
            $bug = $e->getMessage();
            return redirect()->back()->with('error', $bug);
        }

    }

    public function create()
    {
        try{
            $outlets = Outlet::where('status', 1)->orderBy('name')->get();
            $stores = Store::where('status', 1)->orderBy('name')->get();

            return view('inventory.store.create', [
                'outlets' => $outlets,
                'stores'  => $stores
            ]);
        } catch (\Exception $e) {
            $bug = $e->getMessage();
            return redirect()->back()->with('error', $bug);
        }
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'outlet_id' => 'required',
            'name' => 'required|unique:stores,name,NULL,id,deleted_at,NULL',
            'address' => 'required'
        ]);
        try{
            $store=Store::where('outlet_id',$request->outlet_id)->first();
            if($store!=null){
                return redirect()->back()->with('error', 'Whoops! Sorry The Seleted outlet has a store already');
            }else{
                $store=new Store;
                $store->outlet_id = $request->outlet_id;
                $store->name    = $request->name;
                $store->address = $request->address;

                $store->save();

                return redirect()->route('inventory.store.index')->with('success', 'Store added Successfully.');
            }

        } catch (\Exception $e) {
            $bug = $e->getMessage();
            return redirect()->back()->with('error', $bug);
        }

    }

    public function edit($id)
    {
        try{
            $outlets = Outlet::where('status', 1)->orderBy('name')->get();
            $store = Store::findOrFail($id);

            return view('inventory.store.edit', [
                'outlets' => $outlets,
                'store'   => $store
            ]);
        } catch (\Exception $e) {
            $bug = $e->getMessage();
            return redirect()->back()->with('error', $bug);
        }
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'outlet_id' => 'required',
            'name' => 'required|unique:stores,name,' . $id,
            'address' => 'required'
        ]);
        try{
            $store=Store::findOrFail($request->id);

            $store->outlet_id = $request->outlet_id;
            $store->name = $request->name;
            $store->address = $request->address;
            $store->update();

            return redirect()->route('inventory.store.index')
                    ->with('success', 'Store updated Successfully.');
        } catch (\Exception $e) {
            $bug = $e->getMessage();
            return redirect()->back()->with('error', $bug);
        }
    }

    public function destroy($id)
    {
        try{
            $store = Store::findOrFail($id);
            $Employee=Employee::where('store_id',$store->id)->get();
            $rack=Rack::where('store_id',$store->id)->get();
            if(count($Employee) > 0 || count($rack)>0 ){
                return response()->json([
                    'success' => false,
                    'message' => "Sorry! Can't Delete. This Store is used in Employee / Rack Management",
                ]);
            }else{
                $store->delete();
                return response()->json([
                    'success' => true,
                    'message' => 'Store Deleted Successfully.',
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
            $store = Store::findOrFail($id);

            if($store->status == 0) {
                $store->update([
                    'status' => 1
                ]);

                return back()->with('success', __('Store active now'));
            }elseif ($store->status == 1) {
                $store->update([
                    'status' => 0
                ]);

                return back()->with('success', __('Store inactive now'));
            }

            return back()->with('error', __('Action decline'));
        }catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}
