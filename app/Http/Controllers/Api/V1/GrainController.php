<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\StoreGrainRequest;
use App\Http\Requests\Api\V1\UpdateGrainRequest;
use App\Http\Resources\GrainResource;
use App\Models\Grain;
use Spatie\QueryBuilder\QueryBuilder;

class GrainController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return GrainResource::collection(Grain::all());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreGrainRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Grain $grain)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateGrainRequest $request, Grain $grain)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Grain $grain)
    {
        //
    }
}
