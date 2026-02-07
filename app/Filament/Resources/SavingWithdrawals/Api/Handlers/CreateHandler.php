<?php
namespace App\Filament\Resources\SavingWithdrawals\Api\Handlers;

use Illuminate\Http\Request;
use Rupadana\ApiService\Http\Handlers;
use App\Filament\Resources\SavingWithdrawals\SavingWithdrawalResource;
use App\Filament\Resources\SavingWithdrawals\Api\Requests\CreateSavingWithdrawalRequest;

class CreateHandler extends Handlers {
    public static string | null $uri = '/';
    public static string | null $resource = SavingWithdrawalResource::class;
    protected static string $permission = 'Create:SavingWithdrawal';

    public static function getMethod()
    {
        return Handlers::POST;
    }

    public static function getModel() {
        return static::$resource::getModel();
    }

    /**
     * Create SavingWithdrawal
     *
     * @param CreateSavingWithdrawalRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function handler(CreateSavingWithdrawalRequest $request)
    {
        $model = new (static::getModel());

        $model->fill($request->all());

        $model->save();

        return static::sendSuccessResponse($model, "Successfully Create Resource");
    }
}