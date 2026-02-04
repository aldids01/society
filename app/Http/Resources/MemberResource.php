<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MemberResource extends JsonResource
{
    public static $wrap = 'members';
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'slug' => $this->slug,
            'name' => $this->name,
            'gender' => $this->gender,
            'email' => $this->email,
            'phone' => $this->phone,
            'address' => $this->address,
            'saving' => $this->saving,
            'status' => $this->status,
            'relationships' => [
//                'user' => UserResource::make($this->user())
//              'loans' => LoanResource::make($this->loans()),
//              'grains' => grainResource::make($this->grains()),
            ],

        ];
    }
}
