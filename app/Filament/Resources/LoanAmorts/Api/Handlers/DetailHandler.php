<?php

namespace App\Filament\Resources\LoanAmorts\Api\Handlers;

use App\Filament\Resources\SettingResource;
use App\Filament\Resources\LoanAmorts\LoanAmortResource;
use Rupadana\ApiService\Http\Handlers;
use Spatie\QueryBuilder\QueryBuilder;
use Illuminate\Http\Request;
use App\Filament\Resources\LoanAmorts\Api\Transformers\LoanAmortTransformer;

class DetailHandler extends Handlers
{
    public static string | null $uri = '/{id}';
    public static string | null $resource = LoanAmortResource::class;
    protected static string $permission = 'View:LoanAmort';


    /**
     * Show LoanAmort
     *
     * @param Request $request
     * @return LoanAmortTransformer
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

        return new LoanAmortTransformer($query);
    }
}
