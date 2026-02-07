<?php
namespace App\Filament\Resources\Users\Api\Handlers;

use Illuminate\Http\Request;
use Rupadana\ApiService\Http\Handlers;
use App\Filament\Resources\Users\UserResource;
use App\Filament\Resources\Users\Api\Requests\UpdateUserRequest;

class UpdateHandler extends Handlers {
    public static string | null $uri = '/{id}';
    public static string | null $resource = UserResource::class;
    protected static string $permission = 'Update:User';

    public static function getMethod()
    {
        return Handlers::PUT;
    }

    public static function getModel() {
        return static::$resource::getModel();
    }


    /**
     * Update User
     *
     * @param UpdateUserRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function handler(UpdateUserRequest $request)
    {
        $id = $request->route('id');

        $model = static::getModel()::find($id);

        if (!$model) return static::sendNotFoundResponse();

        $model->fill($request->all());

        $model->save();

        return static::sendSuccessResponse($model, "Successfully Update Resource");
    }
}