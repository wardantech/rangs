<?php

namespace App\Models\Inventory;

use App\Models\Inventory\Bin;
use App\Models\Inventory\Rack;
use App\Models\Inventory\Parts;
use App\Models\Inventory\Store;
use Illuminate\Database\Eloquent\Model;
use Maatwebsite\Excel\Concerns\ToModel;
use Illuminate\Database\Eloquent\SoftDeletes;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RackBinManagement extends Model implements ToModel, WithHeadingRow
{
    use HasFactory, SoftDeletes;

    protected  $table = 'rack_bin_management';

    protected  $fillable = [
        'store_id', 'parts_id', 'rack_id', 'bin_id'
    ];

    protected  $hidden = [
        'created_at', 'updated_at', 'deleted_at'
    ];

    public function store()
    {
        return $this->belongsTo(Store::class, 'store_id');
    }

    public function parts()
    {
        return $this->belongsTo(Parts::class, 'parts_id');
    }

    public function rack()
    {
        return $this->belongsTo(Rack::class, 'rack_id');
    }

    public function bin()
    {
        return $this->belongsTo(Bin::class, 'bin_id');
    }
    public function model(array $row)
    {
        $store_id = Store::where('name', $row['store'])->firstOrFail();
        $parts_id = Parts::where('code', $row['code'])->firstOrFail();
        $rack_id = Rack::where('name', $row['rack'])->firstOrFail();
        $bin_id = Bin::where('name', $row['bin'])->first();
        return new RackBinManagement([
           'store_id' => $store_id->id ?? null, 
           'parts_id' => $parts_id->id ?? null, 
           'rack_id' => $rack_id->id ?? null,
           'bin_id' => $bin_id->id ?? null,
        ]);
    }
}
