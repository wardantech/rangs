<?php

namespace App\Models\Job;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Inventory\Parts;
use App\Models\JobModel\Job;

class JobSubmission extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $guarded = [];
    protected $dates = ['deleted_at','submission_date'];
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
    public function part()
    {
        return $this->belongsTo(Parts::class, 'part_id'); 
    }
    public function jobSubmissions()
    {
        return $this->hasMany(JobSubmissionDetails::class, 'job_submission_id');
    }
}
