<?php

namespace App\Http\Controllers\Account;

use DB;
use Illuminate\Http\Request;
use App\Models\Outlet\Outlet;
use App\Models\Employee\Employee;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\Account\CashTransection;

class AccountBranchController extends Controller
{
    public function index()
    {
        try{
            $outlets = Outlet::with('transactions')->where('status', 1)->get();

            $collectBalance = [];
            foreach($outlets as $outlet)
            {
                $cashIn = DB::table('cash_transections')->where('outlet_id',$outlet->id)->where('deleted_at', NULL)->sum('cash_in');
                $cashOut = DB::table('cash_transections')->where('outlet_id',$outlet->id)->where('deleted_at', NULL)->sum('cash_out');
                $balance = ($cashIn - $cashOut);
                //array_push($collectBalance,$balance);
                $collectBalance[] = $balance;
            }

            return view('account.branch.index', compact('outlets','collectBalance'));
        } catch (\Exception $e) {
            $bug = $e->getMessage();
            return redirect()->back()->with('error', $bug);
        }
    }

    public function show($id)
    {
        try{
            $outlet = Outlet::with('transactions')
                    ->where('id', $id)
                    ->latest()->first();

            // This is for purpose wayes operations
            $cashIn = DB::table('cash_transections')->where('outlet_id',$outlet->id)->where('deleted_at', NULL)->sum('cash_in');
            $cashOut = DB::table('cash_transections')->where('outlet_id',$outlet->id)->where('deleted_at', NULL)->sum('cash_out');
            $balance = ($cashIn - $cashOut);
            return view('account.branch.show', compact('outlet', 'balance'));
        } catch (\Exception $e) {
            $bug = $e->getMessage();
            return redirect()->back()->with('error', $bug);
        }
    }

    public function pettycash()
    {
        try{
            $auth = Auth::user();
            $userRole =  $auth->roles->first();
            $mystore = "";
            if($userRole->name == "Super Admin" || $userRole->name == "Admin") {
                $outlets = Outlet::with('transactions')->where('status', 1)->get();
                $collectBalance = [];
                foreach($outlets as $outlet)
                {
                    // This is for purpose wayes operations
                    $cashIn = DB::table('cash_transections')->where('outlet_id',$outlet->id)->where('deleted_at', NULL)->sum('cash_in');
                    $cashOut = DB::table('cash_transections')->where('outlet_id',$outlet->id)->where('deleted_at', NULL)->sum('cash_out');
                    $current_balance = ($cashIn - $cashOut);
                    array_push($collectBalance,$current_balance);
                }
                return view('account.branch.index', compact('outlets','collectBalance'));
            } else {
                $employee = Employee::where('user_id', Auth::user()->id)->first();

                $outlet = Outlet::with('transactions')
                ->where('id', $employee->outlet_id)->first();
                $cashIn = DB::table('cash_transections')->where('outlet_id',$outlet->id)->where('deleted_at', NULL)->sum('cash_in');
                $cashOut = DB::table('cash_transections')->where('outlet_id',$outlet->id)->where('deleted_at', NULL)->sum('cash_out');

                $balance = ($cashIn - $cashOut);
                return view('account.cash_transections.pettycash', compact('outlet', 'balance'));
            }
        } catch (\Exception $e) {
            $bug = $e->getMessage();
            return redirect()->back()->with('error', $bug);
        }
    }
}
