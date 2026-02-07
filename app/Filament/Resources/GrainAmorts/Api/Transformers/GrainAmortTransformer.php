<?php
namespace App\Filament\Resources\GrainAmorts\Api\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\GrainAmort;

/**
 * @property GrainAmort $resource
 */
class GrainAmortTransformer extends JsonResource
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
