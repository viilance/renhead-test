<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProfessorRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'first_name' => 'sometimes|max:255',
            'last_name' => 'sometimes|max:255',
            'email' => 'sometimes|email|unique:users,email',
            'password' => 'sometimes|min:6|max:255',
            'total_available_hours' => 'sometimes|numeric|min:0',
            'payroll_per_hour' => 'sometimes|numeric|min:0',
            'total_projects' => 'sometimes|integer|min:0',
            'office_number' => 'sometimes|integer|min:0',
        ];
    }
}
