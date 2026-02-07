<?php

namespace App\Filament\Resources\Grains\Api\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateGrainRequest extends FormRequest
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
			'slug' => 'required',
			'member_id' => 'required',
			'rate' => 'required',
			'terms' => 'required',
			'amount' => 'required|numeric',
			'status' => 'required',
			'start_date' => 'required|date',
			'deleted_at' => 'required'
		];
    }
}
