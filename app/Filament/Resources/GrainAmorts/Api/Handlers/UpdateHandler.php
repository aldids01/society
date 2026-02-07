<?php
namespace App\Filament\Resources\GrainAmorts\Api\Handlers;

use Illuminate\Http\Request;
use Rupadana\ApiService\Http\Handlers;
use App\Filament\Resources\GrainAmorts\GrainAmortResource;
use App\Filament\Resources\GrainAmorts\Api\Requests\UpdateGrainAmortRequest;

class UpdateHandler extends Handlers {
    public static string | null $uri = '/{id}';
    public static string | null $resource = GrainAmortResource::class;
    protected static string $permission = 'Update:GrainAmort';

    public static function getMethod()
    {
        return Handlers::PUT;
    }

    public static function getModel() {
        return static::$resource::getModel();
    }


    /**
     * Update GrainAmort
     *
     * @param UpdateGrainAmortRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function handler(UpdateGrainAmortRequest $request)
    {
        $id = $request->route('id');

        $model = static::getModel()::find($id);

        if (!$model) return static::sendNotFoundResponse();

        $model->fill($request->all());

        $model->save();

        return static::sendSuccessResponse($model, "Successfully Update Resource");
    }
}