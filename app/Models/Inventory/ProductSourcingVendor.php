<?php

namespace App\Models\Inventory;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductSourcingVendor extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable= ["name", "address", "phone", "email", "code", "grade", "district_id", "thana_id", "status"];

    public function district()
    {
        return $this->belongsTo(District::class, 'district_id');
    }

    public function thana()
    {
        return $this->belongsTo(Thana::class, 'thana_id');
    }
}
