<?php

namespace App\Filament\Resources\Users\Api\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateUserRequest extends FormRequest
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
			'name' => 'required',
			'email' => 'required',
			'email_verified_at' => 'required',
			'password' => 'required',
			'two_factor_secret' => 'required|string',
			'two_factor_recovery_codes' => 'required|string',
			'two_factor_confirmed_at' => 'required',
			'remember_token' => 'required',
			'deleted_at' => 'required'
		];
    }
}
