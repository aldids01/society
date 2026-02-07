<?php
namespace App\Filament\Resources\SavingReports\Api;

use Rupadana\ApiService\ApiService;
use App\Filament\Resources\SavingReports\SavingReportResource;


class SavingReportApiService extends ApiService
{
    protected static string | null $resource = SavingReportResource::class;

    public static function handlers() : array
    {
        return [
            Handlers\CreateHandler::class,
            Handlers\UpdateHandler::class,
            Handlers\DeleteHandler::class,
            Handlers\PaginationHandler::class,
            Handlers\DetailHandler::class
        ];

    }
}
