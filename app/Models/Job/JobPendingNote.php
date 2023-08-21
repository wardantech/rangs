<?php

namespace App\Models\Job;

use App\Models\Job\JobCloseRemark;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class JobPendingNote extends Model
{
    use HasFactory;
    use HasFactory;
    use SoftDeletes;
    protected $guarded = [];
    
    public function jobPendingNote(){
        return $this->hasOne(JobCloseRemark::class, 'job_close_remark_id');
    }
}
