<?php
namespace App\Filament\Resources\SavingWithdrawals\Api;

use Rupadana\ApiService\ApiService;
use App\Filament\Resources\SavingWithdrawals\SavingWithdrawalResource;


class SavingWithdrawalApiService extends ApiService
{
    protected static string | null $resource = SavingWithdrawalResource::class;

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
