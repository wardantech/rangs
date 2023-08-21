<?php

namespace App\Models\Inventory;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Rack extends Model
{
    use HasFactory, SoftDeletes;

    protected $table= 'racks';
    protected $guarded = [];

    public function store()
    {
        return $this->belongsTo(Store::class, 'store_id')->withTrashed();
    }
}
