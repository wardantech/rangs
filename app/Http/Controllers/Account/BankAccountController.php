<?php

namespace App\Http\Controllers\Account;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\Account\Deposit;
use Yajra\DataTables\DataTables;
use App\Models\Account\BankAccount;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class BankAccountController extends Controller
{
    public function index()
    {
        try {
            $bankAccounts = BankAccount::latest();
            if (request()->ajax()) {
                return DataTables::of($bankAccounts)

                    ->addColumn('dateFormat', function ($bankAccounts) {
                        $data = Carbon::parse($bankAccounts->date)->format('m/d/Y');
                        return $data;
                    })

                    ->addColumn('action', function ($bankAccounts) {
                        if (Auth::user()->can('edit') && Auth::user()->can('delete')) {
                            return '<div class="table-actions text-center">
                                            <a href="' . route('edit.bank-account', $bankAccounts->id) . '" title="Edit"><i class="ik ik-edit-2 f-16 mr-15 text-green"></i></a>
                                            <a type="submit" onclick="showDeleteConfirm(' . $bankAccounts->id . ')" title="Delete"><i class="ik ik-trash-2 f-16 text-red"></i></a>
                                            </div>';
                        } elseif (Auth::user()->can('edit')) {
                            return '<div class="table-actions">
                                            <a href="' . route('edit.bank-account', $bankAccounts->id) . '" title="Edit"><i class="ik ik-edit-2 f-16 mr-15 text-green"></i></a>
                                            </div>';
                        } elseif (Auth::user()->can('delete')) {
                            return '<div class="table-actions">
                                            <a type="submit" onclick="showDeleteConfirm(' . $bankAccounts->id . ')" title="Delete"><i class="ik ik-trash-2 f-16 text-red"></i></a>
                                            </div>';
                        }
                    })
                    ->addIndexColumn()
                    ->rawColumns(['action', 'dateFormat'])
                    ->make(true);
            }
            return view('account.bank_account.index', compact('bankAccounts'));
        } catch (\Exception $e) {
            $bug = $e->getMessage();
            return redirect()->back()->with('error', $bug);
        }
    }

    public function store(Request $request)
    {

        $this->validate($request, [
            'date' => 'required',
            'bank_name' => 'required|string',
            'account_no' => 'required|unique:bank_accounts,account_no,NULL,id,deleted_at,NULL',
        ]);

        try {
            $bankAccount = $request->all();
            BankAccount::create($bankAccount);
            return redirect()->route('bank-account-index')->with('success', __('New bank account created successfully.'));
        } catch (\Exception $e) {
            $bug = $e->getMessage();
            return redirect()->back()->with('error', $bug);
        }
    }

    public function edit($id)
    {
        try {
            $bankAccount = BankAccount::find($id);
            return view('account.bank_account.edit', compact('bankAccount'));
        } catch (\Exception $e) {
            $bug = $e->getMessage();
            return redirect()->back()->with('error', $bug);
        }
    }

    public function update($id, Request $request)
    {
        $this->validate($request, [
            'date' => 'required',
            'bank_name' => 'required|string',
            'account_no' => 'required|unique:bank_accounts,account_no,' . $id,
        ]);

        try {
            $updatedBankAccount = $request->all();
            $bankAccount = BankAccount::find($id);
            $bankAccount->update($updatedBankAccount);
            return redirect()->route('bank-account-index')->with('success', __('Bank Account Updated Successfully.'));
        } catch (\Exception $e) {
            $bug = $e->getMessage();
            return redirect()->back()->with('error', $bug);
        }
    }

    public function destroy($id)
    {
        try {
            $bankAccount = BankAccount::findOrFail($id);
            $deposit = Deposit::where('account_id', $bankAccount->id)->get();
            if (count($deposit) > 0) {
                return response()->json([
                    'success' => false,
                    'message' => "Sorry! Can't Delete. This Bank Account is used in Deposit",
                ]);
            } else {
                $bankAccount->delete();
                return response()->json([
                    'success' => true,
                    'message' => 'Bank Account Deleted Successfully.',
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
}
