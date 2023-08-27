<?php

namespace App\Models\Inventory;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Inventory\Parts;
use App\Models\Inventory\PartsModel;
use App\Models\Employee\Employee;
use App\Models\User;
use App\Models\Outlet\Outlet;

class PartsReturn extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $guarded = [];
    protected $dates = ['deleted_at','date'];

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function part()
    {
        return $this->belongsTo(Parts::class, 'part_id'); 
    }

    public function toStore()
    {
        return $this->belongsTo(Store::class, 'to_store_id');
    }

    public function senderStore()
    {
        return $this->belongsTo(Store::class, 'from_store_id');
    }
    
    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id'); 
    }
}
