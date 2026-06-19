<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Evidence extends Model
{
    protected $table = 'evidence'; // Explicitly defining because plural of evidence is evidence
    protected $primaryKey = 'evidence_id';
    protected $fillable = ['case_id', 'officer_id', 'type', 'description', 'collected_date'];

    public function case(): BelongsTo
    {
        return $this->belongsTo(CaseFir::class, 'case_id', 'case_id');
    }

    public function officer(): BelongsTo
    {
        return $this->belongsTo(Officer::class, 'officer_id', 'officer_id');
    }
}