<?php

namespace App\Models\Loan;

use Auth;
use App\Models\User;
use App\Models\Outlet\Outlet;
use App\Models\Inventory\Parts;
use App\Models\Inventory\Store;
use App\Models\Inventory\PartsModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AcceptLoanRequest extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'date',	'belong_to', 'outlate_id', 'store_id', 'to_store_id', 'loan_id', 'issue_quantity', 'total_received_quantity', 'receive_id', 'status'
    ];
    // protected $guarded = [];
    protected $dates = ['deleted_at', 'date'];

    public function outlate()
    {
        return $this->belongsTo(Outlet::class, 'outlate_id');
    }
    
    public function store()
    {
        return $this->belongsTo(Store::class, 'store_id');
    }

    public function toStore()
    {
        return $this->belongsTo(Store::class, 'to_store_id');
    }
    public function loan()
    {
        return $this->belongsTo(Loan::class, 'loan_id');
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
        });

        static::updating(function($post)
        {
            $post->updated_by = Auth::user()->id;
        });

    }
}
