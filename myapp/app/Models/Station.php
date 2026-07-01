<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Station extends Model
{
    protected $primaryKey = 'station_id';

    protected $fillable = ['name', 'district', 'address', 'contact_number', 'status'];

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
