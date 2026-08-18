<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $fillable = ['organization_id', 'name', 'code', 'type', 'description'];

    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    public function suppliers()
    {
        return $this->hasMany(Supplier::class);
    }

    public function tenders()
    {
        return $this->hasMany(Tender::class);
    }
}
