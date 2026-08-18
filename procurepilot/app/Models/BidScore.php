<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BidScore extends Model
{
    use HasFactory;

    protected $fillable = ['bid_id', 'criterion_id', 'evaluator_id', 'score', 'comment'];

    public function bid()
    {
        return $this->belongsTo(Bid::class);
    }

    public function criterion()
    {
        return $this->belongsTo(EvaluationCriterion::class, 'criterion_id');
    }

    public function evaluator()
    {
        return $this->belongsTo(User::class, 'evaluator_id');
    }
}
