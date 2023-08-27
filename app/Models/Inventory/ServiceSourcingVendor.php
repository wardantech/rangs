<?php

namespace App\Models\Inventory;

use App\Models\Inventory\Thana;
use App\Models\Inventory\District;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ServiceSourcingVendor extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable= [
        "name", "address", "phone", "email", "type", "code", "grade", "district_id", "thana_id", "status"
    ];

    public function district()
    {
        return $this->belongsTo(District::class, 'district_id');
    }

    public function thana()
    {
        return $this->belongsTo(Thana::class, 'thana_id');
    }
}
