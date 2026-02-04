<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GrainResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'grain_id' => $this->grain_id,
            'member_id' => $this->member_id,
            'amount' => $this->amount,
            'rate' => $this->rate,
            'terms' => $this->terms,
            'start_date' => $this->start_date
        ];
    }
}
