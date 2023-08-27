<?php

namespace App\Models\Product;

use App\User;
use App\Models\Inventory\Brand;
use App\Models\Inventory\Category;
use Illuminate\Database\Eloquent\Model;
use Maatwebsite\Excel\Concerns\ToModel;
use Illuminate\Database\Eloquent\SoftDeletes;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class BrandModel extends Model implements ToModel, WithHeadingRow
{
    use HasFactory;
    use SoftDeletes;
    protected $guarded = [];
    protected $dates = ['deleted_at','date'];

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by')->withTrashed();
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by')->withTrashed();
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'product_category_id');
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class, 'brand_id');
    }

    public function model(array $row)
    {
        return new BrandModel([
           'model_name' => $row['model_name'],
           'product_category_id' => Category::where('name', $row['product_category'])->firstOrFail()->id,
           'brand_id' => Brand::where('name', $row['brand'])->firstOrFail()->id, //$row[2]
           'status' => $row['status']=='Active' ? 1 : 0,
        ]);
    }
}
