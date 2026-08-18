<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SupplierDocumentRequirement extends Model
{
    use HasFactory;

    protected $fillable = [
        'organization_id', 'name', 'description', 'is_required', 'is_active', 'sort_order',
    ];

    protected $casts = [
        'is_required' => 'boolean',
        'is_active'   => 'boolean',
    ];

    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    public function documents()
    {
        return $this->hasMany(SupplierDocument::class, 'requirement_id');
    }

    /**
     * Documents a given supplier uploaded against this requirement.
     */
    public function supplierDocuments(Supplier $supplier)
    {
        return $this->documents()->where('supplier_id', $supplier->id)->get();
    }
}
