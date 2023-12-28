<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\JobStatusService;
use App\Services\TicketStatusService;
use App\Models\Employee\Employee;

class DashBoardController extends Controller
{
    protected $jobStatusService;
    protected $ticketStatusService;

    public function __construct(JobStatusService $jobStatusService, TicketStatusService $ticketStatusService)
    {
        $this->jobStatusService = $jobStatusService;
        $this->ticketStatusService = $ticketStatusService;
    }

    public function index(Request $request)
    {

        $auth = Auth::user();
        $employee=Employee::where('user_id', $auth->id)->first();
        $user_role = $auth->roles->first();

        if ($user_role->name == 'Super Admin' || $user_role->name == 'Admin' || $user_role->name =='Team Leader Admin') {
            $totalJobStatus = $this->jobStatusService->totalStatus();
        } elseif ($user_role->name == 'Team Leader') {
            $totalJobStatus = $this->jobStatusService->totalStatusByTeam($auth->id);
        } elseif ($user_role->name == 'Technician') {
            $totalJobStatus = $this->jobStatusService->totalStatusByTechnician($auth->id);
        } else {
            $totalJobStatus = $this->jobStatusService->totalStatusByOutlet($employee->outlet_id);
        }

        //Ticket status
        if ($user_role->name == 'Team Leader') {
            $teamLeader = TeamLeader::where('user_id', $auth->id)->first();
            if (empty($teamLeader)) {
                return redirect()->back()->with('error', "Whoops! You don't have the access");
            }

            $districtIds = json_decode($teamLeader->group->region->district_id, true);
            $thanaIds = json_decode($teamLeader->group->region->thana_id, true);
            $categoryIds = json_decode($teamLeader->group->category_id, true);

            $totalTicketStatus = $this->ticketStatusService->totalStatusByTeam($districtIds, $thanaIds, $categoryIds);
        } elseif ($user_role->name == 'Admin' || $user_role->name == 'Super Admin' || $user_role->name == 'Call Center Admin') {
            $totalTicketStatus = $this->ticketStatusService->totalStatus();
        } else {
            $totalTicketStatus = $this->ticketStatusService->totalStatusByOutlet($employee->outlet_id);
        }

       return view('pages.dashboard', compact('totalJobStatus','totalTicketStatus'));
    }
}
