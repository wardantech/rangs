<?php

namespace App\Http\Controllers\Employee;

use Session;
use Redirect;
use Validator;
use DataTables;
use Illuminate\Http\Request;
use App\Models\Employee\Employee;
use App\Http\Controllers\Controller;
use App\Models\Employee\Designation;
use Illuminate\Support\Facades\Auth;

class DesignationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try{
            $designations = Designation::orderBy('id','desc');
            if (request()->ajax()) {
                return DataTables::of($designations)

                    ->addColumn('status', function ($designations) {

                        if ($designations->status == true) {
                            $status = '<div class="text-center">
                                            <a href="' . route('hrm.designation.status', $designations->id) . '" title="Status" class="btn btn-sm btn-success">
                                                <i class="fas fa-arrow-up"></i>
                                            </a>
                                        </div>';
                        } else {
                            $status = '<div class="text-center">
                                        <a href="' . route('hrm.designation.status', $designations->id) . '" title="Status" class="btn btn-sm btn-secondary">
                                            <i class="fas fa-arrow-down"></i>
                                        </a>
                                    </div>';
                        }
                        return $status;
                    })

                    ->addColumn('action', function ($designations) {
                        if (Auth::user()->can('edit') && Auth::user()->can('delete')) {
                            return '<div class="table-actions text-center">
                                            <a href="' . route('hrm.designation.edit', $designations->id) . '" title="Edit"><i class="ik ik-edit-2 f-16 mr-15 text-green"></i></a>
                                            <a type="submit" onclick="showDeleteConfirm(' . $designations->id . ')" title="Delete"><i class="ik ik-trash-2 f-16 text-red"></i></a>
                                            </div>';
                        } elseif (Auth::user()->can('edit')) {
                            return '<div class="table-actions">
                                            <a href="' . route('hrm.designation.edit', $designations->id) . '" title="Edit"><i class="ik ik-edit-2 f-16 mr-15 text-green"></i></a>
                                            </div>';
                        } elseif (Auth::user()->can('delete')) {
                            return '<div class="table-actions">
                                            <a type="submit" onclick="showDeleteConfirm(' . $designations->id . ')" title="Delete"><i class="ik ik-trash-2 f-16 text-red"></i></a>
                                            </div>';
                        }
                    })
                    ->addIndexColumn()
                    ->rawColumns(['status', 'action'])
                    ->make(true);
            }
            return view('employee.designation.index', compact('designations'));
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
        return view('employee.designation.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $rules = [
            'name' => 'required|unique:designations,name,NULL,id,deleted_at,NULL',
            ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return redirect()
                            ->back()
                            ->withInput($request->all())
                            ->withErrors($validator);
        }
        $store=new Designation;
        $store->name    = $request->name;
        $store->status    = $request->status;
        $store->created_by = Auth::id();

        try {

            $store->save();
            return redirect('hrm/designation')->with('success', __('label.NEW_DESIGNATION_ADDED'));

        } catch (\Exception $e) {
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
        //
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
            $designation=Designation::findOrFail($id);
            return view('employee.designation.edit', compact('designation'));
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
        $rules = [
            'name' => 'required|unique:designations,name,' . $id,
            ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return redirect()
                            ->back()
                            ->withInput($request->all())
                            ->withErrors($validator);
        }
        $designation=Designation::findOrFail($id);
        $designation->update([
            'name'    => $request->name,
            'status'    => $request->status,
            'updated_by' => Auth::id()
        ]);
        try {
            return redirect('hrm/designation')->with('success', __('label.DESIGNATION_UPDATED'));
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
            $designation=Designation::findOrFail($id);
            $employee=Employee::where('designation_id', $designation->id)->get();
            if(count($employee) > 0){
                return response()->json([
                    'success' => false,
                    'message' => "Sorry! Can't Delete. This Designation is used in Employee Management",
                ]);
            }else{
                $designation->delete();
                return response()->json([
                    'success' => true,
                    'message' => 'Designation deleted successfully',
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
            $designation = Designation::findOrFail($id);

            if($designation->status == false) {
                $designation->update([
                    'status' => true
                ]);

                return back()->with('success', __('Designation active now'));
            }elseif ($designation->status == true) {
                $designation->update([
                    'status' => false
                ]);

                return back()->with('success', __('Designation inactive now'));
            }

            return back()->with('error', __('Action decline'));
        }catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}
