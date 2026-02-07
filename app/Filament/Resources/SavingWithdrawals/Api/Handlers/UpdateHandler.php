<?php
namespace App\Filament\Resources\SavingWithdrawals\Api\Handlers;

use Illuminate\Http\Request;
use Rupadana\ApiService\Http\Handlers;
use App\Filament\Resources\SavingWithdrawals\SavingWithdrawalResource;
use App\Filament\Resources\SavingWithdrawals\Api\Requests\UpdateSavingWithdrawalRequest;

class UpdateHandler extends Handlers {
    public static string | null $uri = '/{id}';
    public static string | null $resource = SavingWithdrawalResource::class;
    protected static string $permission = 'Update:SavingWithdrawal';

    public static function getMethod()
    {
        return Handlers::PUT;
    }

    public static function getModel() {
        return static::$resource::getModel();
    }


    /**
     * Update SavingWithdrawal
     *
     * @param UpdateSavingWithdrawalRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function handler(UpdateSavingWithdrawalRequest $request)
    {
        $id = $request->route('id');

        $model = static::getModel()::find($id);

        if (!$model) return static::sendNotFoundResponse();

        $model->fill($request->all());

        $model->save();

        return static::sendSuccessResponse($model, "Successfully Update Resource");
    }
}