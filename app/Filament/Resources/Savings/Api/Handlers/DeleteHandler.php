<?php
namespace App\Filament\Resources\Savings\Api\Handlers;

use Illuminate\Http\Request;
use Rupadana\ApiService\Http\Handlers;
use App\Filament\Resources\Savings\SavingResource;

class DeleteHandler extends Handlers {
    public static string | null $uri = '/{id}';
    public static string | null $resource = SavingResource::class;
    protected static string $permission = 'Delete:Saving';

    public static function getMethod()
    {
        return Handlers::DELETE;
    }

    public static function getModel() {
        return static::$resource::getModel();
    }

    /**
     * Delete Saving
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function handler(Request $request)
    {
        $id = $request->route('id');

        $model = static::getModel()::find($id);

        if (!$model) return static::sendNotFoundResponse();

        $model->delete();

        return static::sendSuccessResponse($model, "Successfully Delete Resource");
    }
}