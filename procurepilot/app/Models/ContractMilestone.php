<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContractMilestone extends Model
{
    use HasFactory;

    protected $fillable = ['contract_id', 'title', 'due_date', 'amount', 'status', 'completed_at'];

    protected $casts = [
        'due_date'     => 'date',
        'completed_at' => 'datetime',
    ];

    public function contract()
    {
        return $this->belongsTo(Contract::class);
    }

    public function statusLabel(): string
    {
        return match ($this->status) {
            'pending'   => 'Pending',
            'completed' => 'Completed',
            'overdue'   => 'Overdue',
            default     => ucfirst($this->status),
        };
    }
}
