<?php

namespace App\Models\Requisition;

use App\Models\Inventory\Bin;
use App\Models\Inventory\Rack;
use App\Models\Inventory\Parts;
use App\Models\Inventory\Category;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Requisition\BranchAllocationReceived;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class BranchAllocationReceivedDetali extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'branch_allocation_received_detalis';

    protected $fillable = [
        'branch_allocation_received_id', 'part_id', 'part_category_id', 'allocation_details_id',
        'rack_id', 'bin_id', 'stock_in_hand', 'issued_quantity', 'requisition_quantity',
        'receiving_quantity', 'created_by', 'updated_by'
    ];

    public function part()
    {
        return $this->belongsTo(Parts::class, 'part_id');
    }

    public function partCategory()
    {
        return $this->belongsTo(Category::class, 'part_category_id');
    }

    public function allocationDetail()
    {
        return $this->belongsTo(AllocationDetails::class, 'allocation_details_id');
    }

    public function rack()
    {
        return $this->belongsTo(Rack::class, 'rack_id')->withTrashed();
    }

    public function bin()
    {
        return $this->belongsTo(Bin::class, 'bin_id')->withTrashed();
    }

    public function branchAllocationReceived()
    {
        return $this->belongsTo(BranchAllocationReceived::class, 'branch_allocation_received_id');
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
