<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HandoverBudgetItem extends Model
{
    use HasFactory;
    protected $table = 'handover_budget_items';
    protected $guarded = [
        'id'
    ];
    protected $casts = [
        'handover_budgets_id' => 'integer',
        'status' => 'integer',
        'price' => 'integer',
    ];
    public function categoryHandoverBudget()
    {
        return $this->belongsTo(CategoryHandover::class, 'category_handover_budgets_id');
    }
}
