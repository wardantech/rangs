<?php

namespace App\Http\Controllers\Ticket;

use DB;
use Illuminate\Http\Request;
use App\Models\Ticket\JobPriority;
use App\Http\Controllers\Controller;

class JobPriorityController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try{
            $job_priorities = JobPriority::latest()->get();
            return view('ticket.job_priority.index',compact('job_priorities'));
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
            'job_priority' => 'required|string|unique:job_priorities,job_priority,NULL,id,deleted_at,NULL',
            'status' => 'required'
        ]);

        try {
            $data = $request->all();
            JobPriority::create($data);
            return back()->with('success', __('label.NEW_JOB_PRIORITY_CREATED'));
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
        $priority = JobPriority::findOrFail($id);
        return $priority;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $this->validate($request, [
            'job_priority' => 'required|string|unique:job_priorities,job_priority' . $request->id,
            'status' => 'required'
        ]);

        try {
            $data = $request->all();
            $priority = JobPriority::find($data['priority_id']);
            $priority->job_priority = $data['job_priority'];
            $priority->status = $data['status'];
            $priority->save();

            return back()->with('success', __('label.NEW_JOB_PRIORITY_UPDATED'));
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
        try {
            $jobPriority=JobPriority::findOrFail($id);
            $Ticket = DB::table('tickets')
            ->where('deleted_at', NULL)
            ->where('job_priority_id',$jobPriority->id)
            ->get();
            if(count($Ticket) > 0){
                return back()->with('error', "Sorry! Can't Delete. This Job Priority is used in Ticket Management");
            }else{
                $jobPriority->delete();
                return redirect()->back()->with('success', 'Job Priority deleted successfully');
            }
            // return back()->with('success', __('label.JOB_PRIORITY_DELETED'));
        } catch (\Exception $e) {
            $bug = $e->getMessage();
            return redirect()->back()->with('error', $bug);
        }
    }

    public function aciveInactive(Request $request, $id)
    {
        $this->validate($request, [
            'status' => 'required|numeric|boolean'
        ]);

        try {
            $jobPriority = JobPriority::findOrFail($id);

            if($request->status == false) {
                $jobPriority->update([
                    'status' => $request->status
                ]);

                return back()->with('success', __('job priority inactive now'));
            }elseif ($request->status == true) {
                $jobPriority->update([
                    'status' => $request->status
                ]);

                return back()->with('success', __('job priority active now'));
            }

            return back()->with('error', __('Action decline'));
        }catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}
