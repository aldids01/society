<?php
namespace App\Filament\Resources\Members\Api\Handlers;

use Illuminate\Http\Request;
use Rupadana\ApiService\Http\Handlers;
use App\Filament\Resources\Members\MemberResource;
use App\Filament\Resources\Members\Api\Requests\CreateMemberRequest;

class CreateHandler extends Handlers {
    public static string | null $uri = '/';
    public static string | null $resource = MemberResource::class;
    protected static string $permission = 'Create:Member';

    public static function getMethod()
    {
        return Handlers::POST;
    }

    public static function getModel() {
        return static::$resource::getModel();
    }

    /**
     * Create Member
     *
     * @param CreateMemberRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function handler(CreateMemberRequest $request)
    {
        $model = new (static::getModel());

        $model->fill($request->all());

        $model->save();

        return static::sendSuccessResponse($model, "Successfully Create Resource");
    }
}