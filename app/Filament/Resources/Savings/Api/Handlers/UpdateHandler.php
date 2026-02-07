<?php
namespace App\Filament\Resources\Savings\Api\Handlers;

use Illuminate\Http\Request;
use Rupadana\ApiService\Http\Handlers;
use App\Filament\Resources\Savings\SavingResource;
use App\Filament\Resources\Savings\Api\Requests\UpdateSavingRequest;

class UpdateHandler extends Handlers {
    public static string | null $uri = '/{id}';
    public static string | null $resource = SavingResource::class;
    protected static string $permission = 'Update:Saving';

    public static function getMethod()
    {
        return Handlers::PUT;
    }

    public static function getModel() {
        return static::$resource::getModel();
    }


    /**
     * Update Saving
     *
     * @param UpdateSavingRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function handler(UpdateSavingRequest $request)
    {
        $id = $request->route('id');

        $model = static::getModel()::find($id);

        if (!$model) return static::sendNotFoundResponse();

        $model->fill($request->all());

        $model->save();

        return static::sendSuccessResponse($model, "Successfully Update Resource");
    }
}