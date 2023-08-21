<?php

namespace App\Models\Inventory;

use Auth;
use App\User;
use App\Models\JobModel\Job;
use App\Models\Inventory\Bin;
use App\Models\Inventory\Rack;
use App\Models\Inventory\Parts;
use App\Models\Inventory\Store;
use App\Models\Inventory\Source;
use App\Models\Employee\Employee;
use App\Models\Inventory\PartsModel;
use App\Models\Requisition\Allocation;
use Illuminate\Database\Eloquent\Model;
use Maatwebsite\Excel\Concerns\ToModel;
use App\Models\Inventory\PriceManagement;
use Illuminate\Database\Eloquent\SoftDeletes;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use App\Models\Inventory\ProductSourcingVendor;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class InventoryStock extends Model implements ToModel, WithHeadingRow
{
    use HasFactory;
        use SoftDeletes;
        /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    // protected $table = 'inventory_stocks';
    protected $guarded = [];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    // protected $dates = ['deleted_at'];

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
    public function job()
    {
        return $this->belongsTo(Job::class, 'job_id');
    }
    public function part_model()
    {
        return $this->belongsTo(PartsModel::class, 'parts_model_id');
    }
    public function store()
    {
        return $this->belongsTo(Store::class, 'store_id');
    }
    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }
    public function bin()
    {
        return $this->belongsTo(Bin::class, 'bin_id')->withTrashed();
    }
    public function rack()
    {
        return $this->belongsTo(Rack::class, 'rack_id')->withTrashed();
    }
    public function price(){
        return $this->belongsTo(PriceManagement::class, 'price_management_id');
    }

    public function inventory(){
        return $this->belongsTo(Inventory::class, 'receive_id');
    }
     public function allocation(){
        return $this->belongsTo(Allocation::class,'allocation_id');
     }
    public function model(array $row)
    {
        
        $part = Parts::where('code', $row['code'])->firstOrFail();
        $store = Store::where('name', $row['store'])->first();

        return new InventoryStock([
            'belong_to' =>  1, //1=Central WareHouse
            'vendor_id' => null,
            'price_management_id' => null,
            'cost_price_usd' => $row['pur_price_usd'],
            'cost_price_bdt' => $row['pur_price_bdt'],
            'selling_price_bdt' => $row['selling_price'],
            'store_id' =>  $store->id ?? null,
            'part_id' =>$part->id ?? null,
            'bin_id' => null,
            'rack_id' => null,
            'stock_in' => $row['present_stock'] ?? 0,
            'created_by' => Auth::id(),
        ]);
    }
}
