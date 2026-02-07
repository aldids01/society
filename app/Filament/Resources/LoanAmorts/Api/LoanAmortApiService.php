<?php
namespace App\Filament\Resources\LoanAmorts\Api;

use Rupadana\ApiService\ApiService;
use App\Filament\Resources\LoanAmorts\LoanAmortResource;


class LoanAmortApiService extends ApiService
{
    protected static string | null $resource = LoanAmortResource::class;

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
