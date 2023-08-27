<?php

namespace App\Http\Controllers\Ticket;

use DB;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Ticket\ProductCondition;

class ProductConditionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try{
            $product_conditions = ProductCondition::latest()->get();
            return view('ticket.product_condition.index',compact('product_conditions'));
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
        //
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
            'product_condition' => 'required|string|unique:product_conditions,product_condition,NULL,id,deleted_at,NULL',
            'status' => 'required'
        ]);

        try {
            $data = $request->all();
            ProductCondition::create($data);
            return back()->with('success', __('label.NEW_PRODUCT_CONDITION_TYPE_CREATED'));
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
        $condition = ProductCondition::findOrFail($id);
        return $condition;
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
            'product_condition' => 'required|string|unique:product_conditions,product_condition' . $request->id,
            'status' => 'required'
        ]);

        $data = $request->all();
        $condition = ProductCondition::find($data['condition_id']);
        $condition->product_condition = $data['product_condition'];
        $condition->status = $data['status'];

        try {
            $condition->save();
            return back()->with('success', __('label.NEW_PRODUCT_CONDITION_UPDATED'));
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
            $productCondition=ProductCondition::findOrFail($id);
            $Ticket = DB::table('tickets')
            ->where('deleted_at', NULL)
            ->where('product_condition_id', 'LIKE','%'.$productCondition->id.'%')
            ->get();
            if(count($Ticket) > 0){
                return back()->with('error', "Sorry! Can't Delete. This Product Condition is used in Ticket Management");
            }else{
                $productCondition->delete();
                return redirect()->back()->with('success', 'Product Condition deleted successfully');
            }
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
            $productCondition = ProductCondition::findOrFail($id);

            if($request->status == false) {
                $productCondition->update([
                    'status' => $request->status
                ]);

                return back()->with('success', __('Product condition inactive now'));
            }elseif ($request->status == true) {
                $productCondition->update([
                    'status' => $request->status
                ]);

                return back()->with('success', __('Product condition active now'));
            }

            return back()->with('error', __('Action decline'));
        }catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}
