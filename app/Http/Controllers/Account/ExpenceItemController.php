<?php

namespace App\Http\Controllers\Account;

use Illuminate\Http\Request;
use App\Models\Account\Expense;
use Yajra\DataTables\DataTables;
use App\Models\Account\ExpenseItem;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class ExpenceItemController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try{
            $expenceItems = ExpenseItem::latest();
            if (request()->ajax()) {
                return DataTables::of($expenceItems)

                    ->addColumn('status', function ($expenceItems) {

                        if ($expenceItems->status == true) {
                            $status = '<div class="text-center">
                                            <a href="' . route('expense-items.status', $expenceItems->id) . '" title="Status" class="btn btn-sm btn-success">
                                                <i class="fas fa-arrow-up"></i>
                                            </a>
                                        </div>';
                        } else {
                            $status = '<div class="text-center">
                                        <a href="' . route('expense-items.status', $expenceItems->id) . '" title="Status" class="btn btn-sm btn-secondary">
                                            <i class="fas fa-arrow-down"></i>
                                        </a>
                                    </div>';
                        }
                        return $status;
                    })

                    ->addColumn('action', function ($expenceItems) {
                        if (Auth::user()->can('edit') && Auth::user()->can('delete')) {
                            return '<div class="table-actions text-center">
                                            <a href="' . route('expense-items.edit', $expenceItems->id) . '" title="Edit"><i class="ik ik-edit-2 f-16 mr-15 text-green"></i></a>
                                            <a type="submit" onclick="showDeleteConfirm(' . $expenceItems->id . ')" title="Delete"><i class="ik ik-trash-2 f-16 text-red"></i></a>
                                            </div>';
                        } elseif (Auth::user()->can('edit')) {
                            return '<div class="table-actions">
                                            <a href="' . route('expense-items.edit', $expenceItems->id) . '" title="Edit"><i class="ik ik-edit-2 f-16 mr-15 text-green"></i></a>
                                            </div>';
                        } elseif (Auth::user()->can('delete')) {
                            return '<div class="table-actions">
                                            <a type="submit" onclick="showDeleteConfirm(' . $expenceItems->id . ')" title="Delete"><i class="ik ik-trash-2 f-16 text-red"></i></a>
                                            </div>';
                        }
                    })
                    ->addIndexColumn()
                    ->rawColumns(['action', 'status'])
                    ->make(true);
            }
            return view('account.expense.items.index', compact('expenceItems'));
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
            'name' => 'required|string|unique:expense_items,name',
            'status' => 'required|numeric'
        ]);

        try{
            ExpenseItem::create([
                'name' => $request->name,
                'status' => $request->status
            ]);

            return back()->with('success', __('Expense item created successfully.'));
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
        try{
            $expenseItem = ExpenseItem::find($id);
            return view('account.expense.items.edit', compact('expenseItem'));
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
        $this->validate($request, [
            'name' => 'required|string|unique:expense_items,name,' . $id,
            'status' => 'required|numeric'
        ]);

        try{
            ExpenseItem::find($id)->update([
                'name' => $request->name,
                'status' => $request->status
            ]);

            return redirect()->route('expense-items.index')->with('success', __('Expense Item Updated Successfully.'));
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
        try{
            $expenseItem=ExpenseItem::find($id);
                $expense=Expense::where('expense_item_id',$expenseItem->id)->get();
                if(count($expense) > 0){
                    return response()->json([
                        'success' => false,
                        'message' => "Sorry! Can't Delete. This ExpenseItem is used in Expense Management",
                    ]);
                }else{
                    $expenseItem->delete();
                    return response()->json([
                        'success' => true,
                        'message' => 'Expense item Deleted Successfully.',
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

    public function activeInactive($id)
    {
        try {
            $expenseItem = ExpenseItem::findOrFail($id);

            if($expenseItem->status == false) {
                $expenseItem->update([
                    'status' =>true
                ]);

                return back()->with('success', __('Expense item active now'));
            } elseif ($expenseItem->status == true) {
                $expenseItem->update([
                    'status' => false
                ]);

                return back()->with('success', __('Expense item inactive now'));
            }

            return back()->with('error', __('Action decline'));
        }catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}
