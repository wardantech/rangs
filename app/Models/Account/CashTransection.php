<?php

namespace App\Models\Account;

use App\Models\Outlet\Outlet;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Carbon;

class CashTransection extends Model
{
    use HasFactory, SoftDeletes;

    protected $dates = ['deleted_at', 'date'];

    protected $fillable = [
        'date', 'belong_to', 'outlet_id', 'purpose', 'amount', 'cash_in', 'cash_out', 'cheque_number', 'remarks', 'created_by', 'updated_by', 'balance_transfer', 'type', 'expense_id', 'deposit_id', 'revenue_id'
    ];

    public function getDateAttribute($date)
    {
        return Carbon::parse($date)->format('m/d/Y');
    }

    public function outlet()
    {
        return $this->belongsTo(Outlet::class, 'outlet_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
    
    public function getPurposeAttribute()
    {
        if ($this->deposit_id) {
            return 'Deposit';
        } elseif ($this->expense_id) {
            return 'Expense';
        } elseif ($this->revenue_id) {
            return 'Revenue';
        } else {
            return 'Cash Received';
        }
    }
}
