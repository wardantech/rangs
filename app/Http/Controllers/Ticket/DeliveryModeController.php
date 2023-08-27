<?php

namespace App\Http\Controllers\Ticket;

use DB;
use Validator;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use App\Models\Ticket\DeliveryMode;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class DeliveryModeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try{
            $deliveryModes=DeliveryMode::orderBy('id','desc');
            if (request()->ajax()) {
                return DataTables::of($deliveryModes)

                    ->addColumn('status', function ($deliveryModes) {
                        if ($deliveryModes->status == true) {
                            $status = '<div class="text-center">
                                            <a href="' . route('delivery-mode.status', $deliveryModes->id) . '" title="Status" class="btn btn-sm btn-success">
                                                <i class="fas fa-arrow-up"></i>
                                            </a>
                                        </div>';
                        } else {
                            $status = '<div class="text-center">
                                        <a href="' . route('delivery-mode.status', $deliveryModes->id) . '" title="Status" class="btn btn-sm btn-secondary">
                                            <i class="fas fa-arrow-down"></i>
                                        </a>
                                    </div>';
                        }
                        return $status;
                    })

                    ->addColumn('action', function ($deliveryModes) {
                        if (Auth::user()->can('edit') && Auth::user()->can('delete')) {
                            return '<div class="table-actions text-center">
                                            <a href="' . route('delivery-mode.edit', $deliveryModes->id) . '" title="Edit"><i class="ik ik-edit-2 f-16 mr-15 text-green"></i></a>
                                            <a type="submit" onclick="showDeleteConfirm(' . $deliveryModes->id . ')" title="Delete"><i class="ik ik-trash-2 f-16 text-red"></i></a>
                                            </div>';
                        } elseif (Auth::user()->can('edit')) {
                            return '<div class="table-actions">
                                            <a href="' . route('delivery-mode.edit', $deliveryModes->id) . '" title="Edit"><i class="ik ik-edit-2 f-16 mr-15 text-green"></i></a>
                                            </div>';
                        } elseif (Auth::user()->can('delete')) {
                            return '<div class="table-actions">
                                            <a type="submit" onclick="showDeleteConfirm(' . $deliveryModes->id . ')" title="Delete"><i class="ik ik-trash-2 f-16 text-red"></i></a>
                                            </div>';
                        }
                    })
                    ->addIndexColumn()
                    ->rawColumns(['status', 'action'])
                    ->make(true);
            }
            return view('ticket.delivery_mode.index', compact('deliveryModes'));
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
        return view('ticket.delivery_mode.create');
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
            'name' => 'required|string|unique:delivery_modes,name,NULL,id,deleted_at,NULL',
            'status' => 'required'
        ]);

        DB::beginTransaction();

        try{
            DeliveryMode::create([
                'name' => $request->name,
                'status' => $request->status
            ]);
            DB::commit();
            return redirect('tickets/delivery-mode')
            ->with('success', __('label.NEW_PRODUCT_DELIVERY_MODE_CREATED_SUCCESSFULLY'));
        }catch(\Exception $e){
            dd($e);
            DB::rollback();
            return redirect()->back()->with('error','Something Went Wrong!');
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
            $deliveryMode=DeliveryMode::find($id);
            return view('ticket.delivery_mode.edit', compact('deliveryMode'));
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
        // dd($request->all());
        $this->validate($request, [
            'name' => 'required|string|unique:delivery_modes,name,'. $id,
            'status' => 'required'
        ]);

        DB::beginTransaction();

        try{
            $delivery=DeliveryMode::find($id);
            $delivery->update([
                'name' => $request->name,
                'status' => $request->status
            ]);

            DB::commit();
            return redirect('tickets/delivery-mode')
            ->with('success', __('label.PRODUCT DELIVERY MODE UPDATED SUCCESSFULLY'));
        }catch(\Exception $e){
            DB::rollback();
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
            $deliveryMode=DeliveryMode::findOrFail($id);
            $Ticket = DB::table('tickets')
            ->where('deleted_at', NULL)
            ->where('expected_delivery_mode_id', 'LIKE','%'.$deliveryMode->id.'%')
            ->get();
            if(count($Ticket) > 0){
                return response()->json([
                    'success' => false,
                    'message' => "Sorry! Can't Delete. This Delivery Mode is used in Ticket Management",
                ]);
            }else{
                $deliveryMode->delete();
                return response()->json([
                    'success' => true,
                    'message' => "DeliveryMode deleted successfully",
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
            $deliveryMode = DeliveryMode::findOrFail($id);

            if($deliveryMode->status == false) {
                $deliveryMode->update([
                    'status' => true,
                ]);

                return back()->with('success', __('Delivery mode active now'));
            }elseif ($deliveryMode->status == true) {
                $deliveryMode->update([
                    'status' => false,
                ]);
                return back()->with('success', __('Delivery mode inactive now'));
            }

            return back()->with('error', __('Action decline'));
        }catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}
