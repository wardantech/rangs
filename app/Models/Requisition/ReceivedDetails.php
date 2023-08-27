<?php

namespace App\Models\Requisition;

use App\Models\Inventory\Parts;
use App\Models\Inventory\PartsModel;
use App\Models\Requisition\Received;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ReceivedDetails extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'received_details';

    protected $fillable = [
        'received_id', 'part_id', 'part_category_id', 'requisition_quantity', 'allocation_details', 'stock_in_hand', 'issued_quantity', 'receiving_quantity', 'created_by', 'updated_by'
    ];

    public function part()
    {
        return $this->belongsTo(Parts::class, 'part_id');
    }

    public function part_model()
    {
        return $this->belongsTo(PartsModel::class, 'model_id');
    }

    public function received()
    {
        return $this->belongsTo(Received::class, 'received_id');
    }

    public static function boot()
    {
        parent::boot();
        static::creating(function($post)
        {
            $post->created_by = Auth::id();
            $post->updated_by = Auth::id();
        });

        static::updating(function($post)
        {
            $post->updated_by = Auth::id();
        });

    }
}
