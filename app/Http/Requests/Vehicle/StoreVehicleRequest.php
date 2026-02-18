<?php

namespace App\Http\Requests\Vehicle;

use Illuminate\Foundation\Http\FormRequest;

class StoreVehicleRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'brand' => 'required|string|max:255',
            'model' => 'required|string|max:255',
            'year' => 'nullable|integer|min:1900|max:2100',
            'color' => 'nullable|string|max:255',
            'license_plate' => 'required|string|max:20|unique:vehicles,license_plate',
            'client_id' => [
                'required',
                'integer',
                \Illuminate\Validation\Rule::exists('clients', 'id')->where(function ($query) {
                    $query->where('user_id', auth()->id());
                }),
            ],
        ];
    }

    protected function prepareForValidation(): void
    {
        if ($this->filled('license_plate')) {
            $this->merge([
                'license_plate' => strtoupper(trim((string) $this->input('license_plate'))),
            ]);
        }
    }
}
