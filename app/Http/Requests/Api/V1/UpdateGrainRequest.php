<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;

class UpdateGrainRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'grain_id' => ['required|string'],
            'member_id' => ['required|string'],
            'amount' => ['required|numeric'],
            'rate' => ['required|numeric'],
            'terms' => ['required|numeric'],
            'start_date' => ['required|date'],
        ];
    }
}
