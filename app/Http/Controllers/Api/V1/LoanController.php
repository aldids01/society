<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\StoreLoanRequest;
use App\Http\Requests\Api\V1\UpdateLoanRequest;
use App\Http\Resources\LoanResource;
use App\Models\Loan;

class LoanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return LoanResource::collection(Loan::all());

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreLoanRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $loan)
    {
//        $showloan = Loan::query()->where('member_id', '=', $loan)->firstOrFail();
//        return $this->success(new LoanResource($showloan));

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateLoanRequest $request, Loan $loan)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Loan $loan)
    {
        //
    }
}
