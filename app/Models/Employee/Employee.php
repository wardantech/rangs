<?php

namespace App\Models\Employee;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\User;
use App\Models\CallCenter\CallCenter;
use App\Models\ServiceCenter\ServiceCenter;
use App\Models\Inventory\Store;
use App\Models\Outlet\Outlet;

class Employee extends Model
{
    use HasFactory;
    use SoftDeletes;
        /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by')->withTrashed();
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by')->withTrashed();
    }
    public function designation()
    {
        return $this->belongsTo(Designation::class, 'designation_id')->withTrashed();
    }
    public function callcenter()
    {
        return $this->belongsTo(CallCenter::class, 'call_center_id')->withTrashed();
    }
    public function servicecenter()
    {
        return $this->belongsTo(ServiceCenter::class, 'service_center_id')->withTrashed();
    }
    public function store()
    {
        return $this->belongsTo(Store::class, 'store_id');
    }
    public function outlet()
    {
        return $this->belongsTo(Outlet::class, 'outlet_id');
    }
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function teamLeader()
    {
        return $this->belongsTo(TeamLeader::class, 'team_leader_id');
    }
}
