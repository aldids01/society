<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Saving extends Model
{
    /** @use HasFactory<\Database\Factories\SavingFactory> */
    use HasFactory, SoftDeletes;

    protected $guarded = [];

    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::Class, 'member_id', 'slug');
    }
}
