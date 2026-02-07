<?php
namespace App\Filament\Resources\Savings\Api;

use Rupadana\ApiService\ApiService;
use App\Filament\Resources\Savings\SavingResource;


class SavingApiService extends ApiService
{
    protected static string | null $resource = SavingResource::class;

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
