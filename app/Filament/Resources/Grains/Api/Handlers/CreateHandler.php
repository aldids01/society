<?php
namespace App\Filament\Resources\Grains\Api\Handlers;

use Illuminate\Http\Request;
use Rupadana\ApiService\Http\Handlers;
use App\Filament\Resources\Grains\GrainResource;
use App\Filament\Resources\Grains\Api\Requests\CreateGrainRequest;

class CreateHandler extends Handlers {
    public static string | null $uri = '/';
    public static string | null $resource = GrainResource::class;
    protected static string $permission = 'Create:Grain';

    public static function getMethod()
    {
        return Handlers::POST;
    }

    public static function getModel() {
        return static::$resource::getModel();
    }

    /**
     * Create Grain
     *
     * @param CreateGrainRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function handler(CreateGrainRequest $request)
    {
        $model = new (static::getModel());

        $model->fill($request->all());

        $model->save();

        return static::sendSuccessResponse($model, "Successfully Create Resource");
    }
}