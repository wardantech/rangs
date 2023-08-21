<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class storeTicketRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'date' => 'required|date',
            'sl_number' => 'required|string',
            'product_category_id' => 'required|numeric',
            'purchase_id' => 'required|numeric',
            'job_priority_id' => 'required|numeric',
            'service_type_id' => 'required',
            'product_condition_id' => 'nullable',
            'customer_id' => 'required|numeric',
            'district_id' => 'required|numeric',
            'thana_id' => 'required|numeric',
            // 'fault_description_id' => 'nullable',
            // 'accessories_list_id' => 'nullable',
            'start_date' => 'required|date',
            'end_date' => 'required|date',
            'product_receive_mode_id' => 'required|numeric',
            'expected_delivery_mode_id' => 'required|numeric',
            'status' => 'numeric',
            'is_assigned' => 'numeric',
            'is_accepted' => 'numeric',
            'is_started' => 'numeric',
            'is_ended' => 'numeric',
            'is_rejected' => 'numeric',
            'is_closed_by_teamleader' => 'numeric',
            'is_reopened' => 'numeric',
            'is_closed' => 'numeric',
        ];
    }
}
