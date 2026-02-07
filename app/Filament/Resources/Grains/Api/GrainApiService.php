<?php
namespace App\Filament\Resources\Grains\Api;

use Rupadana\ApiService\ApiService;
use App\Filament\Resources\Grains\GrainResource;


class GrainApiService extends ApiService
{
    protected static string | null $resource = GrainResource::class;

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
