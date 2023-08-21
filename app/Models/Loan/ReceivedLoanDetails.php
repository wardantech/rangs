<?php

namespace App\Models\Loan;

use Auth;
use App\Models\User;
use App\Models\Inventory\Bin;
use App\Models\Inventory\Rack;
use App\Models\Inventory\Parts;
use App\Models\Inventory\PartsModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ReceivedLoanDetails extends Model
{
    use HasFactory, SoftDeletes;
    protected $guarded = [];
    
    public function acceptLoanDetails()
    {
        return $this->belongsTo(AcceptLoanRequestDetails::class, 'accept_loan_request_details_id');
    }
    public function part()
    {
        return $this->belongsTo(Parts::class, 'part_id');
    }

    public function part_model()
    {
        return $this->belongsTo(PartsModel::class, 'model_id');
    }

    public function parts()
    {
        return $this->belongsTo(Parts::class, 'parts_id');
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
        return $this->belongsTo(Bin::class, 'bin_id');
    }
    public function rack()
    {
        return $this->belongsTo(Rack::class, 'rack_id');
    }
}
