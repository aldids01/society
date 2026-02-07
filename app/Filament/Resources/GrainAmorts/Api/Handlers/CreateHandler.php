<?php
namespace App\Filament\Resources\GrainAmorts\Api\Handlers;

use Illuminate\Http\Request;
use Rupadana\ApiService\Http\Handlers;
use App\Filament\Resources\GrainAmorts\GrainAmortResource;
use App\Filament\Resources\GrainAmorts\Api\Requests\CreateGrainAmortRequest;

class CreateHandler extends Handlers {
    public static string | null $uri = '/';
    public static string | null $resource = GrainAmortResource::class;
    protected static string $permission = 'Create:GrainAmort';

    public static function getMethod()
    {
        return Handlers::POST;
    }

    public static function getModel() {
        return static::$resource::getModel();
    }

    /**
     * Create GrainAmort
     *
     * @param CreateGrainAmortRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function handler(CreateGrainAmortRequest $request)
    {
        $model = new (static::getModel());

        $model->fill($request->all());

        $model->save();

        return static::sendSuccessResponse($model, "Successfully Create Resource");
    }
}