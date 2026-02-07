<?php

namespace App\Filament\Resources\SavingReports\Api\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateSavingReportRequest extends FormRequest
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
			'member_id' => 'required',
			'annual' => 'required',
			'January' => 'required|numeric',
			'February' => 'required|numeric',
			'March' => 'required|numeric',
			'April' => 'required|numeric',
			'May' => 'required|numeric',
			'June' => 'required|numeric',
			'July' => 'required|numeric',
			'August' => 'required|numeric',
			'September' => 'required|numeric',
			'October' => 'required|numeric',
			'November' => 'required|numeric',
			'December' => 'required|numeric',
			'total' => 'required|numeric',
			'status' => 'required',
			'deleted_at' => 'required'
		];
    }
}
