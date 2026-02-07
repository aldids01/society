<?php
namespace App\Filament\Resources\Loans\Api\Handlers;

use Illuminate\Http\Request;
use Rupadana\ApiService\Http\Handlers;
use App\Filament\Resources\Loans\LoanResource;
use App\Filament\Resources\Loans\Api\Requests\CreateLoanRequest;

class CreateHandler extends Handlers {
    public static string | null $uri = '/';
    public static string | null $resource = LoanResource::class;
    protected static string $permission = 'Create:Loan';

    public static function getMethod()
    {
        return Handlers::POST;
    }

    public static function getModel() {
        return static::$resource::getModel();
    }

    /**
     * Create Loan
     *
     * @param CreateLoanRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function handler(CreateLoanRequest $request)
    {
        $model = new (static::getModel());

        $model->fill($request->all());

        $model->save();

        return static::sendSuccessResponse($model, "Successfully Create Resource");
    }
}