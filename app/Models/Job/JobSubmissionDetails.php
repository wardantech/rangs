<?php

namespace App\Models\Job;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\User;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Inventory\Parts;
use App\Models\Job\JobSubmission;

class JobSubmissionDetails extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $guarded = [];
    protected $dates = ['deleted_at','date'];
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by')->withTrashed();
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by')->withTrashed();
    }
    public function part()
    {
        return $this->belongsTo(Parts::class, 'part_id'); 
    }
    public function jobSubmission()
    {
        return $this->belongsTo(JobSubmission::class, 'job_submission_id');
    }
}
