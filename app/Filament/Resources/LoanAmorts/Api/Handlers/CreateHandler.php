<?php
namespace App\Filament\Resources\LoanAmorts\Api\Handlers;

use Illuminate\Http\Request;
use Rupadana\ApiService\Http\Handlers;
use App\Filament\Resources\LoanAmorts\LoanAmortResource;
use App\Filament\Resources\LoanAmorts\Api\Requests\CreateLoanAmortRequest;

class CreateHandler extends Handlers {
    public static string | null $uri = '/';
    public static string | null $resource = LoanAmortResource::class;
    protected static string $permission = 'Create:LoanAmort';

    public static function getMethod()
    {
        return Handlers::POST;
    }

    public static function getModel() {
        return static::$resource::getModel();
    }

    /**
     * Create LoanAmort
     *
     * @param CreateLoanAmortRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function handler(CreateLoanAmortRequest $request)
    {
        $model = new (static::getModel());

        $model->fill($request->all());

        $model->save();

        return static::sendSuccessResponse($model, "Successfully Create Resource");
    }
}