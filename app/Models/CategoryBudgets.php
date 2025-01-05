<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CategoryBudgets extends Model
{
    use HasFactory;
    protected $guarded = [
        'id'
    ];
    protected $casts = [
        'budget_id' => 'integer',
    ];
    public function budget()
    {
        return $this->belongsTo(Budgets::class, 'budget_id');
    }
    public function listBudget()
    {
        return $this->hasMany(ListBudgets::class, 'category_budget_id');
    }
}
