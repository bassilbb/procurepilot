<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContractDocument extends Model
{
    use HasFactory;

    protected $fillable = ['contract_id', 'name', 'path', 'mime', 'size'];

    public function contract()
    {
        return $this->belongsTo(Contract::class);
    }
}
