<?php

namespace App\Models\Inventory;

use App\Models\Inventory\Rack;
use App\Models\Inventory\Store;
use Illuminate\Database\Eloquent\Model;
use Maatwebsite\Excel\Concerns\ToModel;
use Illuminate\Database\Eloquent\SoftDeletes;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Bin extends Model implements ToModel, WithHeadingRow
{
    use HasFactory, SoftDeletes;

    protected $guarded = [];

    public function store()
    {
        return $this->belongsTo(Store::class, 'store_id');
    }

    public function rack()
    {
        return $this->belongsTo(Rack::class, 'rack_id')->withTrashed();
    }

    public function model(array $row)
    {
        $store_id = Store::where('name', $row['store'])->firstOrFail();
        $rack_id = Rack::where('name', $row['rack'])->firstOrFail();
        return new Bin([
           'name' => $row['name'] ?? null,
           'store_id' => $store_id->id ?? null, 
           'rack_id' => $rack_id->id ?? null,
           'status' => $row['status']=='Active' ? 1 : 0,
        ]);
    }
}
