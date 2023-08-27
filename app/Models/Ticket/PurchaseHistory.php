<?php

namespace App\Models\PurchaseHistory;

use Auth;
use App\Models\JobModel\Job;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PurchaseHistory extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $dates = ['deleted_at'];
    
    protected $table= 'purchase_historys';

    //protected $fillable= ["store_name", "address", "store_code"];

    public $timestamps = false;

    
}
