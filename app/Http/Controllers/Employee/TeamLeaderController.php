<?php

namespace App\Http\Controllers\Employee;

use DB;
use Session;
use Redirect;
use Validator;
use DataTables;
use App\Models\User;
use App\Models\Group\Group;
use Illuminate\Http\Request;
use App\Models\Employee\Employee;
use App\Models\Employee\TeamLeader;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class TeamLeaderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try{
            $teamleaders=TeamLeader::with('user','group')->latest();
            $employees=Employee::orderBy('name')->get();
            $groups=Group::where('status', 1)->orderBy('name')->get();

            if (request()->ajax()) {
                return DataTables::of($teamleaders)

                    ->addColumn('userName', function ($teamleaders) {
                        $data = isset($teamleaders->user) ? $teamleaders->user->name : null;
                        return $data;
                    })

                    ->addColumn('groupName', function ($teamleaders) {
                        $data = isset($teamleaders->group) ? $teamleaders->group->name : null;
                        return $data;
                    })

                    ->addColumn('action', function ($teamleaders) {
                        if (Auth::user()->can('edit') && Auth::user()->can('delete')) {
                            return '<div class="table-actions text-center">
                                            <a href="' . route('hrm.teamleader.edit', $teamleaders->id) . '" title="Edit"><i class="ik ik-edit-2 f-16 mr-15 text-green"></i></a>
                                            <a type="submit" onclick="showDeleteConfirm(' . $teamleaders->id . ')" title="Delete"><i class="ik ik-trash-2 f-16 text-red"></i></a>
                                            </div>';
                        } elseif (Auth::user()->can('edit')) {
                            return '<div class="table-actions">
                                            <a href="' . route('hrm.teamleader.edit', $teamleaders->id) . '" title="Edit"><i class="ik ik-edit-2 f-16 mr-15 text-green"></i></a>
                                            </div>';
                        } elseif (Auth::user()->can('delete')) {
                            return '<div class="table-actions">
                                            <a type="submit" onclick="showDeleteConfirm(' . $teamleaders->id . ')" title="Delete"><i class="ik ik-trash-2 f-16 text-red"></i></a>
                                            </div>';
                        }
                    })
                    ->addIndexColumn()
                    ->rawColumns(['userName', 'groupName', 'action'])
                    ->make(true);
            }

            return view('employee.teamleader.index',compact('employees','groups','teamleaders'));
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
        //
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
            'employee_id' => 'required|numeric',
            'group_id' => 'required|numeric',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return redirect('hrm/teamleader')
                            ->withInput($request->all())
                            ->withErrors($validator);
        }
        DB::beginTransaction();
        try{
            $employee=Employee::where('id',$request->employee_id)->first();
            $teamleader=TeamLeader::where('user_id',$employee->user_id)->first();
            if(!empty($teamleader)){
               return redirect()->back()->with('error','This user already exist in teamleader list');
            }
            TeamLeader::create([
                'employee_id' =>  $request->employee_id,
                'group_id' =>  $request->group_id,
                'user_id' =>  $employee->user_id,
                'created_by' => Auth::id(),
            ]);
            DB::commit();
            return redirect('hrm/teamleader')
            ->with('success', __('label.TEAM_LEADER_CREATED'));
        }catch(\Exception $e){
            DB::rollback();
            return redirect()->back()->with('error','Something Went Wrong!');
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
            $employees=Employee::orderBy('name')->get();
            $groups=Group::where('status', 1)->orderBy('name')->get();
            $teamleader=TeamLeader::findOrFail($id);
            return view('employee.teamleader.edit',compact('employees','groups','teamleader'));
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
            'employee_id' => 'required|numeric',
            'group_id' => 'required|numeric',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return redirect('hrm/teamleader')
                            ->withInput($request->all())
                            ->withErrors($validator);
        }
        DB::beginTransaction();
        try{
            $teamLeader=TeamLeader::findOrFail($id);
            $employee=Employee::where('id',$request->employee_id)->first();
            $teamLeader->update([
                'employee_id' =>  $request->employee_id,
                'group_id' =>  $request->group_id,
                'user_id' =>  $employee->user_id,
                'updated_by' => Auth::id(),
            ]);
            DB::commit();
            return redirect('hrm/teamleader')
            ->with('success', __('label.TEAM_LEADER_UPDATED'));
        }catch(\Exception $e){
            dd($e);
            DB::rollback();
            return redirect()->back()->with('error','Something Went Wrong!');
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
            $teamleader=TeamLeader::findOrFail($id);
            if ($teamleader != null) {
                $teamleader->delete();
                return response()->json([
                    'success' => true,
                    'message' => 'Teamleader deleted successfully.',
                ]);
            }
        } catch (\Exception $e) {
            $bug = $e->getMessage();
            return response()->json([
                'success' => false,
                'message' =>  $bug,
            ]);
        }
    }
}
