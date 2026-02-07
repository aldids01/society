<?php

namespace App\Filament\Resources\Members\Api\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateMemberRequest extends FormRequest
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
			'user_id' => 'required',
			'name' => 'required',
			'gender' => 'required',
			'phone' => 'required',
			'email' => 'required',
			'address' => 'required',
			'kin_name' => 'required',
			'kin_relationship' => 'required',
			'kin_phone' => 'required',
			'kin_address' => 'required',
			'saving' => 'required|numeric',
			'status' => 'required',
			'deleted_at' => 'required'
		];
    }
}
