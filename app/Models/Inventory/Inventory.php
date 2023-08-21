<?php

namespace App\Models\Inventory;

use Auth;
use App\Models\User;
use App\Models\Inventory\Parts;
use App\Models\Inventory\Source;
use Illuminate\Database\Eloquent\Model;
use App\Models\Inventory\PriceManagement;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Inventory\ProductSourcingVendor;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Inventory extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $guarded = [];
    protected $dates = ['deleted_at','order_date','sending_date','receive_date'];

    protected $table= 'inventorys';

    //protected $fillable= ["store_name", "address", "store_code"];

    public $timestamps = true;

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

    public function part()
    {
        return $this->belongsTo(Parts::class, 'part_id');
    }

    public function part_model()
    {
        return $this->belongsTo(PartsModel::class, 'model_id');
    }
    public function store()
    {
        return $this->belongsTo(Store::class, 'store_id');
    }
    public function bin()
    {
        return $this->belongsTo(Bin::class, 'bin_id');
    }
    public function rack()
    {
        return $this->belongsTo(Rack::class, 'rack_id');
    }
    public function productVendor()
    {
        return $this->belongsTo(ProductSourcingVendor::class, 'vendor_id');
    }

    public function source()
    {
        return $this->belongsTo(Source::class, 'source_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    // public function source()
    // {
    //     return $this->belongsTo(Source::class, 'source_id');
    // }



}
