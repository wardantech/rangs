<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCashTransectionRequest extends FormRequest
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
            'date' => 'required',
            'outlet_id' => 'required',
            'amount' => 'required',
            // 'purpose' => 'required',
            // 'type' => 'nullable',
            // 'cheque_number' => 'nullable',
            // 'balance_transfer' => 'nullable',
            'remarks' => 'nullable|string'
        ];
    }
}
