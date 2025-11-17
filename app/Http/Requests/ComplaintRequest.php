<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;


class ComplaintRequest extends FormRequest
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
            'type' => ['required', Rule::in([
                'service_missing',
                'power_outage',
                'corruption',
                'employee_misconduct',
                'technical_issue'
            ])],
            'authority' => ['required', Rule::in([
                'municipality',
                'electric_company',
                'water_authority',
                'health_directorate',
                'other'
            ])],
            'description' => ['required', 'string', 'min:20'],
            'location_text' => ['nullable', 'string'],

            'files.*' => ['file', 'mimes:jpg,jpeg,png,pdf,mp4', 'max:5120']


        ];
    }
}
