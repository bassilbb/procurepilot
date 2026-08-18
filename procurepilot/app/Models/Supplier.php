<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    use HasFactory;

    protected $fillable = [
        'organization_id', 'name', 'reg_number', 'email', 'phone', 'address',
        'country', 'category_id', 'tax_id', 'bank_account_name', 'bank_account_number',
        'bank_name', 'certifications', 'rating', 'status', 'notes',
        'approved_by', 'approved_at',
    ];

    protected $casts = [
        'approved_at' => 'datetime',
    ];

    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function documents()
    {
        return $this->hasMany(SupplierDocument::class);
    }

    public function tenders()
    {
        return $this->belongsToMany(Tender::class, 'tender_suppliers')
            ->withTimestamps();
    }

    public function bids()
    {
        return $this->hasMany(Bid::class);
    }

    public function contracts()
    {
        return $this->hasMany(Contract::class);
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    public function statusLabel(): string
    {
        return match ($this->status) {
            'pending'    => 'Pending Vetting',
            'approved'   => 'Approved',
            'suspended'  => 'Suspended',
            'blacklisted'=> 'Blacklisted',
            default      => ucfirst($this->status),
        };
    }
}
