<?php

declare(strict_types=1);

namespace App\Http\Requests\Sale;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ListSaleRequest extends FormRequest
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
            'client_id' => ['nullable', 'integer', Rule::exists('clients', 'id')->where('user_id', auth()->id())],
            'vehicle_id' => ['nullable', 'integer', Rule::exists('vehicles', 'id')->where('user_id', auth()->id())],
            'payment_method' => ['nullable', 'string', Rule::in(['cash', 'transfer', 'card'])],
            'should_invoice' => ['nullable', 'boolean'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
            'page' => ['nullable', 'integer', 'min:1'],
        ];
    }
}
