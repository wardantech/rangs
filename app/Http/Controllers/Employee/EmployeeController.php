<?php

namespace App\Http\Controllers\Employee;

use DB;
use Session;
use Redirect;
use Validator;
use DataTables;
use Carbon\Carbon;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\Outlet\Outlet;
use App\Models\Inventory\Store;
use App\Models\Employee\Employee;
use Spatie\Permission\Models\Role;
use App\Models\Employee\Attendance;
use App\Models\Employee\TeamLeader;
use App\Models\Inventory\Inventory;
use Illuminate\Support\Facades\URL;
use App\Http\Controllers\Controller;
use App\Models\Employee\Designation;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\CallCenter\CallCenter;
use App\Models\ServiceCenter\ServiceCenter;
use App\Models\Inventory\ServiceSourcingVendor;


class EmployeeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try{
            $employees = Employee::with('designation')->orderBy('id','desc');
            $branches = Outlet::where('status', 1)->orderBy('id', 'desc')->get();

            if (request()->ajax()) {
                return DataTables::of($employees)
                ->addColumn('status', function ($employees) {
                    if (Auth::user()->can('delete')) {
                        $button = '<label class="switch">';
                        $button .= ' <input type="checkbox" class="changeStatus" id="customSwitch' . $employees->id . '" getId="' . $employees->id . '" title="status"';

                        if ($employees->status == 1) {
                            $button .= "checked";
                        }
                        $button .= ' ><span class="slider round"></span>';
                        $button .= '</label>';
                        return $button;
                    }else{
                            if($employees->status == 1){
                                return '<span class="badge badge-success" title="Active">Active</span>';
                            }elseif($employees->status == 0){
                                return '<span class="badge badge-danger" title="Inactive">Inactive</span>';
                            }
                        }

                    })
                    ->addColumn('action', function ($employees) {
                        if (Auth::user()->can('edit') && Auth::user()->can('delete') && Auth::user()->can('show')) {
                            return '<div class="table-actions text-center">
                                            <a href="' . route('hrm.technician.show', $employees->id) . '" title="Show"><i class="ik ik-eye f-16 mr-15 text-blue"></i></a>
                                            <a href="' . URL::signedRoute('hrm.technician.edit', $employees->id) . '" title="Edit"><i class="ik ik-edit-2 f-16 mr-15 text-green"></i></a>
                                            <a type="submit" onclick="showDeleteConfirm(' . $employees->id . ')" title="Delete"><i class="ik ik-trash-2 f-16 text-red"></i></a>
                                            </div>';
                        } elseif (Auth::user()->can('edit')) {
                            return '<div class="table-actions">
                                            <a href="' . URL::signedRoute('hrm.technician.edit', $employees->id) . '" title="Edit"><i class="ik ik-edit-2 f-16 mr-15 text-green"></i></a>
                                            </div>';
                        } elseif (Auth::user()->can('show')) {
                        return '<div class="table-actions">
                                            <a href="' . route('hrm.technician.show', $employees->id) . '" title="Show"><i class="ik ik-eye f-16 mr-15 text-blue"></i></a>
                                            </div>';
                    }elseif (Auth::user()->can('delete')) {
                            return '<div class="table-actions">
                                            <a type="submit" onclick="showDeleteConfirm(' . $employees->id . ')" title="Delete"><i class="ik ik-trash-2 f-16 text-red"></i></a>
                                            </div>';
                        }
                    })
                    ->addIndexColumn()
                    ->rawColumns(['status','action'])
                    ->make(true);
            }
            return view('employee.registration.index',compact('employees', 'branches'));
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
            $designations = Designation::where('status', 1)->pluck('name','id')->toArray();
            $callCenter = CallCenter::pluck('name','id')->toArray();
            // $serviceCenter = ServiceCenter::pluck('name','id')->toArray();
            $outlets=Outlet::where('status', 1)->get();
            $stores = Store::where('status', 1)->pluck('name','id')->toArray();
            $vendors = ServiceSourcingVendor::where('status', 1)->pluck('name','id')->toArray();
            $roles = Role::pluck('name', 'id');
            $teamleaders=TeamLeader::latest()->get();

            return view('employee.registration.create', compact('roles','designations','callCenter','stores','vendors','teamleaders', 'outlets'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
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
            'designation_id' => 'required|numeric',
            'name' => 'required|string',
            'employee_address' => 'required|string',
            'email' => 'required|email|unique:employees,email,NULL,id,deleted_at,NULL',
            'email' => 'required|email|unique:users,email',
            'phone' => 'required||min:11|max:11|regex:/(01)[0-9]{9}/|unique:employees,mobile,NULL,id,deleted_at,NULL',
            'outlet_id' => 'required|numeric',
            'store_id' => 'required|numeric',
            // 'callcenter_id' => 'nullable|numeric',
            'vendor_id' => 'nullable|numeric',
            'teamleader_id' => 'nullable|numeric',
        ]);

        DB::beginTransaction();

        try{
            if ($request->add_user) {
                $user = User::create([
                    'name' => $request->name,
                    'email' => $request->email,
                    'password' => Hash::make($request->password),
                ]);
                $user->syncRoles($request->role);
            }

            $employee=Employee::create([
                'user_id'        => $user->id ?? Null,
                'team_leader_id' => $request->teamleader_id,
                'designation_id' => $request->designation_id,
                'name'           => $request->name,
                'employee_code'  => $request->employee_code,
                'address'        => $request->employee_address,
                'email'          => $request->email,
                'mobile'         => $request->phone,
                'user_type'      => $request->status,
                'outlet_id'      => $request->outlet_id,
                // 'call_center_id' => $request->callcenter_id,
                'store_id'       => $request->store_id,
                'vendor_id'      => $request->vendor_id,
                'created_by'     => Auth::id()
            ]);

            if ($request->teamleader_id) {
                Store::create([
                    'user_id'    => $user->id ?? Null,
                    'outlet_id'  => $request->outlet_id,
                    'name'       => $request->name,
                    'address'    => $request->employee_address,
                    'code'       => $request->employee_code,
                    'created_by' => Auth::id(),
                ]);
            }
            DB::commit();
            return redirect('hrm/technician')->with('success', __('label.NEW_EMPLOYEE_ADDED'));
        }catch(\Exception $e){
            DB::rollback();
            return redirect()->back()->with('error', $e->getMessage());
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
        try{
            $admin=0;
            $employee = Employee::findOrFail($id);
            $current_date = Carbon::now('Asia/Dhaka');
            $checkstatus=Attendance::where('employee_id',$employee->id)->whereDate('date', $current_date->toDateString())->first();
            //Attendance History
            $today = today('Asia/Dhaka');
            $dates = [];

            for($i=1; $i < $today->daysInMonth+1; ++$i) {
                $dates[] = \Carbon\Carbon::createFromDate($today->year, $today->month, $i)->format('Y-m-d');
            }
            return view('employee.registration.profile',compact('admin','employee','checkstatus','dates','current_date'));
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
            $employee = Employee::findOrFail($id);
            $designations = Designation::where('status',1)->select('name','id')->get();
            $callCenters = CallCenter::select('name','id')->get();
            $outlets = Outlet::where('status', 1)->get();
            $stores = Store::where('status', 1)->select('name','id')->get();
            $vendors = ServiceSourcingVendor::where('status', 1)->select('name','id')->get();
            $roles = Role::select('name','id')->get();
            $teamleaders = TeamLeader::latest()->get();

            $user = "";
            if($employee->user_id) {
                $user = User::where('id', $employee->user_id)->first();
            }

            return view('employee.registration.edit', compact(
                'employee', 'designations', 'callCenters', 'outlets', 'stores', 'vendors' , 'roles', 'teamleaders', 'user'
            ));

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
            'designation_id' => 'required|numeric',
            'name' => 'required|string',
            'employee_address' => 'required|string',
            'email' => 'required|email|unique:employees,email,' . $id,
            'phone' => 'required||min:11|max:11|regex:/(01)[0-9]{9}/|unique:employees,mobile,' . $id,
            'outlet_id' => 'required|numeric',
            'store_id' => 'required|numeric',
            // 'callcenter_id' => 'nullable|numeric',
            'vendor_id' => 'nullable|numeric',
            'teamleader_id' => 'nullable|numeric',
        ]);

        try {
            $employee = Employee::findOrFail($id);

            if ($request->add_user) {
                // store user information
                $user = User::find($employee->user_id);

                if(!$user) {
                    $user = User::create([
                        'name' => $request->name,
                        'email' => $request->email,
                        'password' => Hash::make($request->password),
                    ]);
                }else {
                    $user->update([
                        'name' => $request->name,
                        'email' => $request->email,
                    ]);

                    if (isset($request->password)) {
                        $update = $user->update([
                            'password' => Hash::make($request->password),
                        ]);
                    }
                }

                $user->syncRoles($request->role);
            }

            $employee->update([
                'user_id'        => $user->id?? Null,
                'team_leader_id' => $request->teamleader_id,
                'designation_id' => $request->designation_id,
                'name'           => $request->name,
                'employee_code'  => $request->employee_code,
                'address'        => $request->employee_address,
                'email'          => $request->email,
                'mobile'         => $request->phone,
                'user_type'      => $request->status,
                'outlet_id'      => $request->outlet_id,
                // 'call_center_id' => $request->callcenter_id,
                'store_id'       => $request->store_id,
                'vendor_id'      => $request->vendor_id,
                'created_by'     => Auth::id()
            ]);

            if ($request->teamleader_id) {
                $Store=Store::where('user_id',$employee->user_id)->first();
                if(!empty($Store)){
                    $Store->update([
                        'user_id' => $employee->user_id,
                        'outlet_id' => $request->outlet_id,
                        'name' => $request->name,
                        'address' => $request->employee_address,
                        'code' => $request->employee_code,
                        'updated_by' => Auth::id()
                    ]);
                }else{
                    Store::Create([
                        'user_id' => $employee->user_id,
                        'outlet_id' => $request->outlet_id,
                        'name' => $request->name,
                        'address' => $request->employee_address,
                        'code' => $request->employee_code,
                        'created_by' => Auth::id()
                    ]);
                }
            }
            return redirect('hrm/technician')->with('success', 'Employee Updated Successfully');

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
            $employee = Employee::findOrFail($id);
            $user = User::find($employee->user_id); //TeamLeader

            $teamLeader=TeamLeader::where('employee_id', $employee->id)->get();
            if(count($teamLeader) > 0){
                return response()->json([
                    'success' => false,
                    'message' => "Sorry! Can't Delete. This Employee is used in Team Leader Management",
                ]);
            }else{
                if($user != null){
                    $user->delete();
                }
                $employee->delete();
                return response()->json([
                    'success' => true,
                    'message' => 'Employee Deleted Successfully.',
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

    public function filterEmployee(Request $request)
    {
        $this->validate($request, [
            'branch_id' => 'required',
        ]);
        try{
            $employees = Employee::where('outlet_id', $request->branch_id)->get();
            $branches = Outlet::where('status', 1)->orderBy('name')->get();
            return view('employee.registration.filter_employee', compact('employees', 'branches'));
        } catch (\Exception $e) {
            $bug = $e->getMessage();
            return redirect()->back()->with('error', $bug);
        }
    }

    public function getStore($id)
    {
        try{
            $stores=Store::where('status', 1)->where('outlet_id', $id)->get();
            return response()->json($stores);
        } catch (\Exception $e) {
            $bug = $e->getMessage();
            return redirect()->back()->with('error', $bug);
        }
    }

    public function profile()
    {
        try{
            $current_date = Carbon::now('Asia/Dhaka');
            $admin=0;
            $employee = Employee::with('user')
                    ->where('user_id', Auth::id())
                    ->first();
                    if($employee!=null){
                        $checkstatus=Attendance::where('employee_id',$employee->id)->whereDate('date', $current_date->toDateString())->first();
                        //Attendance History
                        $today = today('Asia/Dhaka');
                        $dates = [];

                        for($i=1; $i < $today->daysInMonth+1; ++$i) {
                            $dates[] = \Carbon\Carbon::createFromDate($today->year, $today->month, $i)->format('Y-m-d');
                        }
                    }
            if($employee) {
                return view('employee.registration.profile',compact('admin','employee','checkstatus','dates','current_date'));
            }else {
                $employee = User::where('id', Auth::id())->first();
                $admin=1;
                return view('employee.registration.profile',compact('employee','admin'));
            }
        } catch (\Exception $e) {
            $bug = $e->getMessage();
            return redirect()->back()->with('error', $bug);
        }
    }
    public function getEmployees($type)
    {
        try{
            $auth = Auth::user();
            $user_role = $auth->roles->first();
            if ($type==2) {
                if ($user_role->name == 'Super Admin' || $user_role->name == 'Admin' || $user_role->name =='Call Center Admin') {
                    $employees=Employee::whereNotNull('vendor_id')->whereNotNull('team_leader_id')->latest()->get();
                } else {
                $teamleader=TeamLeader::where('user_id',Auth::user()->id)->first();
                $employees = Employee::whereNotNull('vendor_id')->where('team_leader_id',$teamleader->id)->get();
                }
            }else{
                if ($user_role->name == 'Super Admin' || $user_role->name == 'Admin' || $user_role->name =='Call Center Admin') {
                    $employees=Employee::whereNotNull('team_leader_id')->latest()->get();
                } else {
                $teamleader=TeamLeader::where('user_id',Auth::user()->id)->first();
                $employees = Employee::where('vendor_id',null)->where('team_leader_id',$teamleader->id)->get();
                }
            }
            return response()->json($employees);
        } catch (\Exception $e) {
            $bug = $e->getMessage();
            return response()->json([
                'success' => false,
                'message' => $bug,
            ]);
        }
    }
    public function updateStatus(Request $request, $id)
    {
        if ($request->ajax()) {
            $employee=Employee::findOrFail($id);
            $user=User::findOrFail($employee->user_id);
            if ($user) {
                $user->status = $user->status == 1 ? 0 : 1;
                $user->update();
            }
            $employee->status = $employee->status == 1 ? 0 : 1;
            $employee->update();

            if ($employee->status == 1) {
                return response()->json([
                    'success' => true,
                    'message' => 'Status Enabled',
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Status Disabled',
                ]);
            }
        }

    }
}
