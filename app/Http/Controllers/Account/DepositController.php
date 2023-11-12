<?php

namespace App\Http\Controllers\Account;

use Session;
use Redirect;
use Validator;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\Outlet\Outlet;
use App\Models\Account\Deposit;
use App\Models\Inventory\Store;
use Yajra\DataTables\DataTables;
use App\Models\Employee\Employee;
use Illuminate\Support\Facades\DB;
use App\Models\Account\BankAccount;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\Account\CashTransection;

class DepositController extends Controller
{
    public function index(Request $request)
    {
        try{
            $user = auth()->user();
            $userRole = $user->roles->first();
            $mystore = ($userRole && in_array($userRole->name, ["Super Admin", "Admin"])) ? "" : optional($user->employee)->outlet;

            $bankAccounts = BankAccount::all();
            $outlets = Outlet::select('id', 'name', 'status')
                        ->where('status', 1)->orderBy('name')->get();

            if ($request->ajax()) {

                $data = Deposit::with('outlet');

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
                
                $deposits = $data->orderBy('created_at', 'desc');

                return DataTables::of($deposits)

                    ->addColumn('dateFormat', function ($deposits) {
                        $data = Carbon::parse($deposits->date)->format('m/d/Y');
                        return $data;
                    })

                    ->addColumn('outletName', function ($deposits) {
                        $branch = $deposits->outlet->name ?? null;
                        return $branch;
                    })

                    ->addColumn('bankName', function ($deposits) {
                        $bank = $deposits->bank->account_no ?? null;
                        return 'A/C-' . $bank;
                    })

                    ->addColumn('depositName', function ($deposits) {
                        if ($deposits->deposit_type == 'cash'){
                             $data = $deposits->deposit_type;
                        } elseif ($deposits->deposit_type == 'cheque'){
                            $data =  $deposits->deposit_type .'</br>'.'Cheque No : '. $deposits->cheque_nunber;
                        }
                       return '<td class="text-capitalize">'. $data.'</td>';
                    })

                    ->addColumn('amountFormat', function ($deposits) {
                            $amount = number_format($deposits->amount) ;
                        return $amount;
                    })

                    ->addColumn('action', function ($deposits) use ($userRole) {
                        $canEdit = Auth::user()->can('edit');
                        $canDelete = Auth::user()->can('delete');;

                        $actions = [];

                        if ($canEdit) {
                            $actions[] = '<a href="' . route('edit.deposit', $deposits->id) . '" title="Edit"><i class="ik ik-edit-2 f-16 mr-15 text-green"></i></a>';
                        }

                        if ($canDelete) {
                            $actions[] = '<a type="submit" onclick="showDeleteConfirm(' . $deposits->id . ')" title="Delete"><i class="ik ik-trash-2 f-16 text-red"></i></a>';
                        }

                        return '<div class="table-actions text-center">' . implode('', $actions) . '</div>';

                    })
                    ->addIndexColumn()
                    ->rawColumns(['dateFormat', 'outletName', 'bankName', 'depositName', 'amountFormat', 'action'])
                    ->make(true);
            }


            return view('account.deposit.index', compact('bankAccounts', 'outlets', 'mystore', 'userRole'));
        } catch (\Exception $e) {
            $bug = $e->getMessage();
            return redirect()->back()->with('error', $bug);
        }
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'date' => 'required|date',
            'outlet_id' => 'required',
            'deposit_type' => 'required|string|max:50',
            'account_id' => 'required|integer',
            'amount' => 'required|numeric',
            'remark' => 'nullable|string',
            'cheque_nunber' => 'nullable',
        ]);

        $deposit = $request->all();
        DB::beginTransaction();
        if($request->has('deposit_type') && $request->deposit_type == 'cash'){
            $deposit['deposit_type'] = 'cash';
        }
        try{
            $deposit=Deposit::create($deposit);
            // $depositId = DB::table('deposits')->latest()->first()->id;

            CashTransection::create([
                'date' => $request->date,
                'outlet_id' => $request->outlet_id,
                'deposit_id' => $deposit->id,
                'cash_out' => $request->amount,
                'type' => $request->deposit_type,
                'cheque_number' => ($request->cheque_number) ? $request->cheque_number : null,
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
            return redirect()->route('deposit-index')
                        ->with('success', __('New deposit created successfully.'));
        }
        catch(\Exception $e){
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

            $bankAccounts = BankAccount::all();
            $outlets = Outlet::select('id', 'name', 'status')->where('status', 1)->orderBy('name')->get();

            if($userRole->name == "Super Admin" || $userRole->name == "Admin") {
                $deposit = Deposit::findOrFail($id);
            }else {
                $employee = Employee::where('user_id', Auth::user()->id)->first();
                $mystore = Outlet::where('id', $employee->outlet_id)->first();
                $deposit = Deposit::where('outlet_id', $employee->outlet_id)
                            ->findOrFail($id);
            }
            return view('account.deposit.edit', compact('deposit', 'bankAccounts', 'outlets', 'mystore', 'userRole'));
        } catch (\Exception $e) {
            $bug = $e->getMessage();
            return redirect()->back()->with('error', $bug);
        }
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'date' => 'required|date',
            'outlet_id' => 'required',
            'deposit_type' => 'required|string|max:50',
            'account_id' => 'required|integer',
            'amount' => 'required|numeric',
            'remark' => 'nullable|string',
            'cheque_nunber' => 'nullable',
        ]);

        DB::beginTransaction();
        $data = $request->all();
        if($request->has('deposit_type') && $request->deposit_type == 'cash'){
            $data['deposit_type'] = 'cash';
        }
        try{
            $deposit = Deposit::find($id);
            $deposit->outlet_id = $data['outlet_id'];
            $deposit->date = $data['date'];
            $deposit->deposit_type = $data['deposit_type'];
            $deposit->amount = $data['amount'];
            $deposit->account_id  = $data['account_id'];
            $deposit->remark = $data['remark'];
            $deposit->cheque_nunber	 = $data['cheque_nunber'];
            $deposit->save();

            $cashTransection = CashTransection::where('deposit_id', $id)->first();

            $cashTransection->update([
                'date' => $request->date,
                'outlet_id' => $request->outlet_id,
                'deposit_id' => $id,
                'cash_out' => $request->amount,
                'remarks' => $request->remark,
                'belong_to' => 1,
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
            return redirect()->route('deposit-index')
                    ->with('success', __('Deposit Updated Successfully.'));
        }
        catch(\Exception $e){
            DB::rollBack();
            $bug = $e->getMessage();
            return redirect()->back()->with('error', $bug);
        }
    }

    public function destroy($id)
    {
        try {
            Deposit::findOrFail($id)->delete();
            CashTransection::where('deposit_id', $id)
                    ->first()->delete();
            return response()->json([
                'success' => true,
                'message' => "Deposit deleted successfully",
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
