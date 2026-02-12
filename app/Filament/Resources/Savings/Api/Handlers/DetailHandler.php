<?php

namespace App\Filament\Resources\Savings\Api\Handlers;

use App\Filament\Resources\SettingResource;
use App\Filament\Resources\Savings\SavingResource;
use Rupadana\ApiService\Http\Handlers;
use Spatie\QueryBuilder\QueryBuilder;
use Illuminate\Http\Request;
use App\Filament\Resources\Savings\Api\Transformers\SavingTransformer;

class DetailHandler extends Handlers
{
    public static string | null $uri = '/{slug}';
    public static string | null $resource = SavingResource::class;
    protected static string $permission = 'View:Saving';


    /**
     * Show Saving
     *
     * @param Request $request
     * @return SavingTransformer
     */
    public function handler(Request $request)
    {
        $id = $request->route('slug');

        $query = static::getEloquentQuery();

        $query = QueryBuilder::for(
            $query->where('slug', '=', $id)
        )
            ->first();

        if (!$query) return static::sendNotFoundResponse();

        return new SavingTransformer($query);
    }
}
