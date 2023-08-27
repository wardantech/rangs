<?php

namespace App\Http\Controllers\Ticket;

use DB;
use Validator;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use App\Models\Ticket\ReceiveMode;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class ReceiveModeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try{
            $receiveModes=ReceiveMode::orderBy('id','desc');
            if (request()->ajax()) {
                return DataTables::of($receiveModes)

                    ->addColumn('status', function ($receiveModes) {
                        if ($receiveModes->status == true) {
                            $status = '<div class="text-center">
                                            <a href="' . route('receive-mode.status', $receiveModes->id) . '" title="Status" class="btn btn-sm btn-success">
                                                <i class="fas fa-arrow-up"></i>
                                            </a>
                                        </div>';
                        } else {
                            $status = '<div class="text-center">
                                        <a href="' . route('receive-mode.status', $receiveModes->id) . '" title="Status" class="btn btn-sm btn-secondary">
                                            <i class="fas fa-arrow-down"></i>
                                        </a>
                                    </div>';
                        }
                        return $status;
                    })

                    ->addColumn('action', function ($receiveModes) {
                        if (Auth::user()->can('edit') && Auth::user()->can('delete')) {
                            return '<div class="table-actions text-center">
                                            <a href="' . route('receive-mode.edit', $receiveModes->id) . '" title="Edit"><i class="ik ik-edit-2 f-16 mr-15 text-green"></i></a>
                                            <a type="submit" onclick="showDeleteConfirm(' . $receiveModes->id . ')" title="Delete"><i class="ik ik-trash-2 f-16 text-red"></i></a>
                                            </div>';
                        } elseif (Auth::user()->can('edit')) {
                            return '<div class="table-actions">
                                            <a href="' . route('receive-mode.edit', $receiveModes->id) . '" title="Edit"><i class="ik ik-edit-2 f-16 mr-15 text-green"></i></a>
                                            </div>';
                        } elseif (Auth::user()->can('delete')) {
                            return '<div class="table-actions">
                                            <a type="submit" onclick="showDeleteConfirm(' . $receiveModes->id . ')" title="Delete"><i class="ik ik-trash-2 f-16 text-red"></i></a>
                                            </div>';
                        }
                    })
                    ->addIndexColumn()
                    ->rawColumns(['status', 'action'])
                    ->make(true);
            }
            return view('ticket.receive_mode.index', compact('receiveModes'));
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
        return view('ticket.receive_mode.create');
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
            'name' => 'required|string|unique:receive_modes,name,NULL,id,deleted_at,NULL',
            'status' => 'required',
        ]);

        DB::beginTransaction();

        try{
            ReceiveMode::create([
                'name' => $request->name,
                'status' => $request->status,
            ]);
            DB::commit();
            return redirect('tickets/receive-mode')
            ->with('success', __('label.NEW PRODUCT RECEIVE MODE CREATED SUCCESSFULLY'));
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
            $receiveMode=ReceiveMode::findOrFail($id);
            return view('ticket.receive_mode.edit', compact('receiveMode'));
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
            'name' => 'required|string|unique:receive_modes,name,' . $id,
            'status' => 'required',
        ]);


        DB::beginTransaction();

        try{
            $receiveMode=ReceiveMode::findOrFail($id);
            $receiveMode->name = $request->name;
            $receiveMode->status = $request->status;
            $receiveMode->update();
            DB::commit();
            return redirect('tickets/receive-mode')
            ->with('success','Product Receive Mode Updated Successfully');
        }catch(\Exception $e){
            dd($e);
            DB::rollback();
            return redirect()->back()->with('error','Something Went Wrong!');
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
            $receiveMode=ReceiveMode::findOrFail($id);
            $Ticket = DB::table('tickets')
            ->where('deleted_at', NULL)
            ->where('product_receive_mode_id', 'LIKE','%'.$receiveMode->id.'%')
            ->get();
            if(count($Ticket) > 0){
                return response()->json([
                    'success' => false,
                    'message' => "Sorry! Can't Delete. This Receive Mode is used in Ticket Management",
                ]);
            }else{
                $receiveMode->delete();
                return response()->json([
                    'success' => true,
                    'message' => "Receive Mode deleted successfully",
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

    public function activeInactive($id)
    {
        try {
            $receiveMode = ReceiveMode::findOrFail($id);

            if($receiveMode->status == false) {
                $receiveMode->update([
                    'status' => true,
                ]);

                return back()->with('success', __('Receive mode active now'));
            }elseif ($receiveMode->status == true) {
                $receiveMode->update([
                    'status' => false,
                ]);

                return back()->with('success', __('Receive mode inactive now'));
            }

            return back()->with('error', __('Action decline'));
        }catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}
