<?php

namespace App\Models\Inventory;

use App\Models\User;
use App\Models\Inventory\Bin;
use App\Models\Inventory\Rack;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ReceivedPartsDetails extends Model
{
    use HasFactory, SoftDeletes;
    protected $guarded = [];

    public function part()
    {
        return $this->belongsTo(Parts::class, 'part_id');
    }

    public function outlet()
    {
        return $this->belongsTo(Outlet::class, 'outlet_id');
    }

    public function toStore()
    {
        return $this->belongsTo(Store::class, 'to_store_id');
    }

    public function senderStore()
    {
        return $this->belongsTo(Store::class, 'from_store_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
    public function bin()
    {
        return $this->belongsTo(Bin::class, 'bin_id');
    }
    public function rack()
    {
        return $this->belongsTo(Rack::class, 'rack_id');
    }
}
