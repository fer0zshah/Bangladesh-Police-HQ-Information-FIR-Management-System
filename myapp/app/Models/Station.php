<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Station extends Model
{
    protected $primaryKey = 'station_id';

    protected $fillable = [
        'name',
        'type',
        'parent_id',
        'district',
        'division',
        'head_rank',
        'address',
        'contact_number',
        'jurisdiction',
        'status',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id', 'station_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id', 'station_id');
    }

    public function officers(): HasMany
    {
        return $this->hasMany(Officer::class, 'station_id', 'station_id');
    }

    public function complaints(): HasMany
    {
        return $this->hasMany(CitizenComplaint::class, 'station_id', 'station_id');
    }

    public function cases(): HasMany
    {
        return $this->hasMany(CaseFir::class, 'station_id', 'station_id');
    }
}
