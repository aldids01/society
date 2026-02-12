<?php
namespace App\Filament\Resources\Members\Api\Handlers;

use Illuminate\Http\Request;
use Rupadana\ApiService\Http\Handlers;
use App\Filament\Resources\Members\MemberResource;
use App\Filament\Resources\Members\Api\Requests\UpdateMemberRequest;

class UpdateHandler extends Handlers {
    public static string | null $uri = '/{slug}';
    public static string | null $resource = MemberResource::class;
    protected static string $permission = 'Update:Member';

    public static function getMethod()
    {
        return Handlers::PUT;
    }

    public static function getModel() {
        return static::$resource::getModel();
    }


    /**
     * Update Member
     *
     * @param UpdateMemberRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function handler(UpdateMemberRequest $request)
    {
        $id = $request->route('slug');

        $model = static::getModel()::where('slug', '=', $id);

        if (!$model) return static::sendNotFoundResponse();

        $model->fill($request->all());

        $model->save();

        return static::sendSuccessResponse($model, "Successfully Update Resource");
    }
}
