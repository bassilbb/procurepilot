<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EvaluationCriterion extends Model
{
    use HasFactory;

    protected $fillable = ['tender_id', 'name', 'description', 'weight', 'max_score'];

    public function tender()
    {
        return $this->belongsTo(Tender::class);
    }

    public function scores()
    {
        return $this->hasMany(BidScore::class, 'criterion_id');
    }
}
