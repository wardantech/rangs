<?php

namespace App\Http\Controllers\Customer;

use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use App\Models\Customer\Customer;
use App\Models\Inventory\Division;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\Customer\CustomerGrade;
use App\Models\ProductPurchase\Purchase;

class CustomerController extends Controller
{
    public function index()
    {
        try {

            $divisions = Division::all();
            $customerGrades = CustomerGrade::where('status', 1)->orderBy('id', 'desc')->get();
            if (request()->ajax()) {
                $customers = Customer::with('grade')->orderBy('id', 'desc');
                return DataTables::of($customers)

                    ->addColumn('GradeName', function ($customers) {
                        $data = $customers->grade ? $customers->grade->name : null;
                        return $data;
                    })
                    ->addColumn('action', function ($customers) {
                        if (Auth::user()->can('edit') && Auth::user()->can('delete')) {
                            return '<div class="table-actions d-flex">
                                            <a href="' . route('call-center.edit.customer', $customers->id) . '" title="Edit"><i class="ik ik-edit-2 f-16 mr-15 text-green"></i></a>
                                            <a type="submit" onclick="showDeleteConfirm(' . $customers->id . ')" title="Delete"><i class="ik ik-trash-2 f-16 text-red"></i></a>
                                            </div>';
                        } elseif (Auth::user()->can('edit')) {
                            return '<div class="table-actions">
                                            <a href="' . route('call-center.edit.customer', $customers->id) . '" title="Edit"><i class="ik ik-edit-2 f-16 mr-15 text-green"></i></a>
                                            </div>';
                        } elseif (Auth::user()->can('delete')) {
                            return '<div class="table-actions">
                                            <a type="submit" onclick="showDeleteConfirm(' . $customers->id . ')" title="Delete"><i class="ik ik-trash-2 f-16 text-red"></i></a>
                                            </div>';
                        }
                    })
                    ->addIndexColumn()
                    ->rawColumns(['GradeName', 'secondary_mobile', 'action'])
                    ->make(true);
            }

            return view('customer.index', compact('divisions', 'customerGrades'));
        } catch (\Exception $e) {
            $bug = $e->getMessage();
            return redirect()->back()->with('error', $bug);
        }
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'email' => 'nullable|email|unique:customers,email',
            'mobile' => 'required||min:11|max:11|regex:/(01)[0-9]{9}/|unique:customers,mobile,NULL,id,deleted_at,NULL',
            'address' => 'required|string',
            'customer_grade_id' => 'required'
        ]);

        try {
            // $customer = $request->all();
            Customer::create([
                'name' => $request->name,
                'email' => $request->email,
                'mobile' => $request->mobile,
                'secondary_mobile' => $request->secondary_mobile,
                'address' => $request->address,
                'customer_grade_id' => $request->customer_grade_id,
            ]);
            return redirect()->route('call-center.customer-index')->with('success', __('New customer created successfully.'));
        } catch (\Exception $e) {
            $bug = $e->getMessage();
            return redirect()->back()->with('error', $bug);
        }
    }

    public function edit($id)
    {
        try {
            $customer = Customer::find($id);
            $customerGrades = CustomerGrade::where('status', 1)->get();
            return view('customer.edit', compact('customer', 'customerGrades'));
        } catch (\Exception $e) {
            $bug = $e->getMessage();
            return redirect()->back()->with('error', $bug);
        }
    }

    public function update($id, Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'email' => 'nullable|email|unique:customers,email,' . $id,
            'mobile' => 'required||min:11|max:11|regex:/(01)[0-9]{9}/|unique:customers,mobile,' . $id,
            'customer_grade_id' => 'required',
            'address' => 'required|string'
        ]);

        try {
            $customer = Customer::find($id);
            $customer->update([
                'name' => $request->name,
                'email' => $request->email,
                'mobile' => $request->mobile,
                'secondary_mobile' => $request->secondary_mobile,
                'customer_grade_id' => $request->customer_grade_id,
                'address' => $request->address,
            ]);
            return redirect()->route('call-center.customer-index')->with('success', __('Customer Updated Successfully.'));
        } catch (\Exception $e) {
            $bug = $e->getMessage();
            return redirect()->back()->with('error', $bug);
        }
    }

    public function destroy($id)
    {
        try {
            $customer=Customer::findOrFail($id);
            if($customer){
                $purchase=Purchase::where('customer_id', $customer->id)->get();
                if(count($purchase) > 0){
                    return response()->json([
                        'success' => false,
                        'message' => "Sorry! Can't Delete. This Customer is Used  in Purchase Already",
                    ]);
                } else {
                    $customer->delete();
                    return response()->json([
                        'success' => true,
                        'message' => 'Customer Deleted Successfully.',
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
    public function customerData(Request $request)
    {
        $input = $request->all();
        if (!empty($input['query'])) {

            $data = Customer::where("mobile", "LIKE", "%{$input['query']}%")
                ->orWhere('name', "LIKE", "%{$input['query']}%")
                ->get();
        } else {

            $data = Customer::limit(10)
            ->get();
        }

        $customers = [];

        if (count($data) > 0) {

            foreach ($data as $customer) {
                $customers[] = array(
                    "id" => $customer->id,
                    "text" => $customer->name."-".$customer->mobile,
                );
            }
        }
        return response()->json($customers);
    }
}
