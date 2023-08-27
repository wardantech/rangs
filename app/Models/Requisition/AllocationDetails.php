<?php

namespace App\Models\Requisition;

use Auth;
use App\Models\User;
use App\Models\Inventory\Bin;
use App\Models\Inventory\Rack;
use App\Models\Inventory\Parts;
use App\Models\Inventory\Store;
use App\Models\Inventory\PartsModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AllocationDetails extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $guarded = [];
    // protected $fillable = [
    //     'allocation_id', 'rack_id', 'bin_id', 'parts_id',	'model_id',	'requisition_quantity',	'issued_quantity','received_quantity'
    // ];
    protected $dates = ['deleted_at','date'];

    public function part()
    {
        return $this->belongsTo(Parts::class, 'parts_id');
    }

    public function part_model()
    {
        return $this->belongsTo(PartsModel::class, 'model_id');
    }

    public function allocation()
    {
        return $this->belongsTo(Allocation::class, 'allocation_id');
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
        return $this->belongsTo(Bin::class, 'bin_id')->withTrashed();
    }
    public function rack()
    {
        return $this->belongsTo(Rack::class, 'rack_id')->withTrashed();
    }
}
