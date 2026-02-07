<?php

namespace App\Filament\Resources\GrainAmorts\Api\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateGrainAmortRequest extends FormRequest
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
			'grain_id' => 'required',
			'member_id' => 'required',
			'annual' => 'required',
			'period' => 'required',
			'interest' => 'required|numeric',
			'principal' => 'required|numeric',
			'payment' => 'required|numeric',
			'start_balance' => 'required|numeric',
			'end_balance' => 'required|numeric',
			'status' => 'required',
			'deleted_at' => 'required'
		];
    }
}
