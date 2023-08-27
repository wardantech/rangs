<?php

namespace App\Http\Controllers\Customer;

use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use App\Models\Customer\Customer;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\Customer\CustomerGrade;

class CustomerGradeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try{
            $customerGrades=CustomerGrade::orderBy('id','desc');
            if (request()->ajax()) {
                return DataTables::of($customerGrades)

                    ->addColumn('status', function ($customerGrades) {

                        if ($customerGrades->status == true) {
                            $status = '<div class="text-center">
                                            <a href="' . route('call-center.customer-grade.status', $customerGrades->id) . '" title="Status" class="btn btn-sm btn-success">
                                                <i class="fas fa-arrow-up"></i>
                                            </a>
                                        </div>';
                        } else {
                            $status = '<div class="text-center">
                                        <a href="' . route('call-center.customer-grade.status', $customerGrades->id) . '" title="Status" class="btn btn-sm btn-secondary">
                                            <i class="fas fa-arrow-down"></i>
                                        </a>
                                    </div>';
                        }
                        return $status;
                    })

                    ->addColumn('action', function ($customerGrades) {
                        if (Auth::user()->can('edit') && Auth::user()->can('delete')) {
                            return '<div class="table-actions text-center">
                                            <a href="' . route('call-center.customer-grade.edit', $customerGrades->id) . '" title="Edit"><i class="ik ik-edit-2 f-16 mr-15 text-green"></i></a>
                                            <a type="submit" onclick="showDeleteConfirm(' . $customerGrades->id . ')" title="Delete"><i class="ik ik-trash-2 f-16 text-red"></i></a>
                                            </div>';
                        } elseif (Auth::user()->can('edit')) {
                            return '<div class="table-actions">
                                            <a href="' . route('call-center.customer-grade.edit', $customerGrades->id) . '" title="Edit"><i class="ik ik-edit-2 f-16 mr-15 text-green"></i></a>
                                            </div>';
                        } elseif (Auth::user()->can('delete')) {
                            return '<div class="table-actions">
                                            <a type="submit" onclick="showDeleteConfirm(' . $customerGrades->id . ')" title="Delete"><i class="ik ik-trash-2 f-16 text-red"></i></a>
                                            </div>';
                        }
                    })
                    ->addIndexColumn()
                    ->rawColumns(['status', 'action'])
                    ->make(true);
            }
            return view('customer.customer_grade.index', compact('customerGrades'));
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
        $this->validate($request, [
            'name' => 'required|unique:customer_grades',
            'status' => 'required',
        ]);
        try{
            CustomerGrade::create($request->all());
            return redirect('call-center/customer-grade')
                    ->with('success','Customer grade successfully added');
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
            $grade = CustomerGrade::find($id);
            return view('customer.customer_grade.edit', compact('grade'));
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
            'name' => 'required|unique:customer_grades,name,' . $id,
            'status' => 'required',
        ]);

        try{
            CustomerGrade::find($id)->update($request->all());
            return redirect('call-center/customer-grade')
                    ->with('success','Customer grade successfully updated');
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
    public function destroy($id)
    {
        try{
            $customerGrade=CustomerGrade::findOrFail($id);
            if($customerGrade){
                $customer=Customer::where('customer_grade_id',$customerGrade->id)->get();
                if(count($customer) > 0){
                    return response()->json([
                        'success' => false,
                        'message' => "Sorry! Can't Delete. This CustomerGrade is Used  in Customer Already",
                    ]);
                } else {
                    $customerGrade->delete();
                    return response()->json([
                        'success' => true,
                        'message' => 'Customer grade successfully deleted.',
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

    public function activeInactive($id)
    {
        try {
            $customerGrade = CustomerGrade::findOrFail($id);

            if($customerGrade->status == false) {
                $customerGrade->update([
                    'status' => true
                ]);

                return back()->with('success', __('Customer grade active now'));
            }elseif ($customerGrade->status == true) {
                $customerGrade->update([
                    'status' => false
                ]);

                return back()->with('success', __('Customer grade inactive now'));
            }

            return back()->with('error', __('Action decline'));
        }catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}
