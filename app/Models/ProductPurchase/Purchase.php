<?php

namespace App\Models\ProductPurchase;

use App\Models\User;
use App\Models\Outlet\Outlet;
use App\Models\Ticket\Ticket;
use App\Models\Inventory\Brand;
use App\Models\Inventory\Fault;
use App\Models\Customer\Customer;
use App\Models\Inventory\Category;
use App\Models\Product\BrandModel;
use Illuminate\Database\Eloquent\Model;
use Maatwebsite\Excel\Concerns\ToModel;
use Illuminate\Database\Eloquent\SoftDeletes;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithMappedCells;
use Maatwebsite\Excel\Imports\HeadingRowFormatter;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Purchase extends Model implements ToModel, WithHeadingRow
{
    use HasFactory;
    use SoftDeletes;
    protected $guarded = [];
    protected $dates = ['deleted_at','date','general_warranty_date','special_warranty_date','service_warranty_date', 'purchase_date'];

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id')->withTrashed();
    }
    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
    public function brand()
    {
        return $this->belongsTo(Brand::class, 'brand_id');
    }
    public function modelname()
    {
        return $this->belongsTo(BrandModel::class, 'brand_model_id');
    }
    public function category()
    {
        return $this->belongsTo(Category::class, 'product_category_id');
    }
    public function outlet()
    {
        return $this->belongsTo(Outlet::class, 'outlet_id')->withTrashed();
    }
    public function faults(){
        return $this->belongsToMany(Fault::class,'fault_description_id');
    }

    public function ticket()
    {
        return $this->hasMany(Ticket::class, 'purchase_id');
    }

    public function transformDate($value, $format = 'Y-m-d')
{
    try {
        return \Carbon\Carbon::instance(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value));
    } catch (\ErrorException $e) {
        return \Carbon\Carbon::createFromFormat($format, $value);
    }
}

    public function model(array $row)
    {
        // HeadingRowFormatter::default('none');
        return new Purchase([
            'purchase_date' => $this->transformDate($row['purchase_date']),
            'product_serial' => $row['product_serial'],
            'customer_id' => Customer::where('name', $row['customer'])->firstOrFail()->id, //->select('id')->first(), //$row['part_model_id'],
            'product_category_id' => Category::where('name', $row['product_category'])->firstOrFail()->id,
            'brand_id' => Brand::where('name', $row['brand'])->firstOrFail()->id,
            'brand_model_id' => BrandModel::where('model_name', $row['brand_model'])->firstOrFail()->id,
            'outlet_id' => Outlet::where('name', $row['branch'])->firstOrFail()->id,
            'general_warranty_date' => $this->transformDate($row['general_warranty_date']),
            'special_warranty_date' => $this->transformDate($row['special_warranty_date']),
            'service_warranty_date' => $this->transformDate($row['service_warranty_date']),
        ]);
    }
}
