<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProcurementPlanItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'procurement_plan_id', 'title', 'description', 'category_id',
        'estimated_cost', 'quantity', 'method', 'priority', 'expected_date', 'status',
    ];

    protected $casts = [
        'expected_date' => 'date',
    ];

    public function plan()
    {
        return $this->belongsTo(ProcurementPlan::class, 'procurement_plan_id');
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function methodLabel(): string
    {
        return match ($this->method) {
            'open_competitive' => 'Open Competitive',
            'restricted'       => 'Restricted',
            'single_source'    => 'Single Source',
            default            => ucwords(str_replace('_', ' ', $this->method)),
        };
    }
}
