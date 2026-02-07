<?php
namespace App\Filament\Resources\SavingWithdrawals\Api\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\Loan;

/**
 * @property SavingWithdrawal $resource
 */
class SavingWithdrawalTransformer extends JsonResource
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
