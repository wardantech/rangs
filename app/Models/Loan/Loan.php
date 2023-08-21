<?php

namespace App\Models\Loan;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Inventory\Parts;
use App\Models\Inventory\PartsModel;
use App\Models\Outlet\Outlet;
use App\Models\Inventory\Store;
use App\Models\Employee\Employee;
use App\Models\User;
use Auth;

class Loan extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $guarded = [];
    // protected $fillable = [
    //     'store_id','requisition_no','date', 'total_quantity', 'belong_to', 'status','allocation_status'
    // ];
    protected $dates = ['deleted_at', 'date'];

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
