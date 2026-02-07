<?php
namespace App\Filament\Resources\SavingReports\Api\Handlers;

use Illuminate\Http\Request;
use Rupadana\ApiService\Http\Handlers;
use App\Filament\Resources\SavingReports\SavingReportResource;
use App\Filament\Resources\SavingReports\Api\Requests\UpdateSavingReportRequest;

class UpdateHandler extends Handlers {
    public static string | null $uri = '/{id}';
    public static string | null $resource = SavingReportResource::class;
    protected static string $permission = 'Update:SavingReport';

    public static function getMethod()
    {
        return Handlers::PUT;
    }

    public static function getModel() {
        return static::$resource::getModel();
    }


    /**
     * Update SavingReport
     *
     * @param UpdateSavingReportRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function handler(UpdateSavingReportRequest $request)
    {
        $id = $request->route('id');

        $model = static::getModel()::find($id);

        if (!$model) return static::sendNotFoundResponse();

        $model->fill($request->all());

        $model->save();

        return static::sendSuccessResponse($model, "Successfully Update Resource");
    }
}