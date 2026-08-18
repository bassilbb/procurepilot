<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApprovalRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'organization_id', 'approvable_type', 'approvable_id', 'approval_level_id',
        'approver_id', 'status', 'comment', 'decided_at',
    ];

    protected $casts = [
        'decided_at' => 'datetime',
    ];

    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    public function approvable()
    {
        return $this->morphTo();
    }

    public function level()
    {
        return $this->belongsTo(ApprovalLevel::class, 'approval_level_id');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approver_id');
    }
}
