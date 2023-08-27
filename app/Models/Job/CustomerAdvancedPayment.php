<?php

namespace App\Models\Job;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Auth;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\JobModel\Job;
use App\Models\User;
use App\Models\Outlet\Outlet;

class CustomerAdvancedPayment extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $guarded = [];
    protected $dates = ['deleted_at'];
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
    public function job()
    {
        return $this->belongsTo(Job::class, 'job_id'); 
    }
    public function branch()
    {
        return $this->belongsTo(Outlet::class, 'branch_id'); 
    }
}
