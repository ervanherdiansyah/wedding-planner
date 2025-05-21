<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Budgets extends Model
{
    use HasFactory;
    protected $guarded = [
        'id'
    ];
    protected $casts = [
        'project_id' => 'integer',
        'estimated_payment' => 'integer',
        'actual_payment' => 'integer',
        'paid' => 'integer',
        'unpaid' => 'integer',
        'difference' => 'integer',
        'balance' => 'integer',
    ];
    public function project()
    {
        return $this->belongsTo(Projects::class, 'project_id');
    }
    public function categoryBudget()
    {
        return $this->hasMany(CategoryBudgets::class, 'budget_id');
    }
}
