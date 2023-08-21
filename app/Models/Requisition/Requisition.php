<?php

namespace App\Models\Requisition;

use Auth;
use App\Models\User;
use App\Models\JobModel\Job;
use App\Models\Outlet\Outlet;
use App\Models\Inventory\Parts;
use App\Models\Inventory\Store;
use App\Models\Employee\Employee;
use App\Models\Inventory\PartsModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Requisition extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $guarded = [];
    // protected $fillable = [
    //     'store_id','requisition_no','date', 'total_quantity', 'belong_to', 'status','allocation_status'
    // ];
    protected $dates = ['deleted_at','date'];

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
        return $this->belongsTo(Store::class, 'from_store_id');
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    public function requisitionDetails()
    {
        return $this->hasMany(RequisitionDetails::class, 'requisition_id');
    }

    public function job()
    {
        return $this->belongsTo(Job::class, 'job_id');
    }

    public function parts()
    {
        return $this->belongsTo(Parts::class, 'parts_id');
    }

    public function partsModel()
    {
        return $this->belongsTo(PartsModel::class, 'parts_model_id');
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
}
