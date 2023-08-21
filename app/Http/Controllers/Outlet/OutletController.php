<?php

namespace App\Http\Controllers\Outlet;

use DB;
use Session;
use Illuminate\Http\Request;
use App\Models\Outlet\Outlet;
use App\Models\Inventory\Store;
use App\Models\Inventory\Thana;
use Yajra\DataTables\DataTables;
use App\Models\Employee\Employee;
use App\Models\Inventory\District;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class OutletController extends Controller
{
    public function index()
    {
        try{
            $outlets = Outlet::latest();
            if (request()->ajax()) {
                return DataTables::of($outlets)

                    ->addColumn('status', function ($outlets) {

                        if ($outlets->status == true) {
                            $status = '<div class="text-center">
                                            <a href="' . route('general.outlet.status', $outlets->id) . '" title="Status" class="btn btn-sm btn-success">
                                                <i class="fas fa-arrow-up"></i>
                                            </a>
                                        </div>';
                        } else {
                            $status = '<div class="text-center">
                                        <a href="' . route('general.outlet.status', $outlets->id) . '" title="Status" class="btn btn-sm btn-secondary">
                                            <i class="fas fa-arrow-down"></i>
                                        </a>
                                    </div>';
                        }
                        return $status;
                    })

                    ->addColumn('action', function ($outlets) {
                        if (Auth::user()->can('edit') && Auth::user()->can('delete')) {
                            return '<div class="table-actions text-center">
                                            <a href="' . route('general.outlet.edit', $outlets->id) . '" title="Edit"><i class="ik ik-edit-2 f-16 mr-15 text-green"></i></a>
                                            <a type="submit" onclick="showDeleteConfirm(' . $outlets->id . ')" title="Delete"><i class="ik ik-trash-2 f-16 text-red"></i></a>
                                            </div>';
                        } elseif (Auth::user()->can('edit')) {
                            return '<div class="table-actions">
                                            <a href="' . route('general.outlet.edit', $outlets->id) . '" title="Edit"><i class="ik ik-edit-2 f-16 mr-15 text-green"></i></a>
                                            </div>';
                        } elseif (Auth::user()->can('delete')) {
                            return '<div class="table-actions">
                                            <a type="submit" onclick="showDeleteConfirm(' . $outlets->id . ')" title="Delete"><i class="ik ik-trash-2 f-16 text-red"></i></a>
                                            </div>';
                        }
                    })
                    ->addIndexColumn()
                    ->rawColumns(['action', 'status'])
                    ->make(true);
            }
            return view('outlet.index', compact('outlets'));
        } catch (\Exception $e) {
            $bug = $e->getMessage();
            return redirect()->back()->with('error', $bug);
        }
    }

    public function create()
    {
        try{
            $districts = District::orderBy('name')->get();
            return view('outlet.create', compact('districts'));
        } catch (\Exception $e) {
            $bug = $e->getMessage();
            return redirect()->back()->with('error', $bug);
        }
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|string|unique:outlets,name,NULL,id,deleted_at,NULL',
            'code' => 'required|string|unique:outlets,code,NULL,id,deleted_at,NULL',
            'district_id' => 'nullable|numeric',
            'thana_id' => 'nullable|array',
            'address' => 'required',
            'outlet_owner_name' => 'required',
            'market' => 'required',
            'mobile' => 'required|min:11|max:11',
            'outlet_owner_address' => 'required'
        ]);

        try {
            $outlet = $request->all();
            $outlet['thana_id'] = json_encode($request->thana_id);
            Outlet::create($outlet);
            return redirect()->route('general.outlet.index')
                    ->with('success', __('New branch created successfully.'));
        } catch (\Exception $e) {
            $bug = $e->getMessage();
            return redirect()->back()->with('error', $bug);
        }
    }

    public function show($id)
    {
        try{
            $outlet = Outlet::with('district')->findOrFail($id);
            $thanas = Thana::orderBy('name')->get();
            return view('outlet.show', compact('outlet', 'thanas'));
        } catch (\Exception $e) {
            $bug = $e->getMessage();
            return redirect()->back()->with('error', $bug);
        }
    }

    public function edit($id)
    {
        try{
            Session::forget('tahanaIds');

            $outlet = Outlet::findOrFail($id);
            $districts = District::all();
            $districtId = $outlet->district_id;
            $thanaId = json_decode($outlet->thana_id);
            $thanas = Thana::where('district_id', $districtId)->orderBy('name')->get();

            if (!empty($thanaId)) {
                foreach($thanaId as $id) {
                    Session::push('tahanaIds', $id);
                }
            }


            return view('outlet.edit', compact('outlet', 'districts', 'thanas'));
        } catch (\Exception $e) {
            $bug = $e->getMessage();
            return redirect()->back()->with('error', $bug);
        }
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'name' => 'required|unique:outlets,name,' . $id,
            'code' => 'required|unique:outlets,code,' . $id,
            'district_id' => 'nullable|numeric',
            'thana_id' => 'nullable|array',
            'address' => 'required',
            'outlet_owner_name' => 'required',
            'market' => 'required',
            'mobile' => 'required|min:11|max:11',
            'outlet_owner_address' => 'required'
        ]);

        try {
            $updatedOutlet = $request->all();
            $updatedOutlet['thana_id'] = json_encode($request->thana_id);
            $outlet = Outlet::findOrFail($id);
            $outlet->update($updatedOutlet);

            Session::forget('tahanaIds');
            return redirect()->route('general.outlet.index')
                ->with('success', __('Branch updated successfully.'));
        } catch (\Exception $e) {
            $bug = $e->getMessage();
            return redirect()->back()->with('error', $bug);
        }
    }

    public function destroy($id)
    {
        try {
            $outlet=Outlet::findOrFail($id);
            $employee=Employee::where('outlet_id', $outlet->id)->get();
            $store=Store::where('outlet_id', $outlet->id)->get();
            if(count($employee) > 0 || count($store) > 0){
                return response()->json([
                    'success' => false,
                    'message' => "Sorry! Can't Delete. This Branch is used in Employee/Store Management",
                ]);
            }else{
                $outlet->delete();
                return response()->json([
                    'success' => true,
                    'message' => 'Branch Deleted Successfully.',
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
            $outlet = Outlet::findOrFail($id);

            if($outlet->status == false) {
                $outlet->update([
                    'status' => true
                ]);

                return back()->with('success', __('Outlet active now'));
            }elseif ($outlet->status == true) {
                $outlet->update([
                    'status' => false
                ]);

                return back()->with('success', __('Outlet inactive now'));
            }

            return back()->with('error', __('Action decline'));
        }catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}
