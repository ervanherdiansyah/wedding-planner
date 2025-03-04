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
    ];
    public function HandoverBudget()
    {
        return $this->belongsTo(HandoverBudget::class, 'handover_budgets_id');
    }
}
