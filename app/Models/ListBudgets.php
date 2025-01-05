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
    ];
    public function categoryBudget()
    {
        return $this->belongsTo(CategoryBudgets::class, 'category_budget_id');
    }
}
