<?php

namespace App\Models\Inventory;

use App\Models\Inventory\Category;
use Illuminate\Database\Eloquent\Model;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Brand extends Model implements ToModel, WithHeadingRow
{
    use HasFactory;
    protected $guarded = [];

    public function category()
    {
        return $this->belongsTo(Category::class, 'product_category_id');
    }

    public function model(array $row)
    {
        return new Brand([
           'product_category_id' => Category::where('name', $row['product_category'])->firstOrFail()->id, //$row[0],
           'name' => $row['name'],
           'code' => $row['code']
        ]);
    }
}
