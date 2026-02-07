<?php

namespace App\Filament\Resources\Members\Api\Handlers;

use App\Filament\Resources\SettingResource;
use App\Filament\Resources\Members\MemberResource;
use Rupadana\ApiService\Http\Handlers;
use Spatie\QueryBuilder\QueryBuilder;
use Illuminate\Http\Request;
use App\Filament\Resources\Members\Api\Transformers\MemberTransformer;

class DetailHandler extends Handlers
{
    public static string | null $uri = '/{slug}';
    public static string | null $resource = MemberResource::class;
    protected static string $permission = 'View:Member';


    /**
     * Show Member
     *
     * @param Request $request
     * @return MemberTransformer
     */
    public function handler(Request $request)
    {
        $id = $request->route('slug');

        $query = static::getEloquentQuery();

        $query = QueryBuilder::for(
            $query->where('slug', $id)
        )
            ->first();

        if (!$query) return static::sendNotFoundResponse();

        return new MemberTransformer($query);
    }
}
