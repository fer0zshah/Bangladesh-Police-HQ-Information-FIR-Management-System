<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class CaseCriminal extends Pivot
{
    protected $table = 'case_criminals';
    protected $primaryKey = 'involvement_id';
    protected $fillable = ['case_id', 'criminal_id', 'involvement_type'];
}