<?php

namespace App\Models\Requisition;

use App\Models\User;
use App\Models\Inventory\Store;
use App\Models\Employee\Employee;
use Illuminate\Support\Facades\Auth;
use App\Models\Requisition\Allocation;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Requisition\BranchAllocationReceivedDetali;

class BranchAllocationReceived extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'branch_allocation_receiveds';

    protected $dates = ['deleted_at','date'];

    protected $fillable = [
        'date', 'belong_to', 'store_id', 'allocation_id', 'employee_id',
        'requisition_no', 'allocate_quantity', 'is_received', 'created_by',
        'updated_by'
    ];

    public function allocation()
    {
        return $this->belongsTo(Allocation::class, 'allocation_id');
    }

    public function store()
    {
        return $this->belongsTo(Store::class, 'store_id');
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function receivedDetails()
    {
        return $this->hasMany(BranchAllocationReceivedDetali::class, 'branch_allocation_received_id');
    }

    public static function boot()
    {
        parent::boot();
        static::creating(function($post)
        {
            $post->created_by = Auth::user()->id;
            $post->updated_by = Auth::user()->id;
        });

        static::updating(function($post)
        {
            $post->updated_by = Auth::user()->id;
        });

    }
}
