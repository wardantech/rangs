<?php

namespace App\Http\Controllers\Inventory;

use DB;
use Illuminate\Http\Request;
use App\Models\Ticket\Ticket;
use App\Models\Inventory\Fault;
use Yajra\DataTables\DataTables;
use App\Models\Inventory\Category;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class FaultController extends Controller
{
    public function index()
    {
        try {
            $faults = DB::table('faults')
                ->join('categories', 'categories.id', '=', 'faults.category_id')
                ->select('faults.*', 'categories.name as category_name')
                ->latest();
            $categories = Category::where('status', 1)->orderBy('id', 'desc')->get();

            if (request()->ajax()) {
                return DataTables::of($faults)

                    ->addColumn('status', function ($faults) {

                        if ($faults->status == true) {
                            $status = '<div class="text-center">
                                            <a href="' . route('inventory.fault.status', $faults->id) . '" title="Status" class="btn btn-sm btn-success">
                                                <i class="fas fa-arrow-up"></i>
                                            </a>
                                        </div>';
                        } else {
                            $status = '<div class="text-center">
                                        <a href="' . route('inventory.fault.status', $faults->id) . '" title="Status" class="btn btn-sm btn-secondary">
                                            <i class="fas fa-arrow-down"></i>
                                        </a>
                                    </div>';
                        }
                        return $status;
                    })

                    ->addColumn('action', function ($faults) {
                        if (Auth::user()->can('edit') && Auth::user()->can('delete')) {
                            return '<div class="table-actions text-center">
                                            <a href="' . route('inventory.fault.edit', $faults->id) . '" title="Edit"><i class="ik ik-edit-2 f-16 mr-15 text-green"></i></a>
                                            <a type="submit" onclick="showDeleteConfirm(' . $faults->id . ')" title="Delete"><i class="ik ik-trash-2 f-16 text-red"></i></a>
                                            </div>';
                        } elseif (Auth::user()->can('edit')) {
                            return '<div class="table-actions">
                                            <a href="' . route('inventory.fault.edit', $faults->id) . '" title="Edit"><i class="ik ik-edit-2 f-16 mr-15 text-green"></i></a>
                                            </div>';
                        } elseif (Auth::user()->can('delete')) {
                            return '<div class="table-actions">
                                            <a type="submit" onclick="showDeleteConfirm(' . $faults->id . ')" title="Delete"><i class="ik ik-trash-2 f-16 text-red"></i></a>
                                            </div>';
                        }
                    })
                    ->addIndexColumn()
                    ->rawColumns(['status', 'action'])
                    ->make(true);
            }


            return view('inventory.fault.index', compact('categories'));
        } catch (\Exception $e) {
            $bug = $e->getMessage();
            return redirect()->back()->with('error', $bug);
        }
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'category_id' => 'required'
        ]);
        try {
            $Fault = Fault::where('category_id', $request->category_id)->where('name', $request->name)->get();
            if (count($Fault) > 0) {
                return back()->with('error', "Sorry! Can't Create. The Fault Name Is Available For The Selected Category");
            } else {
                Fault::create([
                    'name' => $request->name,
                    'category_id' => $request->category_id

                ]);
                return redirect()->back()->with('success', 'Fault Created Successfully');
            }
        } catch (\Exception $e) {
            $bug = $e->getMessage();
            return redirect()->back()->with('error', $bug);
        }
    }

    public function edit($id)
    {
        try {
            $fault = Fault::findOrFail($id);
            $categories = Category::where('status', 1)->orderBy('name')->get();
            return view('inventory.fault.edit', compact('fault', 'categories'));
        } catch (\Exception $e) {
            $bug = $e->getMessage();
            return redirect()->back()->with('error', $bug);
        }
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required',
            'category_id' => 'required'
        ]);
        try {
            $fault = Fault::findOrFail($id);
            $Fault = Fault::where('category_id', $request->category_id)->where('name', $request->name)->get();
            if (count($Fault) > 0) {
                return back()->with('error', "Sorry! Can't Create. The Fault Name Is Available For The Selected Category");
            } else {
                $fault->update([
                    'name' => $request->name,
                    'category_id' => $request->category_id
                ]);
            }
            return redirect()->route('inventory.fault.index')
                ->with('success', 'Fault Updated Successfully');
        } catch (\Exception $e) {
            $bug = $e->getMessage();
            return redirect()->back()->with('error', $bug);
        }
    }

    public function destroy($id)
    {
        try {
            $fault = Fault::findOrFail($id);
            $Ticket = DB::table('tickets')
                ->where('deleted_at', NULL)
                ->where('fault_description_id', 'LIKE', '%' . $fault->id . '%')
                ->get();
            if (count($Ticket) > 0) {
                return response()->json([
                    'success' => false,
                    'message' => "Sorry! Can't Delete. This Fault is used in Ticket",
                ]);
            } else {
                $fault->delete();
                return response()->json([
                    'success' => true,
                    'message' => 'Fault Deleted Successfully.',
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
            $fault = Fault::findOrFail($id);

            if ($fault->status == false) {
                $fault->update([
                    'status' => true
                ]);

                return back()->with('success', __('Fault active now'));
            } elseif ($fault->status == true) {
                $fault->update([
                    'status' => false
                ]);

                return back()->with('success', __('Fault inactive now'));
            }

            return back()->with('error', __('Action decline'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}
