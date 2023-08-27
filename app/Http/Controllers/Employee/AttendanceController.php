<?php

namespace App\Http\Controllers\Employee;

use Session;
use Redirect;
use Validator;
use DataTables;
use Carbon\Carbon;

use Illuminate\Http\Request;
use App\Models\Employee\Employee;
use App\Models\Employee\Attendance;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class AttendanceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try{
            $employee=Auth::user()->name;
            $dt=Carbon::now();
            $current_date = Carbon::now('Asia/Dhaka');
            $checkstatus=Attendance::where('employee_id',Auth::id())->whereDate('created_at', $current_date->toDateString())->first();
            //Attendance History
            $today = today('Asia/Dhaka');

            $dates = [];

            for($i=1; $i < $today->daysInMonth+1; ++$i) {
                $dates[] = \Carbon\Carbon::createFromDate($today->year, $today->month, $i)->format('Y-m-d');
            }

            return view('employee.attendance.index',compact('employee','checkstatus','dates','current_date'));
        } catch (\Exception $e) {
            $bug = $e->getMessage();
            return redirect()->back()->with('error', $bug);
        }
    }

    public function indexForTechnician()
    {
        try{
            $employee=Auth::user()->name;
            $dt=Carbon::now();
            $current_date = Carbon::now('Asia/Dhaka');
            $checkstatus=Attendance::where('employee_id',Auth::id())->whereDate('date', $current_date->toDateString())->first();
            //Attendance History
            $today = today('Asia/Dhaka');
            $dates = [];

            for($i=1; $i < $today->daysInMonth+1; ++$i) {
                $dates[] = \Carbon\Carbon::createFromDate($today->year, $today->month, $i)->format('Y-m-d');
            }
            return view('employee.attendance.index',compact('employee','checkstatus','dates','current_date'));
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
        try{
            $this->validate($request, [
                'date' => 'required',
                'name' => 'required',
                'time' => 'required',
                'type' => 'required|numeric',
            ]);

            $current_date = Carbon::now('Asia/Dhaka');
            $employee=Employee::where('user_id',Auth::user()->id)->first();
            $checkstatus=Attendance::where('employee_id',Auth::user()->id)->whereDate('date', $current_date->toDateString())->first();

            if($checkstatus==null || $checkstatus->checkin == null){
                $attendance=new Attendance();
                $attendance->employee_id = Auth::id();
                $attendance->user_id = Auth::id();
                $attendance->date = $request->date;
                $attendance->attendance_type = $request->type;
                $attendance->checkin = $request->time;
                $attendance->note = $request->note;
                $attendance->created_by = Auth::id();
                $attendance->save();
                return redirect()->back()->with('success', 'Checked in successfully');
            }else{
                    $checkstatus->checkout=$request->time;
                    $checkstatus->updated_by = Auth::id();
                    $checkstatus->save();
                    return redirect()->back()->with('success', 'Checked Out successfully');
            }
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
        //
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
