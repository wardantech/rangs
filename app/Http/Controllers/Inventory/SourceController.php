<?php

namespace App\Http\Controllers\Inventory;

use Illuminate\Http\Request;
use App\Models\Inventory\Source;
use Yajra\DataTables\DataTables;
use App\Models\Inventory\Inventory;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class SourceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $sources = Source::orderBy('id', 'desc');
        if (request()->ajax()) {
            return DataTables::of($sources)

                ->addColumn('status', function ($sources) {

                    if ($sources->status == true) {
                        $status = '<div class="text-center">
                                            <a href="' . route('inventory.source.status', $sources->id) . '" title="Status" class="btn btn-sm btn-success">
                                                <i class="fas fa-arrow-up"></i>
                                            </a>
                                        </div>';
                    } else {
                        $status = '<div class="text-center">
                                        <a href="' . route('inventory.source.status', $sources->id) . '" title="Status" class="btn btn-sm btn-secondary">
                                            <i class="fas fa-arrow-down"></i>
                                        </a>
                                    </div>';
                    }
                    return $status;
                })

                ->addColumn('action', function ($sources) {
                    if (Auth::user()->can('edit') && Auth::user()->can('delete')) {
                        return '<div class="table-actions text-center">
                                            <a  onclick="editCategory(' . $sources->id . ')" class="edit-btn" data-toggle="modal" data-target="#editModal"><i class="ik ik-edit f-16 mr-15 text-blue" aria-hidden="true"></i></a>

                                            <a type="submit" onclick="showDeleteConfirm(' . $sources->id . ')" title="Delete"><i class="ik ik-trash-2 f-16 text-red"></i></a>
                                            </div>';
                    } elseif (Auth::user()->can('edit')) {
                        return '<div class="table-actions">
                                            <a  onclick="editCategory(' . $sources->id . ')" class="edit-btn" data-toggle="modal" data-target="#editModal"><i class="ik ik-edit f-16 mr-15 text-blue" aria-hidden="true"></i></a>
                                            </div>';
                    } elseif (Auth::user()->can('delete')) {
                        return '<div class="table-actions">
                                            <a type="submit" onclick="showDeleteConfirm(' . $sources->id . ')" title="Delete"><i class="ik ik-trash-2 f-16 text-red"></i></a>
                                            </div>';
                    }
                })
                ->addIndexColumn()
                ->rawColumns(['status', 'action'])
                ->make(true);
        }
        return view('inventory.source.index', compact('sources'));
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
            'name' => 'required|unique:sources,name,NULL,id,deleted_at,NULL',
        ]);

        try {
            $source = new Source();
            $source->name = $request->name;
            $source->save();

            return back()->with('success', 'Source added Successfully.');
        } catch (\Exception $e) {
            $bug = $e->getMessage();
            return redirect()->back()->with('error', $bug);
        }
    }

    //edit
    public function edit(Request $request)
    {
        $Source  = Source::findOrfail($request->id);
        return Response()->json($Source);
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
            'name' => 'required|unique:sources,name,' . $request->source_id,
        ]);
        try {
            $source = Source::find($request->source_id);
            $source->name = $request->name;
            $source->save();
            return back()->with('success', 'Source Updated Successfully.');
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
        try {
            $source = Source::findOrFail($id); //source_id
            $inventory = Inventory::where('source_id', $source->id)->get();
            if (count($inventory) > 0) {
                return response()->json([
                    'success' => false,
                    'message' => "Sorry! Can't Delete. This Source is used in Parts Received Section",
                ]);
            } else {
                $source->delete();
                return response()->json([
                    'success' => true,
                    'message' => 'Source Deleted Successfully.',
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
            $source = Source::findOrFail($id);

            if ($source->status == false) {
                $source->update([
                    'status' => true,
                ]);

                return back()->with('success', __('Source active now'));
            } elseif ($source->status == true) {
                $source->update([
                    'status' => false,
                ]);

                return back()->with('success', __('Source inactive now'));
            }

            return back()->with('error', __('Action decline'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}
