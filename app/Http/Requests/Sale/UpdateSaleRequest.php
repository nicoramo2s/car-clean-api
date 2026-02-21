<?php

declare(strict_types=1);

namespace App\Http\Requests\Sale;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateSaleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, array<int, \Illuminate\Contracts\Validation\ValidationRule|string>|string>
     */
    public function rules(): array
    {
        return [
            'client_id' => [
                'required',
                'integer',
                Rule::exists('clients', 'id')->where('user_id', auth()->id()),
            ],
            'vehicle_id' => [
                'required',
                'integer',
                Rule::exists('vehicles', 'id')->where('user_id', auth()->id()),
            ],
            'payment_method' => ['required', 'string', Rule::in(['cash', 'transfer', 'card'])],
            'paid_at' => ['nullable', 'date'],
            'should_invoice' => ['required', 'boolean'],
            'point_of_sale' => ['nullable', 'integer', 'min:1', 'required_if:should_invoice,true'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.service_id' => [
                'required',
                'integer',
                Rule::exists('services', 'id')->where('user_id', auth()->id()),
            ],
        ];
    }
}
