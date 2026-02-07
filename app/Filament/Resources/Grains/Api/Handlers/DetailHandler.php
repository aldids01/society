<?php

namespace App\Filament\Resources\Grains\Api\Handlers;

use App\Filament\Resources\SettingResource;
use App\Filament\Resources\Grains\GrainResource;
use Rupadana\ApiService\Http\Handlers;
use Spatie\QueryBuilder\QueryBuilder;
use Illuminate\Http\Request;
use App\Filament\Resources\Grains\Api\Transformers\GrainTransformer;

class DetailHandler extends Handlers
{
    public static string | null $uri = '/{id}';
    public static string | null $resource = GrainResource::class;
    protected static string $permission = 'View:Grain';


    /**
     * Show Grain
     *
     * @param Request $request
     * @return GrainTransformer
     */
    public function handler(Request $request)
    {
        $id = $request->route('id');
        
        $query = static::getEloquentQuery();

        $query = QueryBuilder::for(
            $query->where(static::getKeyName(), $id)
        )
            ->first();

        if (!$query) return static::sendNotFoundResponse();

        return new GrainTransformer($query);
    }
}
