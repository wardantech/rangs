<?php

namespace App\Models\Loan;

use Auth;
use App\Models\Inventory\Bin;
use App\Models\Inventory\Rack;
use App\Models\Inventory\Parts;
use App\Models\Inventory\Store;
use App\Models\Inventory\PartsModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AcceptLoanRequestDetails extends Model
{
    use HasFactory;
    use SoftDeletes;

    // protected $fillable = [
    //     'accept_loan_request_id', 'rack_id', 'bin_id', 'parts_id',	'model_id',	'requisition_quantity', 'received_quantity', 'issued_quantity'
    // ];
    protected $guarded = [];
    protected $dates = ['deleted_at'];

    public function part()
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
