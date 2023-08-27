<?php

namespace App\Http\Controllers\Inventory;

use DB;
use Auth;
use Response;
use DataTables;
use Illuminate\Http\Request;
use App\Models\Inventory\Thana;
use App\Models\Inventory\District;
use App\Models\Inventory\Inventory;
use App\Http\Controllers\Controller;
use App\Models\Inventory\ProductSourcingVendor;

class ProductSourcingVendorController extends Controller
{
    public function index()
    {
        $productSourcingVendors = ProductSourcingVendor::with('district', 'thana')->orderBy('id','desc');
        if (request()->ajax()) {
            return DataTables::of($productSourcingVendors)

                ->addColumn('districtName', function ($productSourcingVendors) {
                    $data = isset($productSourcingVendors->district) ? $productSourcingVendors->district->name : null;
                    return $data;
                })

                ->addColumn('thanaName', function ($productSourcingVendors) {
                    $data = isset($productSourcingVendors->thana) ? $productSourcingVendors->thana->name : null;
                    return $data;
                })

                ->addColumn('status', function ($productSourcingVendors) {

                    if ($productSourcingVendors->status == true) {
                        $status = '<div class="text-center">
                                            <a href="' . route('general.product-sourcing-vendor.status', $productSourcingVendors->id) . '" title="Status" class="btn btn-sm btn-success">
                                                <i class="fas fa-arrow-up"></i>
                                            </a>
                                        </div>';
                    } else {
                        $status = '<div class="text-center">
                                        <a href="' . route('general.product-sourcing-vendor.status', $productSourcingVendors->id) . '" title="Status" class="btn btn-sm btn-secondary">
                                            <i class="fas fa-arrow-down"></i>
                                        </a>
                                    </div>';
                    }
                    return $status;
                })

                ->addColumn('action', function ($productSourcingVendors) {
                    if (Auth::user()->can('edit') && Auth::user()->can('delete')) {
                        return '<div class="table-actions text-center" style="display: flex;">
                                            <a href="' . route('general.product-sourcing-vendor.edit', $productSourcingVendors->id) . '" title="Edit"><i class="ik ik-edit-2 f-16 mr-15 text-green"></i></a>
                                            <a type="submit" onclick="showDeleteConfirm(' . $productSourcingVendors->id . ')" title="Delete"><i class="ik ik-trash-2 f-16 text-red"></i></a>
                                            </div>';
                    } elseif (Auth::user()->can('edit')) {
                        return '<div class="table-actions">
                                            <a href="' . route('general.product-sourcing-vendor.edit', $productSourcingVendors->id) . '" title="Edit"><i class="ik ik-edit-2 f-16 mr-15 text-green"></i></a>
                                            </div>';
                    } elseif (Auth::user()->can('delete')) {
                        return '<div class="table-actions">
                                            <a type="submit" onclick="showDeleteConfirm(' . $productSourcingVendors->id . ')" title="Delete"><i class="ik ik-trash-2 f-16 text-red"></i></a>
                                            </div>';
                    }
                })
                ->addIndexColumn()
                ->rawColumns(['status', 'action', 'districtName', 'thanaName'])
                ->make(true);
        }

        return view('inventory.product_sourcing_vendor.index', compact('productSourcingVendors'));
    }

    public function create()
    {
        $districts = District::orderBy('name')->get();
        $upazilas = Thana::orderBy('name')->get();
        return view('inventory.product_sourcing_vendor.create', [
            'districts' => $districts,
            'upazilas' => $upazilas
        ]);
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|string|unique:product_sourcing_vendors,name,NULL,id,deleted_at,NULL',
            'code' => 'required|unique:product_sourcing_vendors,code,NULL,id,deleted_at,NULL',
            'grade' => 'required',
            'phone' => 'required|min:11|max:11|regex:/(01)[0-9]{9}/|',
            'email' => 'required|email',
            'address' => 'required|string',
        ]);

        $productSourcingVendor = new ProductSourcingVendor;

        $productSourcingVendor->name        = $request->name;
        $productSourcingVendor->address     = $request->address;
        $productSourcingVendor->phone       = $request->phone;
        $productSourcingVendor->email       = $request->email;
        $productSourcingVendor->code        = $request->code;
        $productSourcingVendor->grade       = $request->grade;
        $productSourcingVendor->district_id = $request->district_id;
        $productSourcingVendor->thana_id    = $request->thana_id;

        $productSourcingVendor->save();

        return redirect()->route('general.product-sourcing-vendor.index')
                ->with('success', 'Vendor Create Successfully');
    }

    public function edit($id)
    {
        $product_sourcing_vendor=ProductSourcingVendor::findOrFail($id);
        $districts=District::orderBy('name')->get();
        $thanas=Thana::orderBy('name')->get();
        return view('inventory.product_sourcing_vendor.edit', [
            'product_sourcing_vendor' => $product_sourcing_vendor,
            'districts'               => $districts,
            'thanas'                => $thanas
        ]);
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'name' => 'required|string|unique:product_sourcing_vendors,name,' . $id,
            'code' => 'required|unique:product_sourcing_vendors,code,' . $id,
            'grade' => 'required',
            'phone' => 'required|min:11|max:11|regex:/(01)[0-9]{9}/|',
            'email' => 'required|email',
            'address' => 'required|string',
        ]);

        $productSourcingVendor=ProductSourcingVendor::findOrFail($request->id);

        $productSourcingVendor->name        = $request->name;
        $productSourcingVendor->address     = $request->address;
        $productSourcingVendor->phone       = $request->phone;
        $productSourcingVendor->email       = $request->email;
        $productSourcingVendor->code        = $request->code;
        $productSourcingVendor->grade       = $request->grade;
        $productSourcingVendor->district_id = $request->district_id;
        $productSourcingVendor->thana_id    = $request->thana_id;
        $productSourcingVendor->update();

        return redirect()->route('general.product-sourcing-vendor.index')
                ->with('success', 'Vendor Updated Successfully');
    }

    public function destroy($id)
    {
        try{
            $productSourcingVendor=ProductSourcingVendor::findOrFail($id);
            $Inventory=Inventory::where('vendor_id',$productSourcingVendor->id)->get();
            if(count($Inventory) > 0){
                return response()->json([
                    'success' => false,
                    'message' => "Sorry! Can't Delete. This Product Sourcing Vendor is used in Inventory Management",
                ]);
            }else{
                $productSourcingVendor->delete();
                return response()->json([
                    'success' => true,
                    'message' => "Product Sourcing Vendor deleted successfully",
                ]);
            }
        } catch (\Exception $e) {
            $bug = $e->getMessage();
            return response()->json([
                'success' => true,
                'message' => $bug,
            ]);
        }

    }

    public function getThana($id)
    {
        $thanas = DB::table('thanas')->where('thanas.district_id', $id)
                ->orderBy('name')->get();

        return response()->json($thanas);
    }

    public function activeInactive($id)
    {
        try {
            $productSourcingVendor = ProductSourcingVendor::findOrFail($id);

            if($productSourcingVendor->status == false) {
                $productSourcingVendor->update([
                    'status' => true
                ]);

                return back()->with('success', __('Product sourcing vendor active now'));
            }elseif ($productSourcingVendor->status == true) {
                $productSourcingVendor->update([
                    'status' => false
                ]);
                return back()->with('success', __('Product sourcing vendor inactive now'));
            }

            return back()->with('error', __('Action decline'));
        }catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}
