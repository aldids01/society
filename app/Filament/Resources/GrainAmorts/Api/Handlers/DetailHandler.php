<?php

namespace App\Filament\Resources\GrainAmorts\Api\Handlers;

use App\Filament\Resources\SettingResource;
use App\Filament\Resources\GrainAmorts\GrainAmortResource;
use Rupadana\ApiService\Http\Handlers;
use Spatie\QueryBuilder\QueryBuilder;
use Illuminate\Http\Request;
use App\Filament\Resources\GrainAmorts\Api\Transformers\GrainAmortTransformer;

class DetailHandler extends Handlers
{
    public static string | null $uri = '/{id}';
    public static string | null $resource = GrainAmortResource::class;
    protected static string $permission = 'View:GrainAmort';


    /**
     * Show GrainAmort
     *
     * @param Request $request
     * @return GrainAmortTransformer
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

        return new GrainAmortTransformer($query);
    }
}
