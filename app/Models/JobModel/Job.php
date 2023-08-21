<?php

namespace App\Models\JobModel;

use App\Models\User;
use App\Models\Job\JobNote;
use App\Models\Outlet\Outlet;
use App\Models\Ticket\Ticket;
use App\Models\Inventory\Brand;
use App\Models\Inventory\Fault;
use App\Models\Customer\Customer;
use App\Models\Employee\Employee;
use App\Models\Inventory\Category;
use App\Models\Job\JobPendingNote;
use App\Models\Product\BrandModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Job extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $guarded = [];
    protected $dates = ['deleted_at','date','job_start_time','job_end_time'];

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    public function ticket(){
        return $this->belongsTo(Ticket::class, 'ticket_id');
    }
    public function rejectNote(){
        return $this->hasOne(JobNote::class, 'job_id');
    }
    
    public function pendingNotes(){
    return $this->hasMany(JobPendingNote::class, 'job_id');
    }

}
