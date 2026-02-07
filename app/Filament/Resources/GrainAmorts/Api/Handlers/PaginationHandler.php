<?php
namespace App\Filament\Resources\GrainAmorts\Api\Handlers;

use Illuminate\Http\Request;
use Rupadana\ApiService\Http\Handlers;
use Spatie\QueryBuilder\QueryBuilder;
use App\Filament\Resources\GrainAmorts\GrainAmortResource;
use App\Filament\Resources\GrainAmorts\Api\Transformers\GrainAmortTransformer;

class PaginationHandler extends Handlers {
    public static string | null $uri = '/';
    public static string | null $resource = GrainAmortResource::class;
    protected static string $permission = 'ViewAny:GrainAmort';


    /**
     * List of GrainAmort
     *
     * @param Request $request
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function handler()
    {
        $query = static::getEloquentQuery();

        $query = QueryBuilder::for($query)
        ->allowedFields($this->getAllowedFields() ?? [])
        ->allowedSorts($this->getAllowedSorts() ?? [])
        ->allowedFilters($this->getAllowedFilters() ?? [])
        ->allowedIncludes($this->getAllowedIncludes() ?? [])
        ->paginate(request()->query('per_page'))
        ->appends(request()->query());

        return GrainAmortTransformer::collection($query);
    }
}
