<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LoanResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'loan_id' => $this->loan_id,
            'member_id' => $this->member_id,
            'saved' => $this->saved,
            'amount' => $this->amount,
            'rate' => $this->rate,
            'terms' => $this->terms,
            'start_date' => $this->start_date
        ];
    }
}
