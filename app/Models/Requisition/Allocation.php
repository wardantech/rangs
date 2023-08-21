<?php

namespace App\Models\Requisition;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Outlet\Outlet;
use App\Models\Inventory\Store;
use App\Models\Inventory\Parts;
use App\Models\Inventory\PartsModel;
use Auth;
use App\Models\User;

class Allocation extends Model
{
    use HasFactory;
    use SoftDeletes;
    // protected $guarded = [];
    protected $fillable = [
        'date',	'belong_to', 'outlate_id', 'store_id', 'requisition_id', 'allocate_quantity','status','requisition_no','is_reallocated', 'to_store_id','received_quantity','allocation_status','is_received', 'created_by', 'updated_by', 'allocation_date'
    ];
    protected $dates = ['deleted_at','date','allocation_date'];

    public function outlate()
    {
        return $this->belongsTo(Outlet::class, 'outlate_id');
    }

    public function store()
    {
        return $this->belongsTo(Store::class, 'store_id');
    }

    public function senderStore()
    {
        return $this->belongsTo(Store::class, 'to_store_id');
    }
    public function requisition()
    {
        return $this->belongsTo(Requisition::class, 'requisition_id');
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    public function allocationDetails()
    {
        return $this->hasMany(AllocationDetails::class, 'allocation_id');
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
