<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

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

    public function auditLogs(): HasMany
    {
        return $this->hasMany(CaseAuditLog::class, 'case_id', 'case_id')->latest('audit_log_id');
    }

    public function evidence(): HasMany
    {
        return $this->hasMany(Evidence::class, 'case_id', 'case_id');
    }
}
