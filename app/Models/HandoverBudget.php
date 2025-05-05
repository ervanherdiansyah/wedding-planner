<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HandoverBudget extends Model
{
    use HasFactory;
    protected $table = 'handover_budgets';
    protected $guarded = [
        'id'
    ];
    protected $casts = [
        'project_id' => 'integer',
        'male_budget' => 'integer',
        'female_budget' => 'integer',
        'used_budget_male' => 'integer',
        'used_budget_female' => 'integer',
    ];
    public function project()
    {
        return $this->belongsTo(Projects::class, 'project_id');
    }
    public function categoryHandover()
    {
        return $this->hasMany(CategoryHandover::class, 'handover_budgets_id');
    }
    // Hitung sisa budget pria
    public function getRemainingMaleBudgetAttribute()
    {
        return $this->male_budget - $this->used_budget_male;
    }

    // Hitung sisa budget wanita
    public function getRemainingFemaleBudgetAttribute()
    {
        return $this->female_budget - $this->used_budget_female;
    }

    // Menyertakan computed attributes ke dalam response JSON
    protected $appends = ['remaining_male_budget', 'remaining_female_budget'];
}
