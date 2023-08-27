<?php

namespace App\Models\PO;

use Auth;
use App\Models\Inventory\Parts;
use App\Models\PO\PurchaseOrder;
use App\Models\Inventory\PartsModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PurchaseOrderDetails extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $guarded = [];
    protected $dates = ['deleted_at','date'];

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
    public function po()
    {
        return $this->belongsTo(PurchaseOrder::class, 'purchase_order_id');
    }

    public function part()
    {
        return $this->belongsTo(Parts::class, 'part_id');
    }

    public function partsModel()
    {
        return $this->belongsTo(PartsModel::class, 'parts_model_id');
    }
}
