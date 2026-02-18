<?php

namespace App\Http\Requests\Client;

use Illuminate\Foundation\Http\FormRequest;

class StoreClientRequest extends FormRequest
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
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:clients,email',
            'phone' => 'required|string|unique:clients,phone|max:255',
        ];
    }

    protected function prepareForValidation(): void
    {
        if ($this->phone) {

            $phone = preg_replace('/\D/', '', $this->phone);

            if (! str_starts_with($phone, '54')) {
                $phone = '54'.$phone;
            }

            $this->merge([
                'phone' => '+'.$phone,
            ]);
        }
    }

    public function messages()
    {
        return [
            'name.required' => 'El nombre es requerido',
            'email.required' => 'El email es requerido',
            'phone.required' => 'El telefono es requerido',
            'email.unique' => 'El email ya existe',
            'phone.unique' => 'El telefono ya existe',
        ];
    }
}
