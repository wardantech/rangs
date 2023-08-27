<?php

namespace App\Http\Controllers\Inventory;

use DB;
use Response;
use DataTables;
use Illuminate\Http\Request;
use App\Models\Inventory\Parts;
use App\Http\Controllers\Controller;
use App\Models\Inventory\PartsModel;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\Inventory\PartCategory;
use App\Models\Inventory\InventoryStock;
use App\Models\Inventory\PriceManagement;


class PriceManagementController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try{
            $parts = Parts::where('status', 1)->orderBy('name');
            if(request()->ajax()){
                return DataTables::of($parts)
                
                ->addColumn('partcategory', function ($parts) {

                    if ($parts->partCategory !=null ) {
                        $partCategory = $parts->partCategory->name;
                    } else {
                        $partCategory ='null';
                    }
                    return $partCategory;
                    })

                ->addColumn('partmodel', function ($parts) {

                        if ($parts->partModel !=null ) {
                            $partModel = $parts->partModel->name;
                        } else {
                            $partModel ='null';
                        }
                        return $partModel;
                        })
                        ->addColumn('selling_price_bdt', function ($parts) {

                            $price=PriceManagement::where('part_id', $parts->id)->latest()->first();
                                return $price->selling_price_bdt ?? NULL;
                                })
                        ->addColumn('cost_price_usd', function ($parts) {

                            $price=PriceManagement::where('part_id', $parts->id)->latest()->first();
                                return $price->cost_price_usd ?? NULL;
                                })
                        ->addColumn('cost_price_bdt', function ($parts) {

                            $price=PriceManagement::where('part_id', $parts->id)->latest()->first();
                                return $price->cost_price_bdt ?? NULL;
                                })

                                
                                ->addColumn('action', function($parts) {
                                        if(Auth::user()->can('show')) {
                                            return '<div class="table-actions text-center">
                                            <a href="'.route('inventory.price-management-history', $parts->id).'" title="Edit"><i class="ik ik-eye f-16 mr-15 text-blue"></i></a>
                                            </div>';
                                        }
                                })
                                ->addIndexColumn()
                                ->rawColumns(['partcategory','partmodel','action'])
                                ->make(true);
            }
            return view('inventory.price_management.index');
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
        try{
            $parts = Parts::where('status', 1)->orderBy('name')->get();
            return view('inventory.price_management.create', compact('parts'));
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
            'part_id' => 'required|numeric',
            'cost_price_usd' => 'nullable|numeric',
            'cost_price_bdt' => 'required|numeric',
            'selling_price_bdt' => 'nullable|numeric',
        ]);
        
        try{
            PriceManagement::create($request->all());

            return redirect()->route('inventory.price-management.index')->with('success', __('Price Created Successfully.'));
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
            $priceManagementRow=PriceManagement::findOrFail($id);
            return view('inventory.price_management.edit', compact('priceManagementRow'));
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
            'part_id' => 'required|numeric',
            'cost_price_usd' => 'nullable|numeric',
            'cost_price_bdt' => 'required|numeric',
            'selling_price_bdt' => 'nullable|numeric',
        ]);

        try{
 
 
            $priceManagementRow=PriceManagement::findOrFail($id);
            $priceManagementRow->update([
                'part_id'=>$request->part_id,
                'cost_price_usd'=>$request->cost_price_usd,
                'cost_price_bdt'=>$request->cost_price_bdt,
                'selling_price_bdt'=>$request->selling_price_bdt,
                'updated_by'=>Auth::user()->id,
            ]);
            return redirect()->route('inventory.price-management.index')->with('success', __('Price Updated Successfully.'));
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
            $priceManagement=PriceManagement::findOrFail($id);
            $inventoryStock=InventoryStock::where('price_management_id',$priceManagement->id)->get();
            if(count($inventoryStock) > 0){
                return back()->with('error', "Sorry! Can't Delete. This Price Management is used in InventoryStock Management");
            }else{
                $priceManagement->delete();
                return redirect()->route('inventory.price-management.index')->with('success', __('Price Deleted Successfully.'));
            }
        } catch (\Exception $e) {
            $bug = $e->getMessage();
            return redirect()->back()->with('error', $bug);
        }
    }

    public function getPrice($part_id)
    {
        $getPrice=DB::table('price_management')
                    ->where('part_id', $part_id)
                    ->first();
        return response()->json($getPrice);
    }

    //Get Price History by part id
    public function history($id)
    {
        try{
            $pricemanagements=PriceManagement::where('part_id', $id)->latest()->get();
            return view('inventory.price_management.history', compact('pricemanagements'));
        } catch (\Exception $e) {
            $bug = $e->getMessage();
            return redirect()->back()->with('error', $bug);
        }
    }

    // Bulk Entry
    public function sampleExcel()
    {
        try{
        return Response::download(public_path('sample/part_price_sample.xlsx', 'part_price_sample.xlsx'));
        } catch (\Exception $e) {
            $bug = $e->getMessage();
            return redirect()->back()->with('error', $bug);
        }
    }

    public function import(Request $request)
    {
        try{
        Excel::import(new PriceManagement, $request->file('import_file'));
        return redirect()->back()->with('success','Data Uploaded Successfully');
        } catch (\Exception $e) {
            $bug = $e->getMessage();
            return redirect()->back()->with('error', $bug);
        }
    }
}
