<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ListBudgets extends Model
{
    use HasFactory;
    protected $guarded = [
        'id'
    ];
    protected $casts = [
        'category_budget_id' => 'integer',
        'estimated_payment' => 'integer',
        'actual_payment' => 'integer',
        'paid' => 'integer',
        'difference' => 'integer',
        'remaining_payment' => 'integer',
    ];
    public function categoryBudget()
    {
        return $this->belongsTo(CategoryBudgets::class, 'category_budget_id');
    }
    public function detailPaymentBudget()
    {
        return $this->hasMany(DetailPaymentBudget::class, 'list_budgets_id');
    }
}
