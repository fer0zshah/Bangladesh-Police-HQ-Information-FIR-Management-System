<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class CitizenComplaint extends Model
{
    protected $primaryKey = 'complaint_id';
    protected $fillable = [
        'station_id', 'complainant_name', 'complainant_nid',
        'complaint_title', 'description', 'submitted_date', 'status',
    ];

    protected function casts(): array
    {
        return ['submitted_date' => 'date'];
    }

    public function station(): BelongsTo
    { 
        return $this->belongsTo(Station::class, 'station_id', 'station_id');
    }

    public function caseFir(): HasOne
    {
        return $this->hasOne(CaseFir::class, 'complaint_id', 'complaint_id');
    }
}
