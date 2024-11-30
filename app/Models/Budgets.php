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
    public function project()
    {
        return $this->belongsTo(Projects::class, 'project_id');
    }
    public function categoryBudget()
    {
        return $this->hasMany(CategoryBudgets::class, 'budget_id');
    }
}
