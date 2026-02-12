<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Rupadana\ApiService\Contracts\HasAllowedFields;
use Rupadana\ApiService\Contracts\HasAllowedFilters;
use Rupadana\ApiService\Contracts\HasAllowedSorts;

class Guarantor extends Model implements HasAllowedFields, HasAllowedSorts, HasAllowedFilters
{
    protected $guarded = [];

    public function loan(): BelongsTo
    {
        return $this->belongsTo(Loan::class, 'loan_id', 'slug');
    }

    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class, 'member_id', 'slug');
    }

    public static function getAllowedFields(): array
    {
        return [
            'loan_id' => 'loan_id',
            'member_id' => 'member_id',
            'status' => 'status',
        ];
    }

    // Which fields can be used to sort the results through the query string
    public static function getAllowedSorts(): array
    {
        return [
            'created_at' => 'created_at',
        ];
    }

    // Which fields can be used to filter the results through the query string
    public static function getAllowedFilters(): array
    {
        return [
            'loan_id' => 'loan_id',
            'member_id' => 'member_id',
            'status' => 'status',
        ];
    }
}
