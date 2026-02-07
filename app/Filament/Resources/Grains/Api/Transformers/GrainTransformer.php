<?php
namespace App\Filament\Resources\Grains\Api\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\Grain;

/**
 * @property Grain $resource
 */
class GrainTransformer extends JsonResource
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
