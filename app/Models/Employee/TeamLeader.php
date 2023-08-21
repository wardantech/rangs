<?php

namespace App\Models\Employee;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Auth;
use App\Models\User;
use App\Models\Group\Group;

class TeamLeader extends Model
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
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id'); 
    }
    public function group()
    {
        return $this->belongsTo(Group::class, 'group_id'); 
    }
    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id'); 
    }
}
