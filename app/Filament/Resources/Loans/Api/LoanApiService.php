<?php
namespace App\Filament\Resources\Loans\Api;

use Rupadana\ApiService\ApiService;
use App\Filament\Resources\Loans\LoanResource;


class LoanApiService extends ApiService
{
    protected static string | null $resource = LoanResource::class;

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
