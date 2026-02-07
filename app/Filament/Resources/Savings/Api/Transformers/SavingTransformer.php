<?php
namespace App\Filament\Resources\Savings\Api\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\Saving;

/**
 * @property Saving $resource
 */
class SavingTransformer extends JsonResource
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
