<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;

class StoreLoanRequest extends FormRequest
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
            'loan_id' => ['required|string'],
            'member_id' => ['required|string'],
            'saved' => ['required|numeric'],
            'amount' => ['required|numeric'],
            'rate' => ['required|numeric'],
            'terms' => ['required|numeric'],
            'start_date' => ['required|date'],
        ];
    }
}
