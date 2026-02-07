<?php
namespace App\Filament\Resources\SavingReports\Api\Handlers;

use Illuminate\Http\Request;
use Rupadana\ApiService\Http\Handlers;
use App\Filament\Resources\SavingReports\SavingReportResource;
use App\Filament\Resources\SavingReports\Api\Requests\CreateSavingReportRequest;

class CreateHandler extends Handlers {
    public static string | null $uri = '/';
    public static string | null $resource = SavingReportResource::class;
    protected static string $permission = 'Create:SavingReport';

    public static function getMethod()
    {
        return Handlers::POST;
    }

    public static function getModel() {
        return static::$resource::getModel();
    }

    /**
     * Create SavingReport
     *
     * @param CreateSavingReportRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function handler(CreateSavingReportRequest $request)
    {
        $model = new (static::getModel());

        $model->fill($request->all());

        $model->save();

        return static::sendSuccessResponse($model, "Successfully Create Resource");
    }
}