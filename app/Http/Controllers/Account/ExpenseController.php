<?php

namespace App\Http\Controllers\Account;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\Outlet\Outlet;
use App\Models\Account\Expense;
use App\Models\Inventory\Store;
use Yajra\DataTables\DataTables;
use App\Models\Employee\Employee;
use Illuminate\Support\Facades\DB;
use App\Models\Account\ExpenseItem;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\Account\CashTransection;

class ExpenseController extends Controller
{
    public function index(Request $request)
    {
        try{
            $user = auth()->user();
            $userRole =  $user->roles->first();
            $mystore = ($userRole && in_array($userRole->name,['Super Admin', 'Admin'])) ? " " : optional($user->employee)->outlet;

            $outlets = Outlet::select('id', 'name', 'status')->where('status', 1)->orderBy('name')->get();
            $expenseItems = ExpenseItem::select('id', 'name')
                            ->where('status', 1)
                            ->orderBy('name')
                            ->get();


            if ($request->ajax()) {

                $data = Expense::with('outlet','expenseItem');
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

                $expenses=$data->orderBy('created_at', 'desc');

                return DataTables::of($expenses)

                    ->addColumn('outlet', function ($expenses) {
                        $data = $expenses->outlet->name ?? null;
                        return $data;
                    })

                    ->addColumn('expenseItem', function ($expenses) {
                        $data = $expenses->expenseItem->name ?? null;
                        return $data;
                    })

                    ->addColumn('dateFormat', function ($expenses) {
                        $data = Carbon::parse($expenses->created_at)->format('m/d/Y');
                        return $data;
                    })

                    ->addColumn('numberFormat', function ($expenses) {
                        $number = number_format($expenses->amount);
                        return $number;
                    })

                    ->addColumn('action', function ($expenses) use ($userRole) {
                        $canEdit = Auth::user()->can('edit');
                        $canShow = Auth::user()->can('show');

                        $actions = [];

                        if ($canEdit) {
                            $actions[] = '<a href="' . route('edit.expense', $expenses->id) . '" title="Edit"><i class="ik ik-edit-2 f-16 mr-15 text-green"></i></a>';
                        }

                        if ($canDelete) {
                            $actions[] = '<a type="submit" onclick="showDeleteConfirm(' . $expenses->id . ')" title="Delete"><i class="ik ik-trash-2 f-16 text-red"></i></a>';
                        }

                        return '<div class="table-actions text-center">' . implode('', $actions) . '</div>';
                    })
                    ->addIndexColumn()
                    ->rawColumns(['outlet', 'dateFormat', 'numberFormat', 'expenseItem', 'action'])
                    ->make(true);
            }

            return view('account.expense.index', compact( 'outlets', 'expenseItems', 'mystore', 'userRole'));
        } catch (\Exception $e) {
            $bug = $e->getMessage();
            return redirect()->back()->with('error', $bug);
        }
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'outlet_id' => 'required',
            'date' => 'required',
            'amount' => 'required|numeric',
            'expense_item_id' => 'required',
            'remark' => 'nullable|string'
        ]);

        $expense = $request->all();
        DB::beginTransaction();
        try {
            $expense=Expense::create($expense);
            // $expenseId = DB::table('expenses')->latest()->first()->id;

            CashTransection::create([
                'date' => $request->date,
                'outlet_id' => $request->outlet_id,
                'expense_id' => $expense->id,
                'cash_out' => $request->amount,
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
            return redirect()->route('expense-index')
                    ->with('success', __('New expense created successfully.'));
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
            $expenseItems = ExpenseItem::where('status', 1)->select('id', 'name')
                            ->orderBy('name')
                            ->get();

            if($userRole->name == "Super Admin" || $userRole->name == "Admin") {
                $expense = Expense::findOrFail($id);
            }else {
                $employee = Employee::where('user_id', Auth::user()->id)->first();
                $mystore = Outlet::where('id', $employee->outlet_id)->first();
                $expense = Expense::where('outlet_id', $employee->outlet_id)->find($id);
            }

            return view('account.expense.edit', compact('expense','outlets', 'expenseItems', 'mystore', 'userRole'));
        } catch (\Exception $e) {
            $bug = $e->getMessage();
            return redirect()->back()->with('error', $bug);
        }
    }

    public function update($id, Request $request){
        $this->validate($request, [
            'date' => 'required',
            'amount' => 'required|numeric',
            'expense_item_id' => 'required',
            'remark' => 'nullable|string'
        ]);

        $updatedExpense = $request->all();
        DB::beginTransaction();
        try {
            $expense = Expense::find($id);
            $expense->update($updatedExpense);
            $cashTransection = CashTransection::where('expense_id', $id)->first();

            $cashTransection->update([
                'date' => $request->date,
                'outlet_id' => $request->outlet_id,
                'expense_id' => $id,
                'cash_out' => $request->amount,
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
            return redirect()->route('expense-index')
                    ->with('success', __('Expense Updated Successfully.'));
        } catch (\Exception $e) {
            DB::rollBack();
            $bug = $e->getMessage();
            return redirect()->back()->with('error', $bug);
        }
    }

    public function destroy($id)
    {
        try {
            Expense::findOrFail($id)->delete();
            CashTransection::where('expense_id', $id)
                    ->first()->delete();

                    return response()->json([
                        'success' =>true,
                        'message' => 'Expense deleted successfully.',
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
