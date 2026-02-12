<?php
namespace App\Filament\Resources\Guarantors\Api;

use Rupadana\ApiService\ApiService;
use App\Filament\Resources\Guarantors\GuarantorResource;


class GuarantorApiService extends ApiService
{
    protected static string | null $resource = GuarantorResource::class;

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
