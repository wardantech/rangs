<?php

namespace App\Exports;

use Carbon\Carbon;
use App\Models\Inventory\Parts;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class SingleStockExport implements FromView, ShouldAutoSize
{
    /**
    * @return \Illuminate\Support\Collection
    */
    protected $id;

	public function __construct($id) {
	    $this->id = $id;
	}

    public function view(): View
    {
        $raws = Parts::with('inventoryStock')->whereHas('inventoryStock', function($q) use ($id){
            $q->where('store_id', $id)
            ->whereRaw('stock_in - stock_out > 0');
        })
        ->where('status', 1)->orderBy('name');
        $date = Carbon::now()->format('m/d/Y');
        return view('inventory.stock.index.excel',compact('raws','date'));
    }
}
