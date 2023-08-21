<?php

namespace App\Http\Controllers\Ticket;

use DB;
use Redirect;
use Validator;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use App\Models\Ticket\WarrantyType;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class WarrantyTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try{
            $warranty_types = WarrantyType::orderBy('id','desc');
            if (request()->ajax()) {
                return DataTables::of($warranty_types)

                    ->addColumn('status', function ($warranty_types) {
                        if ($warranty_types->status == true) {
                            $status = '<div class="text-center">
                                            <a href="' . route('warranty-types.status', $warranty_types->id) . '" title="Status" class="btn btn-sm btn-success">
                                                <i class="fas fa-arrow-up"></i>
                                            </a>
                                        </div>';
                        } else {
                            $status = '<div class="text-center">
                                        <a href="' . route('warranty-types.status', $warranty_types->id) . '" title="Status" class="btn btn-sm btn-secondary">
                                            <i class="fas fa-arrow-down"></i>
                                        </a>
                                    </div>';
                        }
                        return $status;
                    })

                    ->addColumn('action', function ($warranty_types) {
                        if (Auth::user()->can('edit') && Auth::user()->can('delete')) {
                            return '<div class="table-actions text-center">
                                            <a href="' . route('warranty-types.edit', $warranty_types->id) . '" title="Edit"><i class="ik ik-edit-2 f-16 mr-15 text-green"></i></a>
                                            <a type="submit" onclick="showDeleteConfirm(' . $warranty_types->id . ')" title="Delete"><i class="ik ik-trash-2 f-16 text-red"></i></a>
                                            </div>';
                        } elseif (Auth::user()->can('edit')) {
                            return '<div class="table-actions">
                                            <a href="' . route('warranty-types.edit', $warranty_types->id) . '" title="Edit"><i class="ik ik-edit-2 f-16 mr-15 text-green"></i></a>
                                            </div>';
                        } elseif (Auth::user()->can('delete')) {
                            return '<div class="table-actions">
                                            <a type="submit" onclick="showDeleteConfirm(' . $warranty_types->id . ')" title="Delete"><i class="ik ik-trash-2 f-16 text-red"></i></a>
                                            </div>';
                        }
                    })
                    ->addIndexColumn()
                    ->rawColumns(['status', 'action'])
                    ->make(true);
            }
            return view('ticket.warranty_type.index',compact('warranty_types'));
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
            'warranty_type' => 'string|required|max:100',
            'status' => 'required'
        ]);

        try {
            $data = $request->all();
            WarrantyType::create($data);
            return back()->with('success', __('label.NEW_WARRANTY_TYPE_CREATED'));
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
            $warranty = WarrantyType::findOrFail($id);
            return view('ticket.warranty_type.edit', compact('warranty'));
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
            'warranty_type' => 'string | required|max:100',
            'status' => 'required'
        ]);

        try {
            $warranty = WarrantyType::findOrFail($id);
            $warranty->warranty_type = $request->warranty_type;
            $warranty->status = $request->status;
            $warranty->update();
            return redirect()->route('warranty-types.index')
                ->with('success', __('label.NEW_WARRANTY_TYPE_UPDATED'));
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
            $warrantyType=WarrantyType::findOrFail($id);
            $Ticket = DB::table('tickets')
            ->where('deleted_at', NULL)
            ->where('warranty_type_id', 'LIKE','%'.$warrantyType->id.'%')
            ->get();
            if(count($Ticket) > 0){
                return response()->json([
                    'success' => false,
                    'message' => "Sorry! Can't Delete. This Warranty Type Type is used in Ticket Management",
                ]);
            }else{
                $warrantyType->delete();
                return response()->json([
                    'success' => true,
                    'message' => "Warranty Type deleted successfully",
                ]);
            }
            // return back()->with('success', __('label.WARRANTY_TYPE_DELETED'));
        } catch (\Exception $e) {
            $bug = $e->getMessage();
            return response()->json([
                'success' => false,
                'message' =>  $bug,
            ]);
        }
    }

    public function activeInactive($id)
    {
        try {
            $warrantyType = WarrantyType::findOrFail($id);

            if($warrantyType->status == false) {
                $warrantyType->update([
                    'status' => true
                ]);

                return back()->with('success', __('Warranty type active now'));
            }elseif ($warrantyType->status == true) {
                $warrantyType->update([
                    'status' => false
                ]);

                return back()->with('success', __('Warranty type inactive now'));
            }

            return back()->with('error', __('Action decline'));
        }catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}
