<?php

namespace App\Filament\Resources\Loans\Api\Handlers;

use App\Filament\Resources\SettingResource;
use App\Filament\Resources\Loans\LoanResource;
use Rupadana\ApiService\Http\Handlers;
use Spatie\QueryBuilder\QueryBuilder;
use Illuminate\Http\Request;
use App\Filament\Resources\Loans\Api\Transformers\LoanTransformer;

class DetailHandler extends Handlers
{
    public static string | null $uri = '/{id}';
    public static string | null $resource = LoanResource::class;
    protected static string $permission = 'View:Loan';


    /**
     * Show Loan
     *
     * @param Request $request
     * @return LoanTransformer
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

        return new LoanTransformer($query);
    }
}
