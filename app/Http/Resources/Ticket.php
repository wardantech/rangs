<?php

namespace App\Http\Resources;
use App\Models\Ticket\ServiceType;
use Illuminate\Http\Resources\Json\JsonResource;

class Ticket extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $serviceTypes= ServiceType::where('status', 1)->get();
        $status='';
        if ($this->status == 9 && $this->is_reopened == 1) 
        {
            $status='Ticket Re-Opened';
        } 
        elseif ($this->status == 0) 
        {
            $status='Created';
        }
        elseif ($this->status == 6 && $this->is_pending==1) 
        {
            $status='Pending';
        }
        elseif ($this->status == 5 && $this->is_paused == 1) 
        {
            $status='Paused';
        }
        elseif ($this->status == 7  && $this->is_closed_by_teamleader == 1) 
        {
            $status='Forwarded to CC';
        }
        elseif ($this->status == 10 && $this->is_delivered_by_call_center == 1) 
        {
            $status='Delivered by CC';
        }
        elseif ($this->status == 8 && $this->is_delivered_by_teamleader == 1) 
        {
            $status='Delivered by TL';
        }
        elseif ($this->status == 12  && $this->is_delivered_by_call_center == 1 && $this->is_closed == 1) 
        {
            $status='Tticket is Closed';
        }
        elseif ($this->status == 12 && $this->is_delivered_by_call_center == 0 && $this->is_closed == 1) 
        {
            $status='Ticket is Undelivered Closed';
        }
        elseif ($this->status == 11 && $this->is_ended == 1) 
        {
            $status='Job Completed';
        }
        elseif ($this->status == 4 && $this->is_started == 1) 
        {
            $status='Job Started';
        }
        elseif ($this->status == 3 && $this->is_accepted == 1) 
        {
            $status='Job Accepted';
        }
        elseif ($this->status == 1 && $this->is_assigned == 1) 
        {
            $status='Assigned';
        }
        elseif ($this->status == 2 && $this->is_rejected == 1) 
        {
            $status='Rejected';
        }

        $selectedServiceTypeIds=json_decode($this->service_type_id);
        $service_type='';
        foreach ($serviceTypes as $key => $serviceType) {
           if (in_array($serviceType->id, $selectedServiceTypeIds)) {
               $service_type=$serviceType->service_type;
           }
        }
        return [
            'ticket_id' => $this->ticket_id,
            'customer_name' => $this->customer_name,
            'contact' => $this->customer_mobile,
            'place' => $this->thana.','.' '.$this->district,
            'product_category' => $this->product_category,
            'product_name' => $this->product_name,
            'product_sl' => $this->product_serial,
            'service_type' => $service_type,
            'branch' => $this->outlet_name,
            'createy_by' => $this->created_by,
            'created_at' => $this->created_at,
            'ticket_status' => $status,
        ];
    }
}
