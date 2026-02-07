<?php

namespace App\Filament\Resources\SavingReports\Api\Handlers;

use App\Filament\Resources\SettingResource;
use App\Filament\Resources\SavingReports\SavingReportResource;
use Rupadana\ApiService\Http\Handlers;
use Spatie\QueryBuilder\QueryBuilder;
use Illuminate\Http\Request;
use App\Filament\Resources\SavingReports\Api\Transformers\SavingReportTransformer;

class DetailHandler extends Handlers
{
    public static string | null $uri = '/{id}';
    public static string | null $resource = SavingReportResource::class;
    protected static string $permission = 'View:SavingReport';


    /**
     * Show SavingReport
     *
     * @param Request $request
     * @return SavingReportTransformer
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

        return new SavingReportTransformer($query);
    }
}
