<?php
namespace App\Filament\Resources\Members\Api;

use Rupadana\ApiService\ApiService;
use App\Filament\Resources\Members\MemberResource;


class MemberApiService extends ApiService
{
    protected static string | null $resource = MemberResource::class;

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
