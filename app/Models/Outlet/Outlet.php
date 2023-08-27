<?php

namespace App\Models\Outlet;

use App\Models\Account\CashTransection;
use App\Models\Inventory\District;
use Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Outlet extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $guarded = [];
    protected $dates = ['deleted_at'];


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

    public function transactions()
    {
        return $this->hasMany(CashTransection::class, 'outlet_id');
    }

    public function district()
    {
        return $this->belongsTo(District::class);
    }
}
