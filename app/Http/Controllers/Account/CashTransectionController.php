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
    public function index()
    {
        try {
            $auth = Auth::user();
            $userRole =  $auth->roles->first();
            $mystore = "";
            if ($userRole->name == "Super Admin" || $userRole->name == "Admin") {
                $transections = CashTransection::with('outlet')->orderBy('date', 'desc')->get();
            } else {
                $employee = Employee::where('user_id', Auth::user()->id)->first();
                $mystore = Outlet::where('id', $employee->outlet_id)->first();
                $transections = CashTransection::with('outlet')
                    ->where('outlet_id', $employee->outlet_id)
                    ->orderBy('date', 'desc');
            }

            if (request()->ajax()) {
                return DataTables::of($transections)
                    ->addColumn('outlet', function ($transections) {
                        $data = isset($transections->outlet) ? $transections->outlet->name : null;
                        return $data;
                    })

                    ->addColumn('purpose', function ($transections) {
                        if ($transections->deposit_id) {
                            return 'Deposite';
                        } elseif ($transections->expense_id) {
                            return 'Expense';
                        } elseif ($transections->revenue_id) {
                            return 'Revenue';
                        } else {
                            return 'Cash Received';
                        }
                    })

                    ->addColumn('dateFormat', function ($transections) {
                        $data = Carbon::parse($transections->date)->format('m/d/Y');
                        return $data;
                    })

                    ->addColumn('cashIn', function ($transections) {
                        if ($transections->cash_in) {
                            return number_format($transections->cash_in);
                        } else {
                            return number_format($transections->cash_out);
                        }
                    })

                    ->addColumn('action', function ($transections) {
                        if (Auth::user()->can('edit') && Auth::user()->can('delete') && Auth::user()->can('show')) {
                            return '<div class="table-actions text-center">
                                            <a href="' . route('cash-transections.show', $transections->id) . '" title="Show"><i class="ik ik-eye f-16 mr-15 text-blue"></i></a>
                                            <a href="' . route('cash-transections.edit', $transections->id) . '" title="Edit"><i class="ik ik-edit-2 f-16 mr-15 text-green"></i></a>
                                            <a type="submit" onclick="showDeleteConfirm(' . $transections->id . ')" title="Delete"><i class="ik ik-trash-2 f-16 text-red"></i></a>
                                            </div>';
                        } elseif (Auth::user()->can('edit')) {
                            return '<div class="table-actions">
                                            <a href="' . route('cash-transections.edit', $transections->id) . '" title="Edit"><i class="ik ik-edit-2 f-16 mr-15 text-green"></i></a>
                                            </div>';
                        } elseif (Auth::user()->can('show')) {
                            return '<div class="table-actions">
                                           <a href="' . route('cash-transections.show', $transections->id) . '" title="Show"><i class="ik ik-eye f-16 mr-15 text-blue"></i></a>
                                            </div>';
                        } elseif (Auth::user()->can('delete')) {
                            return '<div class="table-actions">
                                            <a type="submit" onclick="showDeleteConfirm(' . $transections->id . ')" title="Delete"><i class="ik ik-trash-2 f-16 text-red"></i></a>
                                            </div>';
                        }
                    })
                    ->addIndexColumn()
                    ->rawColumns(['outlet', 'dateFormat', 'cashIn', 'purpose', 'action'])
                    ->make(true);
            }
            return view('account.cash_transections.index', compact('transections', 'mystore'));
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
