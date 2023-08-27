<?php

namespace App\Http\Controllers\Ticket;

use DB;
use Illuminate\Http\Request;
use App\Models\Inventory\Category;
use App\Models\Ticket\Accessories;
use App\Http\Controllers\Controller;

class AccessoriesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try{
            $categories = Category::where('status', 1)->orderBy('name')->pluck('name','id')->toArray();
            $accessories = DB::table('accessories')
                        ->where('deleted_at', NULL)
                        ->join('categories','categories.id','=','accessories.product_id')
                        ->select('accessories.*','categories.name')
                        ->orderBY('id','DESC')->get();
            return view('ticket.accessories.index',compact('categories','accessories'));
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
            'accessories_name' => 'string | required | max:100',
            'product_id' => 'required | integer',
            'status' => 'required'
        ]);

        try {
            $data = $request->all();
            $accessories=Accessories::where('accessories_name',$request->accessories_name)->where('product_id',$request->product_id)->get();
            if(count($accessories) > 0){
                return back()->with('error', "Sorry! Can't Create. The Accessory Name Is Available For The Selected Product");
            }else{
                Accessories::create($data);
                return back()->with('success', __('label.NEW_ACCESSORIES_CREATED'));
            }

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
        $accessory = Accessories::findOrFail($id);
        return $accessory;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $this->validate($request, [
            'accessories_name' => 'string | required | max:100',
            'product_id' => 'required | integer',
            'status' => 'required'
        ]);

        $data = $request->all();
        try {
            $accessory = Accessories::find($data['accessory_id']);
            $accessories=Accessories::where('accessories_name',$request->accessories_name)->where('product_id',$request->product_id)->get();
            if(count($accessories) > 0){
                return back()->with('error', "Sorry! Can't Create. The Accessory Name Is Available For The Selected Product");
            }else{
                $accessory->accessories_name = $data['accessories_name'];
                $accessory->product_id = $data['product_id'];
                $accessory->status = $data['status'];

                $accessory->save();
                return back()->with('success','Accessory Updated Successfully');
            }
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
            $accessories=Accessories::findOrFail($id);
            $Ticket = DB::table('tickets')
                ->where('deleted_at', NULL)
                ->where('accessories_list_id', 'LIKE','%'.$accessories->id.'%')
                ->get();
            if(count($Ticket) > 0){
                return back()->with('error', "Sorry! Can't Delete. This Accessory is used in Ticket  Management");
            }else{
                $accessories->delete();
                return redirect()->back()->with('success', 'Accessory deleted successfully');
            }
            // return back()->with('success', __('label.ACCESSORIES_DELETED_SUCCESSFULLY'));
        } catch (\Exception $e) {
            $bug = $e->getMessage();
            return redirect()->back()->with('error', $bug);
        }
    }

    public function aciveInactive(Request $request, $id)
    {
        $this->validate($request, [
            'status' => 'required|numeric|boolean'
        ]);

        try {
            $accessories = Accessories::findOrFail($id);

            if($request->status == false) {
                $accessories->update([
                    'status' => $request->status
                ]);

                return back()->with('success', __('Accessories inactive now'));
            }elseif ($request->status == true) {
                $accessories->update([
                    'status' => $request->status
                ]);

                return back()->with('success', __('Accessories active now'));
            }

            return back()->with('error', __('Action decline'));
        }catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}
