<?php
namespace App\Filament\Resources\Guarantors\Api\Handlers;

use Illuminate\Http\Request;
use Rupadana\ApiService\Http\Handlers;
use App\Filament\Resources\Guarantors\GuarantorResource;
use App\Filament\Resources\Guarantors\Api\Requests\CreateGuarantorRequest;

class CreateHandler extends Handlers {
    public static string | null $uri = '/';
    public static string | null $resource = GuarantorResource::class;
    protected static string $permission = 'Create:Guarantor';

    public static function getMethod()
    {
        return Handlers::POST;
    }

    public static function getModel() {
        return static::$resource::getModel();
    }

    /**
     * Create Guarantor
     *
     * @param CreateGuarantorRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function handler(CreateGuarantorRequest $request)
    {
        $model = new (static::getModel());

        $model->fill($request->all());

        $model->save();

        return static::sendSuccessResponse($model, "Successfully Create Resource");
    }
}