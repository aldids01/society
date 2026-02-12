<?php

namespace App\Filament\Resources\Guarantors\Api\Handlers;

use App\Filament\Resources\SettingResource;
use App\Filament\Resources\Guarantors\GuarantorResource;
use Rupadana\ApiService\Http\Handlers;
use Spatie\QueryBuilder\QueryBuilder;
use Illuminate\Http\Request;
use App\Filament\Resources\Guarantors\Api\Transformers\GuarantorTransformer;

class DetailHandler extends Handlers
{
    public static string | null $uri = '/{id}';
    public static string | null $resource = GuarantorResource::class;
    protected static string $permission = 'View:Guarantor';


    /**
     * Show Guarantor
     *
     * @param Request $request
     * @return GuarantorTransformer
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

        return new GuarantorTransformer($query);
    }
}
