<?php

namespace App\Http\Controllers\Account;

use Carbon\Carbon;
use App\Models\JobModel\Job;
use Illuminate\Http\Request;
use App\Models\Outlet\Outlet;
use App\Models\Account\Revenue;
use App\Models\Inventory\Store;
use Yajra\DataTables\DataTables;
use App\Models\Employee\Employee;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\Account\CashTransection;

class RevenueController extends Controller
{
    public function index(Request $request)
    {
        try{
            $user = auth()->user();
            $userRole =  $user->roles->first();
            $mystore =($userRole && in_array($userRole->name,['Super Admin','Admin'] )) ? " " : optional($user->employee)->outlet;
            $outlets = Outlet::select('id', 'name', 'status')->where('status', 1)->orderBy('name')->get();


            if ($request->ajax()) {

                $data=Revenue::with('outlet');

                if (!in_array($userRole->name,['Super Admin','Admin'])) {
                    $data->whereHas('outlet', function($query) use($mystore){
                        $query->where('id', optional($mystore)->id);
                    });
                }
                
                if ($request->filled(['start_date', 'end_date'])) {
                    $startDate = Carbon::parse($request->input('start_date'))->format('Y-m-d');
                    $endDate = Carbon::parse($request->input('end_date'))->addDay()->format('Y-m-d');

                    $data->whereBetween('created_at', [$startDate, $endDate]);
                } else {
                    $data->whereYear('created_at', Carbon::now()->year)
                    ->whereMonth('created_at', Carbon::now()->month);
                }

                $revenues=$data->orderBy('created_at', 'desc');

                return DataTables::of($revenues)
                    ->addColumn('outlet', function ($revenues) {
                        $data = $revenues->outlet->name ?? null;
                        return $data;
                    })

                    ->addColumn('dateFormat', function ($revenues) {
                        $data = Carbon::parse($revenues->created_at)->format('m/d/Y');
                        return $data;
                    })

                    ->addColumn('action', function ($revenues) use ($userRole) {
                        $canEdit = Auth::user()->can('edit');
                        $canShow = Auth::user()->can('show');

                        $actions = [];

                        if ($canEdit) {
                            $actions[] = '<a href="' . route('edit.revenue', $revenues->id) . '" title="Edit"><i class="ik ik-edit-2 f-16 mr-15 text-green"></i></a>';
                        }

                        if ($canDelete) {
                            $actions[] = '<a type="submit" onclick="showDeleteConfirm(' . $revenues->id . ')" title="Delete"><i class="ik ik-trash-2 f-16 text-red"></i></a>';
                        }

                        return '<div class="table-actions text-center">' . implode('', $actions) . '</div>';
                    })
                    ->addIndexColumn()
                    ->rawColumns(['outlet', 'dateFormat', 'action'])
                    ->make(true);
            }

            return view('account.revenue.index', compact('outlets', 'mystore', 'userRole'));
        } catch (\Exception $e) {
            $bug = $e->getMessage();
            return redirect()->back()->with('error', $bug);
        }
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'date' => 'required',
            'name' => 'required|string',
            // 'job_id' => 'nullable|numeric',
            'amount' => 'required|numeric',
            'remark' => 'nullable|string'
        ]);

        $revenue = $request->all();
        DB::beginTransaction();
        try {
            $Revenue=Revenue::create($revenue);

            // $revenuesId = DB::table('revenues')->latest()->first()->id;

            CashTransection::create([
                'date' => $request->date,
                'outlet_id' => $request->outlet_id,
                'revenue_id' => $Revenue->id,
                'cash_in' => $request->amount,
                'remarks' => $request->remark,
                'belong_to' => 1,
                'created_by' => Auth::id(),
                'updated_by' => Auth::id(),
            ]);

            $cashIn = DB::table('cash_transections')
                    ->where('outlet_id', $request->outlet_id)
                    ->where('deleted_at', NULL)
                    ->sum('cash_in');
            $cashOut = DB::table('cash_transections')
                    ->where('outlet_id', $request->outlet_id)
                    ->where('deleted_at', NULL)
                    ->sum('cash_out');

            $current_balance = ($cashIn - $cashOut);
            $latestId = CashTransection::latest('id')->first()->id;
            $latest = CashTransection::find($latestId);
            $latest->current_balance  = $current_balance;
            $latest->update();

            DB::commit();
            return redirect()->route('revenue-index')
                    ->with('success', __('New revenue created successfully.'));
        } catch (\Exception $e) {
            DB::rollback();
            $bug = $e->getMessage();
            return redirect()->back()->with('error', $bug);
        }
    }

    public function edit($id)
    {
        try{
            $auth = Auth::user();
            $userRole =  $auth->roles->first();
            $mystore = "";

            $outlets = Outlet::select('id', 'name', 'status')->where('status', 1)->orderBy('name')->get();
            // $jobs = Job::select('id', 'job_number')->get();

            if($userRole->name == "Super Admin" || $userRole->name == "Admin"){
                $revenue = Revenue::with('outlet', 'job')->find($id);
            }else {
                $employee = Employee::where('user_id', Auth::user()->id)->first();
                $mystore = Outlet::where('id', $employee->outlet_id)->first();
                $revenue = Revenue::with('outlet', 'job')
                        ->where('outlet_id', $employee->outlet_id)
                        ->find($id);
            }

            return view('account.revenue.edit', compact('revenue', 'outlets', 'mystore', 'userRole'));
        } catch (\Exception $e) {
            $bug = $e->getMessage();
            return redirect()->back()->with('error', $bug);
        }
    }

    public function update($id, Request $request)
    {
        $this->validate($request, [
            'date' => 'required',
            'name' => 'required|string',
            // 'job_id' => 'nullable|numeric',
            'amount' => 'required|numeric',
            'remark' => 'nullable|string'
        ]);

        $updatedRevenue = $request->all();
        DB::beginTransaction();
        try {
            $revenue = Revenue::find($id);
            $revenue->update($updatedRevenue);

            $cashTransection = CashTransection::where('revenue_id', $id)->first();

            $cashTransection->update([
                'date' => $request->date,
                'outlet_id' => $request->outlet_id,
                'revenue_id' => $id,
                'cash_in' => $request->amount,
                'remarks' => $request->remark,
                'belong_to' => 1,
                'created_by' => Auth::id(),
                'updated_by' => Auth::id(),
            ]);

            $cashIn = DB::table('cash_transections')
                    ->where('outlet_id', $request->outlet_id)
                    ->where('deleted_at', NULL)
                    ->sum('cash_in');
            $cashOut = DB::table('cash_transections')
                    ->where('outlet_id', $request->outlet_id)
                    ->where('deleted_at', NULL)
                    ->sum('cash_out');

            $current_balance = ($cashIn - $cashOut);
            $latestId = CashTransection::latest('id')->first()->id;
            $latest = CashTransection::find($latestId);
            $latest->current_balance  = $current_balance;
            $latest->update();

            DB::commit();
            return redirect()->route('revenue-index')
                    ->with('success', __('Revenue Updated Successfully.'));
        } catch (\Exception $e) {
            DB::rollBack();
            $bug = $e->getMessage();
            return redirect()->back()->with('error', $bug);
        }
    }

    public function destroy($id)
    {
        try {
            Revenue::findOrFail($id)->delete();
            CashTransection::where('revenue_id', $id)
                    ->first()->delete();
                    return response()->json([
                        'success' => true,
                        'message' => 'Revenue deleted successfully.',
                    ]);
        } catch (\Exception $e) {
            $bug = $e->getMessage();
            return response()->json([
                'success' => false,
                'message' => $bug,
            ]);
        }
    }
}
