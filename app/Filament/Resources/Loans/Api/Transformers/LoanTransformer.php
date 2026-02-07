<?php
namespace App\Filament\Resources\Loans\Api\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\Loan;

/**
 * @property Loan $resource
 */
class LoanTransformer extends JsonResource
{

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return $this->resource->toArray();
    }
}
