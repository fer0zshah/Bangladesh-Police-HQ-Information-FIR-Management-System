<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CaseAuditLog extends Model
{
    protected $primaryKey = 'audit_log_id';
    protected $fillable = ['case_id', 'user_id', 'action', 'old_status', 'new_status', 'details'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
