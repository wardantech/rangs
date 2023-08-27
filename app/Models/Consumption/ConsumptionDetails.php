<?php

namespace App\Models\Consumption;

use App\Models\User;
use App\Models\JobModel\Job;
use App\Models\Inventory\Parts;
use App\Models\Consumption\Consumption;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ConsumptionDetails extends Model
{
    use HasFactory, SoftDeletes;

    protected $dates = ['deleted_at','date'];

    protected $guarded = [];

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by')->withTrashed();
    }

    public function consumption()
    {
        return $this->belongsTo(Consumption::class, 'consumption_id');  
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
