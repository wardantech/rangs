<?php

namespace App\Http\Controllers\Inventory;

use DB;
use Response;
use DataTables;
use Illuminate\Http\Request;
use App\Models\Inventory\Thana;
use App\Models\Employee\Employee;
use App\Models\Inventory\District;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\Inventory\ServiceSourcingVendor;

class ServiceSourcingVendorController extends Controller
{
    public function index()
    {
        try{
            $serviceSourcingVendors = ServiceSourcingVendor::with('district', 'thana')->orderBy('id','desc');
            if (request()->ajax()) {
                return DataTables::of($serviceSourcingVendors)

                    ->addColumn('districtName', function ($serviceSourcingVendors) {
                        $data = isset($serviceSourcingVendors->district) ?          $serviceSourcingVendors->district->name : null;
                        return $data;
                    })

                    ->addColumn('thanaName', function ($serviceSourcingVendors) {
                        $data = isset($serviceSourcingVendors->thana) ? $serviceSourcingVendors->thana->name : null;
                        return $data;
                    })

                    ->addColumn('status', function ($serviceSourcingVendors) {

                        if ($serviceSourcingVendors->status == true) {
                            $status = '<div class="text-center">
                                            <a href="' . route('general.service-sourcing-vendor.status', $serviceSourcingVendors->id) . '" title="Status" class="btn btn-sm btn-success">
                                                <i class="fas fa-arrow-up"></i>
                                            </a>
                                        </div>';
                        } else {
                            $status = '<div class="text-center">
                                        <a href="' . route('general.service-sourcing-vendor.status', $serviceSourcingVendors->id) . '" title="Status" class="btn btn-sm btn-secondary">
                                            <i class="fas fa-arrow-down"></i>
                                        </a>
                                    </div>';
                        }
                        return $status;
                    })

                    ->addColumn('action', function ($serviceSourcingVendors) {
                        if (Auth::user()->can('edit') && Auth::user()->can('delete')) {
                            return '<div class="table-actions text-center" style="display: flex;">
                                            <a href="' . route('general.service-sourcing-vendor.edit', $serviceSourcingVendors->id) . '" title="Edit"><i class="ik ik-edit-2 f-16 mr-15 text-green"></i></a>
                                            <a type="submit" onclick="showDeleteConfirm(' . $serviceSourcingVendors->id . ')" title="Delete"><i class="ik ik-trash-2 f-16 text-red"></i></a>
                                            </div>';
                        } elseif (Auth::user()->can('edit')) {
                            return '<div class="table-actions">
                                            <a href="' . route('general.service-sourcing-vendor.edit', $serviceSourcingVendors->id) . '" title="Edit"><i class="ik ik-edit-2 f-16 mr-15 text-green"></i></a>
                                            </div>';
                        } elseif (Auth::user()->can('delete')) {
                            return '<div class="table-actions">
                                            <a type="submit" onclick="showDeleteConfirm(' . $serviceSourcingVendors->id . ')" title="Delete"><i class="ik ik-trash-2 f-16 text-red"></i></a>
                                            </div>';
                        }
                    })
                    ->addIndexColumn()
                    ->rawColumns(['districtName', 'thanaName', 'status','action'])
                    ->make(true);
            }

            return view('inventory.service_sourcing_vendor.index', compact('serviceSourcingVendors'));
        } catch (\Exception $e) {
            $bug = $e->getMessage();
            return redirect()->back()->with('error', $bug);
        }
    }

    public function create()
    {
        try{
            $districts = District::orderBy('name')->get();
            $upazilas = Thana::orderBy('name')->get();
            return view('inventory.service_sourcing_vendor.create', compact('districts', 'upazilas'));
        } catch (\Exception $e) {
            $bug = $e->getMessage();
            return redirect()->back()->with('error', $bug);
        }
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|string|unique:service_sourcing_vendors,name,NULL,id,deleted_at,NULL',
            'code' => 'required|unique:service_sourcing_vendors,code,NULL,id,deleted_at,NULL',
            'grade' => 'required',
            'phone' => 'required|min:11|max:11|regex:/(01)[0-9]{9}/|',
            'email' => 'required|email',
            'address' => 'nullable|string',
        ]);

        try{
            $service_sourcing_vendor = new ServiceSourcingVendor;

            $service_sourcing_vendor->name        = $request->name;
            $service_sourcing_vendor->address     = $request->address;
            $service_sourcing_vendor->phone       = $request->phone;
            $service_sourcing_vendor->email       = $request->email;
            $service_sourcing_vendor->code        = $request->code;
            $service_sourcing_vendor->grade       = $request->grade;
            $service_sourcing_vendor->district_id = $request->district_id;
            $service_sourcing_vendor->thana_id    = $request->thana_id;
            $service_sourcing_vendor->save();

            return redirect()->route('general.service-sourcing-vendor.index')
                ->with('success', 'Service Sourcing Vendor Created successfully.');
        } catch (\Exception $e) {
            $bug = $e->getMessage();
            return redirect()->back()->with('error', $bug);
        }
    }

    public function edit($id)
    {
        try{
            $service_sourcing_vendor = ServiceSourcingVendor::find($id);
            $districts = District::orderBy('name')->get();
            $thanas = Thana::orderBy('name')->get();
            return view('inventory.service_sourcing_vendor.edit', compact(
                'service_sourcing_vendor',
                'districts',
                'thanas'
            ));
        } catch (\Exception $e) {
            $bug = $e->getMessage();
            return redirect()->back()->with('error', $bug);
        }
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'name' => 'required|string|unique:service_sourcing_vendors,name,' . $id,
            'code' => 'required|unique:service_sourcing_vendors,code,' . $id,
            'grade' => 'required',
            'phone' => 'required|min:11|max:11|regex:/(01)[0-9]{9}/|',
            'email' => 'required|email',
            'address' => 'nullable|string',
        ]);

        try{
            $service_sourcing_vendor=ServiceSourcingVendor::find($id);

            $service_sourcing_vendor->name        = $request->name;
            $service_sourcing_vendor->address     = $request->address;
            $service_sourcing_vendor->phone       = $request->phone;
            $service_sourcing_vendor->email       = $request->email;
            $service_sourcing_vendor->code        = $request->code;
            $service_sourcing_vendor->grade       = $request->grade;
            $service_sourcing_vendor->district_id = $request->district_id;
            $service_sourcing_vendor->thana_id    = $request->thana_id;
            $service_sourcing_vendor->update();

            return redirect()->route('general.service-sourcing-vendor.index')
                        ->with('success', 'Service Sourcing Vendor Updated Successfully.');
        } catch (\Exception $e) {
            $bug = $e->getMessage();
            return redirect()->back()->with('error', $bug);
        }
    }

    public function destroy($id)
    {
        try{
            $service_sourcing_vendor = ServiceSourcingVendor::find($id);
            $Employee=Employee::where('vendor_id',$service_sourcing_vendor->id)->get();
            if(count($Employee) > 0){
                return response()->json([
                    'success' => false,
                    'message' => "Sorry! Can't Delete. This Service Sourcing Vendor is used in Employee Management",
                ]);
            }else{
                $service_sourcing_vendor->delete();
                return response()->json([
                    'success' => true,
                    'message' => 'Service Sourcing Vendor deleted successfully',
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

    public function getThana($id)
    {
        $thanas = DB::table('thanas')
            ->where('district_id', $id)
            ->orderBy('name')
            ->get();

         return response()->json($thanas);
    }

    public function getMultiThana(Request $request)
    {
        $getId = (array) $request->district_id;
        $thanas = DB::table('thanas')
            ->whereIn('district_id', $getId)
            ->orderBy('name')->get();

         return response()->json($thanas);
    }

    public function activeInactive($id)
    {
        try {
            $serviceSourcingVendor = ServiceSourcingVendor::findOrFail($id);

            if($serviceSourcingVendor->status == false) {
                $serviceSourcingVendor->update([
                    'status' => true
                ]);

                return back()->with('success', __('Service sourcing vendor active now'));
            }elseif ($serviceSourcingVendor->status == true) {
                $serviceSourcingVendor->update([
                    'status' => false
                ]);

                return back()->with('success', __('Service sourcing vendor inactive now'));
            }

            return back()->with('error', __('Action decline'));
        }catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}
