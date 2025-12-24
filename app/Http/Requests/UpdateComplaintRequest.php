<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateComplaintRequest extends FormRequest
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
                 'type' => ['sometimes', Rule::in([
                     'service_missing',
                     'power_outage',
                     'corruption',
                     'employee_misconduct',
                     'technical_issue'
                 ])],
                 'department_id' => ['sometimes'],
                 'description' => ['sometimes', 'string', 'min:20'],
                 'location_text' => ['nullable', 'string'],
                 'files.*' => ['file', 'mimes:jpg,jpeg,png,pdf,mp4', 'max:10240']
        ];
    }
}
