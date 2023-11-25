<?php

namespace App\Http\Controllers\ProductPurchase;

use Session;
use Redirect;
use Response;
use Validator;
use DataTables;
use Illuminate\Http\Request;
use App\Models\Outlet\Outlet;
use App\Models\Ticket\Ticket;
use App\Models\Inventory\Brand;
use App\Models\Inventory\Fault;
use App\Models\Customer\Customer;
use App\Models\Employee\Employee;
use App\Models\Inventory\Category;
use App\Models\Product\BrandModel;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\Inventory\PartsCategory;
use App\Models\ProductPurchase\Purchase;
use PHPUnit\Framework\Constraint\IsTrue;

class ProductPurchaseController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        try {
            if (request()->ajax()) {
                $purchases = DB::table('purchases')->join('outlets','outlets.id','=', 'purchases.outlet_id')
                ->join('customers','customers.id','=', 'purchases.customer_id')
                ->join('categories','categories.id','=', 'purchases.product_category_id')
                ->join('brands','brands.id','=', 'purchases.brand_id')
                ->join('brand_models','brand_models.id','=', 'purchases.brand_model_id')
                ->select(
                    'purchases.id',
                    'purchases.product_serial',
                    'purchases.invoice_number',
                    'outlets.name as outlet_name',
                    'customers.name as customer_name',
                    'customers.mobile as customer_mobile',
                    'categories.name as category_name',
                    'brands.name as brand_name',
                    'brand_models.model_name',
                )
                ->orderBy('purchases.id', 'desc');

                return DataTables::of($purchases)

                    ->addColumn('customer_name', function ($purchases) {
                        return $purchases->customer_name;
                    })
                    ->addColumn('customer_mobile', function ($purchases) {
                        return $purchases->customer_mobile;
                    })
                    ->addColumn('category_name', function ($purchases) {
                        $data = isset($purchases->category_name) ? $purchases->category_name : null;
                        return $data;
                    })
                    ->addColumn('brand_name', function ($purchases) {
                        $data = isset($purchases->brand_name) ? $purchases->brand_name : null;
                        return $data;
                    })
                    ->addColumn('model_name', function ($purchases) {
                        $data = isset($purchases->model_name) ? $purchases->model_name : null;
                        return $data;
                    })
                    ->addColumn('outlet_name', function ($purchases) {
                        $data = isset($purchases->outlet_name) ? $purchases->outlet_name : null;
                        return $data;
                    })

                    ->addColumn('action', function ($purchases) {
                        if (Auth::user()->can('edit') && Auth::user()->can('delete') && Auth::user()->can('show')) {
                            return '<div class="table-actions text-center" style="display: flex;">
                                            <a href="' . route('product.purchase.show', $purchases->id) . '" title="Show"><i class="ik ik-eye f-16 mr-15 text-green"></i></a>
                                            <a href="' . route('product.purchase.edit', $purchases->id) . '" title="Edit"><i class="ik ik-edit-2 f-16 mr-15 text-blue"></i></a>
                                            <a type="submit" onclick="showDeleteConfirm(' . $purchases->id . ')" title="Delete"><i class="ik ik-trash-2 f-16 text-red"></i></a>
                                            </div>';
                        } elseif (Auth::user()->can('edit')) {
                            return '<div class="table-actions">
                                            <a href="' . route('product.purchase.edit', $purchases->id) . '" title="Edit"><i class="ik ik-edit-2 f-16 mr-15 text-blue"></i></a>
                                            </div>';
                        } elseif (Auth::user()->can('show')) {
                            return '<div class="table-actions">
                                            <a href="' . route('product.purchase.show', $purchases->id) . '" title="show"> <i class="ik ik-eye f-16 mr-15 text-green"></i></a>
                                            </div>';
                        } elseif (Auth::user()->can('delete')) {
                            return '<div class="table-actions">
                                            <a type="submit" onclick="showDeleteConfirm(' . $purchases->id . ')" title="Delete"><i class="ik ik-trash-2 f-16 text-red"></i></a>
                                            </div>';
                        }
                    })
                    ->addIndexColumn()
                    ->rawColumns(['action'])
                    ->make(true);
            }

            return view('purchase.index');
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
            $brands = Brand::orderBy('name')->pluck('name', 'id')->toArray();
            $categories = Category::where('status', 1)->orderBy('name')->pluck('name', 'id')->toArray();
            $customers = Customer::orderBy('name')->get();
            $outlets = Outlet::where('status', 1)->orderBy('name')->pluck('name', 'id')->toArray();
            return view('purchase.create', compact('brands', 'categories', 'customers', 'outlets'));
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
            'customer_id' => 'required',
            'category_id' => 'required',
            'brand_id' => 'required',
            'model_id' => 'required',
            'product_serial' => 'nullable|unique:purchases,product_serial,NULL,id,deleted_at,NULL',
            'invoice_number' => 'nullable|unique:purchases,invoice_number,NULL,id,deleted_at,NULL',
            'purchase_date' => 'required',
            'own_stock' => 'required',
            'outlet_id' => 'nullable',
            'general_warranty_date' => 'required',
            'special_warranty_date' => 'required',
            'service_warranty_date' => 'required'
        ]);

        DB::beginTransaction();

        try {
            if ($request->own_stock == 1) {
                $employee = Employee::where('user_id', Auth::user()->id)->first();
                if ($employee == null) {
                    return redirect()->back()->with('error', 'Whoops! You dont belong to any branch!');
                }
                $outlet_id = $employee->outlet_id;
            } else {
                $outlet_id = $request->outlet_id;
            }

            $customer = Customer::findOrFail($request->customer_id)->update(['is_used' => 1]);
            Purchase::create([
                'customer_id' =>  $request->customer_id,
                'product_category_id' =>  $request->category_id,
                'brand_id' =>  $request->brand_id,
                'brand_model_id' =>  $request->model_id,
                'product_serial' => $request->product_serial,
                'invoice_number' => $request->invoice_number,
                'purchase_date' => $request->purchase_date,
                'outlet_id' => $outlet_id,
                'general_warranty_date' => $request->general_warranty_date,
                'special_warranty_date' => $request->special_warranty_date,
                'service_warranty_date' => $request->service_warranty_date,
                'created_by' => Auth::id(),
            ]);
            DB::commit();
            Session::forget('brand_id');
            return redirect('product/purchase')
                ->with('success', __('label.NEW_PURCHASE_CREATED'));
        } catch (\Exception $e) {
            DB::rollback();
            $bug = $e->getMessage();
            return redirect()->back()->with('error', $bug);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try {
            $purchase = Purchase::findOrFail($id);
            return view('purchase.show', compact('purchase'));
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
        try {
            $purchase = Purchase::findOrFail($id);
            $brands = Brand::orderBy('name')->get();
            $categories = Category::where('status', 1)->orderBy('name')->get();
            $customers = Customer::orderBy('name')->get();
            $outlets = Outlet::where('status', 1)->orderBy('name')->get();
            $brandmodels = BrandModel::where('status', 1)->orderBy('model_name')->get();
            return view('purchase.edit', compact('purchase', 'brands', 'categories', 'customers', 'outlets', 'brandmodels'));
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
            'customer_id' => 'required',
            'category_id' => 'required',
            'brand_id' => 'required',
            'model_id' => 'required',
            // 'product_serial' => 'unique:purchases,product_serial,' . $id,
            // 'invoice_number' => 'unique:purchases,invoice_number,' . $id,
            'purchase_date' => 'required',
            'own_stock' => 'required',
            'outlet_id' => 'nullable',
            'general_warranty_date' => 'required',
            'special_warranty_date' => 'required',
            'service_warranty_date' => 'required'
        ]);

        DB::beginTransaction();

        try {

            if ($request->own_stock == 1) {
                $employee = Employee::where('user_id', Auth::user()->id)->first();
                if ($employee == null) {
                    return redirect()->back()->with('error', 'Whoops! You dont belong to any branch!');
                }
                $outlet_id = $employee->outlet_id;
            } else {
                $outlet_id = $request->outlet_id;
            }
            $customer = Customer::findOrFail($request->customer_id)->update(['is_used' => 1]);
            $purchase = Purchase::findOrFail($id);
            $purchase->update([
                'customer_id' =>  $request->customer_id,
                'product_category_id' =>  $request->category_id,
                'brand_id' =>  $request->brand_id,
                'brand_model_id' =>  $request->model_id,
                'product_serial' => $request->product_serial,
                'invoice_number' => $request->invoice_number,
                'purchase_date' => $request->purchase_date,
                'outlet_id' => $outlet_id,
                'general_warranty_date' => $request->general_warranty_date,
                'special_warranty_date' => $request->special_warranty_date,
                'service_warranty_date' => $request->service_warranty_date,
                'updated_by' => Auth::id(),
            ]);
            DB::commit();
            return redirect('product/purchase')
                ->with('success', __('label.PURCHASE_UPDATED'));
        } catch (\Exception $e) {
            DB::rollback();
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
    public function destroy($id)
    {
        try {
            $purchase = Purchase::findOrFail($id);
            if ($purchase) {
                $ticket = Ticket::where('purchase_id', $purchase->id)->get();
                if (count($ticket) > 0) {
                    return response()->json([
                        'success' => false,
                        'message' => "Sorry! Can't Delete. This Customer is Used in Ticket Already",
                    ]);
                } else {
                    $purchase->delete();
                    return response()->json([
                        'success' => true,
                        'message' => "Purchase Data Deleted Successfully",
                    ]);
                }
            }
        } catch (\Exception $e) {
            $bug = $e->getMessage();
            return response()->json([
                'success' => false,
                'message' => $bug,
            ]);
        }
    }
    public function brand(Request $request)
    {
        $brand = Brand::where('product_category_id', $request->id)
            ->where('status', true)->get();

        return response()->json([
            'brand'          => $brand
        ]);
    }
    public function model(Request $request)
    {
        $brand_model = BrandModel::where('brand_id', $request->id)->get();
        return response()->json([
            'brand_model' => $brand_model
        ]);
    }

    public function purchaseInfo(Request $request)
    {
        $search = $request->input('products_serial_number');

        if ($search != null) {
            $purchase_info = [];
            $purchases = Purchase::with('ticket')
                ->where('product_serial', 'LIKE', "%$search%")
                ->get();

            foreach ($purchases as $key => $purchase) {
                $item['purchase_id'] = $purchase->id;
                $item['customer_name'] = $purchase->customer->name;
                $item['customer_address'] = $purchase->customer->address;
                $item['customer_mobile'] = $purchase->customer->mobile;
                $item['customer_name'] = $purchase->customer->name;
                $item['purchase_date'] = $purchase->purchase_date->format('m/d/Y');
                $item['product_serial'] = $purchase->product_serial;
                $item['product_name'] = $purchase->category->name;
                $item['product_brand_name'] = $purchase->brand->name;
                $item['product_model_name'] = $purchase->modelname->model_name;
                $item['point_of_purchase'] = $purchase->outlet->name;
                $tickets = $purchase->ticket;

                array_push($purchase_info, $item);
            }

            $faults = Fault::where('status', 1)->select('id', 'name', 'status')->get();

            $serviceHistory = view('ticket.purchaseHistory.getServiceHistory', compact('purchases', 'faults'))
                ->render();

            return response()->json(compact('purchase_info', 'serviceHistory'));
        }

        $purchase_info = [];
        return response()->json($purchase_info);
    }

    public function purchaseInfoName(Request $request)
    {
        $search = $request->input('coustormer_name');
        $customers = Customer::where('name', 'LIKE', "%$search%")->get();
        $purchase_info = [];
        foreach ($customers as $key => $customer) {
            $purchase = Purchase::where('customer_id', $customer->id)->first();
            $item['purchase_id'] = $purchase->id;
            $item['customer_name'] = $purchase->customer->name;
            $item['customer_address'] = $purchase->customer->address;
            $item['customer_mobile'] = $purchase->customer->mobile;
            $item['customer_name'] = $purchase->customer->name;
            $item['purchase_date'] = $purchase->purchase_date->format('m/d/Y');
            $item['product_serial'] = $purchase->product_serial;
            $item['product_name'] = $purchase->category->name;
            $item['product_brand_name'] = $purchase->brand->name;
            $item['product_model_name'] = $purchase->modelname->model_name;
            $item['point_of_purchase'] = $purchase->outlet->name;
            array_push($purchase_info, $item);
        }
        return response()->json($purchase_info);
    }

    public function purchaseinfo_mobile(Request $request)
    {
        $search = $request->input('coustormer_phone_number');
        $customer = Customer::where('mobile', 'LIKE', "%$search%")->first();
        if ($customer != null) {
            $purchase_info = [];
            $purchases = Purchase::where('customer_id', $customer->id)
                ->get();
            foreach ($purchases as $key => $purchase) {
                $item['purchase_id'] = $purchase->id;
                $item['customer_name'] = $purchase->customer->name;
                $item['customer_address'] = $purchase->customer->address;
                $item['customer_mobile'] = $purchase->customer->mobile;
                $item['customer_name'] = $purchase->customer->name;
                $item['purchase_date'] = $purchase->purchase_date->format('m/d/Y');
                $item['product_serial'] = $purchase->product_serial;
                $item['product_name'] = $purchase->category->name;
                $item['product_brand_name'] = $purchase->brand->name;
                $item['product_model_name'] = $purchase->modelname->model_name;
                $item['point_of_purchase'] = $purchase->outlet->name;
                array_push($purchase_info, $item);
            }
            return response()->json($purchase_info);
        }
    }

    public function sampleExcel()
    {
        try {
            return Response::download('sample/purchase_sample_excel.xlsx', 'purchase_sample_excel.xlsx');
        } catch (\Exception $e) {
            $bug = $e->getMessage();
            return redirect()->back()->with('error', $bug);
        }
    }

    public function import(Request $request)
    {
        try {
            Excel::import(new Purchase, $request->file('import_file'));
            return back();
        } catch (\Exception $e) {
            $bug = $e->getMessage();
            return redirect()->back()->with('error', $bug);
        }
    }
}
