<?php

namespace App\Models\Account;

use Auth;
use App\Models\JobModel\Job;
use App\Models\Outlet\Outlet;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Revenue extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $guarded = [];
    protected $dates = ['deleted_at', 'date'];

    public function getDateAttribute($date)
    {
        return Carbon::parse($date)->format('m/d/Y');
    }


    public static function boot()
    {
        parent::boot();
        static::creating(function($post)
        {
            $post->created_by = Auth::user()->id;
            $post->updated_by = Auth::user()->id;
        });

        static::updating(function($post)
        {
            $post->updated_by = Auth::user()->id;
        });

    }

    public function outlet()
    {
        return $this->belongsTo(Outlet::class, 'outlet_id');
    }

    public function job()
    {
        return $this->belongsTo(Job::class, 'job_id');
    }
}
