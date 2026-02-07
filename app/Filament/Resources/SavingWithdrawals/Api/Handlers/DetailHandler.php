<?php

namespace App\Filament\Resources\SavingWithdrawals\Api\Handlers;

use App\Filament\Resources\SettingResource;
use App\Filament\Resources\SavingWithdrawals\SavingWithdrawalResource;
use Rupadana\ApiService\Http\Handlers;
use Spatie\QueryBuilder\QueryBuilder;
use Illuminate\Http\Request;
use App\Filament\Resources\SavingWithdrawals\Api\Transformers\SavingWithdrawalTransformer;

class DetailHandler extends Handlers
{
    public static string | null $uri = '/{id}';
    public static string | null $resource = SavingWithdrawalResource::class;
    protected static string $permission = 'View:SavingWithdrawal';


    /**
     * Show SavingWithdrawal
     *
     * @param Request $request
     * @return SavingWithdrawalTransformer
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

        return new SavingWithdrawalTransformer($query);
    }
}
