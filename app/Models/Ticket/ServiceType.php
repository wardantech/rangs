<?php

namespace App\Models\Ticket;

use Auth;
use App\Models\Inventory\Category;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ServiceType extends Model
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
    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }
    // public function service()
    // {
    //     return $this->hasMany(ServiceType::class, 'service_type_id');   
    // }
}
