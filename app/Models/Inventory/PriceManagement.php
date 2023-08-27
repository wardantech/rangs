<?php

namespace App\Models\Inventory;

use Auth;
use App\Models\Inventory\Parts;
use Illuminate\Database\Eloquent\Model;
use Maatwebsite\Excel\Concerns\ToModel;
use Illuminate\Database\Eloquent\SoftDeletes;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PriceManagement extends Model implements ToModel, WithHeadingRow
{
    use HasFactory;
    use SoftDeletes;
    protected $guarded = [];
    protected $dates = ['deleted_at'];
    
    public function model(array $row)
    {
        $parts = Parts::where('code', $row['code'])->firstOrFail();

        return new PriceManagement([
           'part_id' => $parts->id ?? null,
           'cost_price_usd' => $row['pur_price_usd'] ?? null,
           'cost_price_bdt' => $row['pur_price_bdt'] ?? null,
           'selling_price_bdt' => $row['selling_price'] ?? null,
        ]);
    }

    public function part(){
        return $this->belongsTo(Parts::class, 'part_id');
    }
   
}
