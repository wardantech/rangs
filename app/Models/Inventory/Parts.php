<?php

namespace App\Models\Inventory;

use App\Models\Inventory\PartsModel;
use App\Models\Inventory\PartCategory;
use Illuminate\Database\Eloquent\Model;
use Maatwebsite\Excel\Concerns\ToModel;
use App\Models\Inventory\ProductCategory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Parts extends Model implements ToModel, WithHeadingRow
{
    use HasFactory,SoftDeletes;

    protected $guarded = [];

    public function category()
    {
        return $this->belongsTo(Category::class, 'product_category_id');
    }

    public function partCategory(){
        return $this->belongsTo(PartCategory::class, 'part_category_id');
    }

    public function partModel(){
        return $this->belongsTo(PartsModel::class, 'part_model_id');
    }

    public function model(array $row)
    {
        $part_model_id = PartsModel::where('name', $row['part_model'])->first();
        $part_category_id = PartCategory::where('name', $row['part_category'])->first();
        $product_category_id = Category::where('name', $row['product_category'])->first();
        return new Parts([
           'name' => $row['name'] ?? null,
           'part_model_id' => $part_model_id->id ?? null, 
           'part_category_id' => $part_category_id->id ?? null,
           'product_category_id' => $product_category_id->id ?? null,
           'code' => $row['code'],
           'unit' => $row['unit'],
           'type' => $row['type']=='General' ? 1 : 2,
           'status' => $row['status']=='Active' ? 1 : 0,
        ]);
    }

    public function inventoryStock(){
        return $this->hasMany(InventoryStock::class, 'part_id');
    }
}
