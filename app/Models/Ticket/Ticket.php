<?php

namespace App\Models\Ticket;

use Auth;
use App\Models\User;
use App\Models\JobModel\Job;
use App\Models\Outlet\Outlet;
use App\Models\Inventory\Brand;
use App\Models\Inventory\Fault;
use App\Models\Inventory\Thana;
use App\Models\Customer\Customer;
use App\Models\Inventory\Category;
use App\Models\Inventory\District;
use App\Models\Product\BrandModel;
use App\Models\Ticket\ReceiveMode;
use App\Models\Ticket\ServiceType;
use App\Models\Ticket\DeliveryMode;
use App\Models\Ticket\WarrantyType;
use App\Models\Ticket\PaymentType;
use App\Models\Ticket\TicketRecommendation;
use Illuminate\Database\Eloquent\Model;
use App\Models\ProductPurchase\Purchase;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Job\JobAttachment;

class Ticket extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $guarded = [];
    protected $dates = ['deleted_at','date','created_at','start_date','end_date','delivery_date_by_call_center','reopen_date'];

    protected $cast = [
        'fault_description_id' => 'array',
        'product_condition_id' => 'array'
    ];

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
    
    public function district()
    {
        return $this->belongsTo(District::class, 'district_id');
    }

    public function thana()
    {
        return $this->belongsTo(Thana::class, 'thana_id');
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
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

    public function service_type()
    {
        return $this->belongsTo(ServiceType::class, 'service_type_id');
    }

    public function purchase()
    {
        return $this->belongsTo(Purchase::class, 'purchase_id');
    }

    public function faults(){
        return $this->belongsToMany(Fault::class,'fault_description_id');
    }

    public function jobPriority()
    {
        return $this->belongsTo(JobPriority::class, 'job_priority_id');
    }

    public function receive_mode()
    {
        return $this->belongsTo(ReceiveMode::class, 'product_receive_mode_id');
    }

    public function deivery_mode()
    {
        return $this->belongsTo(DeliveryMode::class, 'expected_delivery_mode_id');
    }

    public function service()
    {
        return $this->belongsTo(ServiceType::class, 'service_type_id');
    }

    public function job(){
        return $this->hasOne(Job::class, 'ticket_id');
    }

    public function jobs(){
        return $this->hasMany(Job::class, 'ticket_id');
    }

    public function lastJob()
    {
        return $this->hasOne(Job::class)->latest();
    }

    public function warrantytype()
    {
        return $this->belongsTo(WarrantyType::class, 'warranty_type_id');
    }

    public function ticketAttachments()
    {
        return $this->hasMany(JobAttachment::class, 'ticket_id');
    }

    // public function recommend()
    // {
    //     return $this->hasOne(TicketRecommendation::class)
    //         ->where('outlet_id', $this->outlet_id)
    //         ->where('teamleader_id', $this->teamleader_id);
    // }

    public function recommendations()
    {
        return $this->hasMany(TicketRecommendation::class)->where('type', 1);
    }

    public function transfers()
    {
        return $this->hasMany(TicketTransfer::class);
    }

    public function lastRecommendation()
    {
        return $this->hasMany(TicketRecommendation::class, 'ticket_id')->latest()->first();
    }

    public function recommendationByCc()
    {
        return $this->hasMany(TicketRecommendation::class, 'ticket_id')->where('type', 2);
    }
}
