<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BidItem extends Model
{
    use HasFactory;

    protected $fillable = ['bid_id', 'description', 'quantity', 'unit', 'unit_price', 'total_price'];

    public function bid()
    {
        return $this->belongsTo(Bid::class);
    }
}
