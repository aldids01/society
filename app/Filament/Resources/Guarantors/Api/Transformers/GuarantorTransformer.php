<?php
namespace App\Filament\Resources\Guarantors\Api\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\Guarantor;

/**
 * @property Guarantor $resource
 */
class GuarantorTransformer extends JsonResource
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
