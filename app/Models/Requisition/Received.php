<?php

namespace App\Models\Requisition;

use App\Models\User;
use App\Models\Outlet\Outlet;
use App\Models\Inventory\Store;
use App\Models\Employee\Employee;
use Illuminate\Support\Facades\Auth;
use App\Models\Requisition\Allocation;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Received extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'receiveds';

    protected $fillable = [
        'date', 'receiveing_date', 'belong_to','allocation_id', 'store_id', 'to_store_id', 'employee_id', 'outlate_id', 'requisition_no', 'allocate_quantity', 'received_quantity', 'allocation_status', 'is_received', 'status', 'is_reallocated', 'created_by','updated_by'
    ];

    protected $dates = ['deleted_at','date'];

    public function allocation()
    {
        return $this->belongsTo(Allocation::class, 'allocation_id');
    }

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
        return $this->hasMany(ReceivedDetails::class);
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
