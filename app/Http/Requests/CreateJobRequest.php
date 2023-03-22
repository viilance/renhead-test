<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateJobRequest extends FormRequest
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
            'employee_type' => [
                'required',
                Rule::in(['professor', 'trader']),
            ],
            'employee_id' => [
                'required',
                'integer',
                'employee_exists',
            ],
            'date' => 'required|date',
            'total_hours' => 'required|numeric|min:0',
        ];
    }
}
