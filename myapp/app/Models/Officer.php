<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Officer extends Model
{
    protected $primaryKey = 'officer_id';

    protected $fillable = [
        'user_id',
        'station_id',
        'name',
        'badge_number',
        'rank',
        'status',
        'is_oc',
    ];

    protected function casts(): array
    {
        return [
            'is_oc' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function station(): BelongsTo
    {
        return $this->belongsTo(Station::class, 'station_id', 'station_id');
    }

    public function cases(): HasMany
    {
        return $this->hasMany(CaseFir::class, 'investigating_officer_id', 'officer_id');
    }

    public function evidence(): HasMany
    {
        return $this->hasMany(Evidence::class, 'officer_id', 'officer_id');
    }
}
