<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Criminal extends Model
{
    protected $primaryKey = 'criminal_id';
    protected $fillable = ['nid_number', 'name', 'alias', 'date_of_birth', 'wanted_status'];

    public function cases(): BelongsToMany
    {
        return $this->belongsToMany(CaseFir::class, 'case_criminals', 'criminal_id', 'case_id')
                    ->withPivot('involvement_type')
                    ->withTimestamps();
    }
}