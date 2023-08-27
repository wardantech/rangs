<?php

namespace App\Models\Consumption;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\User;
use App\Models\JobModel\Job;
use App\Models\Inventory\Parts;

class PartWithdrawDetails extends Model
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
    public function part()
    {
        return $this->belongsTo(Parts::class, 'part_id');  
    }
    public function job()
    {
        return $this->belongsTo(Job::class, 'job_id');  
    }
}