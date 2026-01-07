<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Grain extends Model
{
    use SoftDeletes;

    protected $guarded = [];
    protected $casts = [
        'start_date' => 'date',
    ];

    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::Class, 'member_id', 'slug');
    }

    public function grainAmorts(): HasMany
    {
        return $this->hasMany(GrainAmort::class, 'grain_id', 'slug');
    }

    public function grainApproval(): HasOne
    {
        return $this->hasOne(GrainApproval::class, 'grain_id', 'slug');
    }

}
