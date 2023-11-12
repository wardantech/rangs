<?php

namespace App\Http\Controllers\Account;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\Outlet\Outlet;
use App\Models\Inventory\Store;
use Yajra\DataTables\DataTables;
use App\Models\Employee\Employee;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\Account\CashTransection;
use App\Http\Requests\StoreCashTransectionRequest;

class CashTransectionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        try {
            $user = auth()->user();
            $userRole = $user->roles->first();
            $mystore = ($userRole && in_array($userRole->name, ["Super Admin", "Admin"])) ? "" : optional($user->employee)->outlet;

            if ($request->ajax()) {
                $data = CashTransection::with('outlet');

                if (!in_array($userRole->name, ["Super Admin", "Admin"])) {
                    $data->whereHas('outlet', function ($query) use ($mystore) {
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
                
                $transactions = $data->orderBy('created_at', 'desc');

                return DataTables::of($transactions)
                    ->addColumn('outlet', function ($transaction) {
                        return optional($transaction->outlet)->name;
                    })
                    ->addColumn('purpose', function ($transaction) {
                        return $transaction->getPurposeAttribute();
                    })
                    ->addColumn('dateFormat', function ($transaction) {
                        return Carbon::parse($transaction->created_at)->format('m/d/Y');
                    })
                    ->addColumn('cashIn', function ($transaction) {
                        return number_format($transaction->cash_in ?: $transaction->cash_out);
                    })
                    ->addColumn('action', function ($transaction) {
                        $canEdit = Auth::user()->can('edit');
                        $canDelete = Auth::user()->can('delete');
                        $canShow = Auth::user()->can('show');

                        $actions = [];

                        if ($canShow) {
                            $actions[] = '<a href="' . route('cash-transections.show', $transaction->id) . '" title="Show"><i class="ik ik-eye f-16 mr-15 text-blue"></i></a>';
                        }

                        if ($canEdit) {
                            $actions[] = '<a href="' . route('cash-transections.edit', $transaction->id) . '" title="Edit"><i class="ik ik-edit-2 f-16 mr-15 text-green"></i></a>';
                        }

                        if ($canDelete) {
                            $actions[] = '<a type="submit" onclick="showDeleteConfirm(' . $transaction->id . ')" title="Delete"><i class="ik ik-trash-2 f-16 text-red"></i></a>';
                        }

                        return '<div class="table-actions text-center">' . implode('', $actions) . '</div>';
                    })
                    ->addIndexColumn()
                    ->rawColumns(['outlet', 'dateFormat', 'cashIn', 'purpose', 'action'])
                    ->make(true);
            }

            return view('account.cash_transections.index', compact('mystore'));
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
            $auth = Auth::user();
            $userRole =  $auth->roles->first();
            $mystore = "";

            if ($userRole->name == "Super Admin" || $userRole->name == "Admin") {
                $outlets = Outlet::select('id', 'name', 'status')
                    ->where('status', 1)->orderBy('name')->get();
            } else {
                $outlets = "";
                $employee = Employee::where('user_id', Auth::user()->id)->first();
                $mystore = Store::where('outlet_id', $employee->outlet_id)->first();
            }
            return view('account.cash_transections.create', compact('outlets', 'mystore', 'userRole'));
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
    public function store(StoreCashTransectionRequest $request)
    {
        $purpose = $request->purpose;

        try {
            CashTransection::create([
                'date' => $request->date,
                'outlet_id' => $request->outlet_id,
                'cash_in' => $request->amount,
                'belong_to' => 1,
                'remarks' => $request->remarks,
                'created_by' => Auth::id(),
            ]);
            // This is for purpose wayes operations
            $cashIn = DB::table('cash_transections')->where('outlet_id', $request->outlet_id)->where('deleted_at', NULL)->sum('cash_in');
            $cashOut = DB::table('cash_transections')->where('outlet_id', $request->outlet_id)->where('deleted_at', NULL)->sum('cash_out');
            $current_balance = ($cashIn - $cashOut);

            $latestId = CashTransection::latest('id')->first()->id;
            $latest = CashTransection::find($latestId);
            $latest->current_balance  = $current_balance;
            $latest->update();

            return redirect()->route('cash-transections.index')
                ->with('success', 'Cash Received Successfully');
        } catch (\Exception $e) {
            $bug = $e->getMessage();
            return redirect()->route('cash-transections.index')->with('error', $bug);
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
            $transection = CashTransection::find($id);
            $outletId = $transection->outlet_id;

            $currentBalance = DB::table('cash_transections')
                ->where('outlet_id', $outletId)
                ->where('deleted_at', NULL)
                ->latest()->first()->current_balance;

            return view('account.cash_transections.show', compact('transection', 'currentBalance'));
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
            $auth = Auth::user();
            $userRole =  $auth->roles->first();
            $mystore = "";

            $outlets = Outlet::select('id', 'name', 'status')
                ->where('status', 1)->orderBy('name')->get();

            if ($userRole->name == "Super Admin" || $userRole->name == "Admin") {
                $transection = CashTransection::find($id);
            } else {
                $employee = Employee::where('user_id', Auth::user()->id)->first();
                $mystore = Store::where('outlet_id', $employee->outlet_id)->first();
                $transection = CashTransection::where('outlet_id', $employee->outlet_id)->find($id);
            }

            return view('account.cash_transections.edit', compact('transection', 'outlets', 'mystore', 'userRole'));
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
        // $purpose = $request->purpose;
        $cashTransection = CashTransection::find($id);

        try {
            $cashTransection->update([
                'date' => $request->date,
                'outlet_id' => $request->outlet_id,
                'cash_in' => ($cashTransection->cash_in) ? $request->amount : 0,
                'cash_out' => ($cashTransection->cash_out) ? $request->amount : 0,
                'belong_to' => 1,
                'remarks' => $request->remarks,
                'updated_by' => Auth::id(),
            ]);

            //This is for purpose wayes operations
            $cashIn = DB::table('cash_transections')->where('outlet_id', $cashTransection->outlet_id)->where('deleted_at', NULL)->sum('cash_in');
            $cashOut = DB::table('cash_transections')->where('outlet_id', $cashTransection->outlet_id)->where('deleted_at', NULL)->sum('cash_out');
            $current_balance = ($cashIn - $cashOut);

            $latestId = CashTransection::latest('id')->first()->id;
            $latest = CashTransection::find($latestId);
            $latest->current_balance  = $current_balance;
            $latest->update();

            return redirect()->route('cash-transections.index')
                ->with('success', 'Cash Received Updated Successfully');
        } catch (\Exception $e) {
            $bug = $e->getMessage();
            return redirect()->route('cash-transections.index')->with('error', $bug);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function delete($id)
    {
        try {
            $cashTransection = CashTransection::find($id);
            if ($cashTransection->deposit_id || $cashTransection->expense_id || $cashTransection->revenue_id) {
                return response()->json([
                    'success' => false,
                    'message' => "Can't Delete! You must delete it's parent data first",
                ]);
            } else {
                $cashTransection->delete();
                return response()->json([
                    'success' => true,
                    'message' => "Cash Transection Deleted Successfully",
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
