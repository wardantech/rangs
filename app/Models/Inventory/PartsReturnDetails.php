<?php

namespace App\Models\Inventory;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PartsReturnDetails extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $guarded = [];

    
    public function bin()
    {
        return $this->belongsTo(Bin::class, 'bin_id');
    }
    public function rack()
    {
        return $this->belongsTo(Rack::class, 'rack_id');
    }
    public function part()
    {
        return $this->belongsTo(Parts::class, 'parts_id'); 
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

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
