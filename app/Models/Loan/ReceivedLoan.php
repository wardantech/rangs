<?php

namespace App\Models\Loan;

use App\Models\Loan\Loan;
use App\Models\Inventory\Store;
use App\Models\Loan\AcceptLoanRequest;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ReceivedLoan extends Model
{
    use HasFactory, SoftDeletes;
    protected $guarded = [];

    public function loan()
    {
        return $this->belongsTo(Loan::class, 'loan_id');
    }
    public function acceptloan()
    {
        return $this->belongsTo(AcceptLoanRequest::class, 'accept_loan_requests_id');
    }

    public function store()
    {
        return $this->belongsTo(Store::class, 'from_store_id');
    }

    public function senderStore()
    {
        return $this->belongsTo(Store::class, 'to_store_id');
    }
}
