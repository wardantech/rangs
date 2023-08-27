<?php

namespace App\Models\Consumption;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\User;
use App\Models\JobModel\Job;
use App\Models\Consumption\PartWithdrawDetails;

class PartWithdraw extends Model
{
    use HasFactory, SoftDeletes;

    protected $dates = ['deleted_at'];

    protected $guarded = [];
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by')->withTrashed();
    }
    public function job()
    {
        return $this->belongsTo(Job::class, 'job_id');  
    }
    public function withdrawdetails()
    {
        return $this->hasmany(PartWithdrawDetails::class);
    }
}
