<?php

namespace App\Http\Requests\Vouchers;

use Illuminate\Foundation\Http\FormRequest;

class GetFilteredVouchersRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'issuer_name' => 'nullable|string|max:255',
            'receiver_name' => 'nullable|string|max:255',
            'serie' => 'nullable|string|max:20',
            'moneda' => 'nullable|string|max:3',
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date|after_or_equal:date_from',
            'page' => 'nullable|integer|min:1',
            'paginate' => 'nullable|integer|min:1|max:100',
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
