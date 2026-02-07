<?php
namespace App\Filament\Resources\SavingWithdrawals\Api\Handlers;

use Illuminate\Http\Request;
use Rupadana\ApiService\Http\Handlers;
use Spatie\QueryBuilder\QueryBuilder;
use App\Filament\Resources\SavingWithdrawals\SavingWithdrawalResource;
use App\Filament\Resources\SavingWithdrawals\Api\Transformers\SavingWithdrawalTransformer;

class PaginationHandler extends Handlers {
    public static string | null $uri = '/';
    public static string | null $resource = SavingWithdrawalResource::class;
    protected static string $permission = 'ViewAny:SavingWithdrawal';


    /**
     * List of SavingWithdrawal
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

        return SavingWithdrawalTransformer::collection($query);
    }
}
