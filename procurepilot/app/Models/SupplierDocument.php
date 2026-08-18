<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SupplierDocument extends Model
{
    use HasFactory;

    protected $fillable = ['supplier_id', 'requirement_id', 'name', 'type', 'path', 'mime', 'size', 'verified_at'];

    protected $casts = ['verified_at' => 'datetime'];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function requirement()
    {
        return $this->belongsTo(SupplierDocumentRequirement::class, 'requirement_id');
    }
}
