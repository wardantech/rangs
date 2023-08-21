<?php

namespace App\Models\Inventory;

use App\User;
use App\Models\Inventory\Bin;
use App\Models\Inventory\Rack;
use App\Models\Inventory\Parts;
use App\Models\Inventory\Store;
use App\Models\Inventory\PartCategory;
use Illuminate\Database\Eloquent\Model;
use Maatwebsite\Excel\Concerns\ToModel;
use Illuminate\Database\Eloquent\SoftDeletes;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PartsModel extends Model implements ToModel, WithHeadingRow
{
    use HasFactory;
    use SoftDeletes;
    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];

    public function model(array $row)
    {
        $cat = PartCategory::where('name', $row['part_category'])->firstOrFail();
        return new PartsModel([
           'part_category_id' => $cat->id ?? null,
           'name' => $row['name'] ?? null,
           'status' => $row['status']=='Active' ? 1 : 0,
        ]);

    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by')->withTrashed();
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by')->withTrashed();
    }
    
    public function part()
    {
        return $this->belongsTo(Parts::class, 'part_id'); 
    }
    public function category()
    {
        return $this->belongsTo(PartCategory::class, 'part_category_id'); 
    }
}
