<?php
namespace App\Filament\Resources\Guarantors\Api\Handlers;

use Illuminate\Http\Request;
use Rupadana\ApiService\Http\Handlers;
use App\Filament\Resources\Guarantors\GuarantorResource;
use App\Filament\Resources\Guarantors\Api\Requests\UpdateGuarantorRequest;

class UpdateHandler extends Handlers {
    public static string | null $uri = '/{id}';
    public static string | null $resource = GuarantorResource::class;
    protected static string $permission = 'Update:Guarantor';

    public static function getMethod()
    {
        return Handlers::PUT;
    }

    public static function getModel() {
        return static::$resource::getModel();
    }


    /**
     * Update Guarantor
     *
     * @param UpdateGuarantorRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function handler(UpdateGuarantorRequest $request)
    {
        $id = $request->route('id');

        $model = static::getModel()::find($id);

        if (!$model) return static::sendNotFoundResponse();

        $model->fill($request->all());

        $model->save();

        return static::sendSuccessResponse($model, "Successfully Update Resource");
    }
}