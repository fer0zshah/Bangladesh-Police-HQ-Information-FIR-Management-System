<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class CaseFir extends Model
{
    protected $primaryKey = 'case_id';
    protected $fillable = ['station_id', 'investigating_officer_id', 'complaint_id', 'case_title', 'date_filed', 'status'];

    public function station(): BelongsTo
    {
        return $this->belongsTo(Station::class, 'station_id', 'station_id');
    }

    public function officer(): BelongsTo
    {
        return $this->belongsTo(Officer::class, 'investigating_officer_id', 'officer_id');
    }

    // Many-to-Many Relationship with Criminals via the bridging table
    public function criminals(): BelongsToMany
    {
        return $this->belongsToMany(Criminal::class, 'case_criminals', 'case_id', 'criminal_id')
                    ->withPivot('involvement_type')
                    ->withTimestamps();
    }
}