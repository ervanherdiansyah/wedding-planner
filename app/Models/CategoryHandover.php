<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CategoryHandover extends Model
{
    use HasFactory;
    protected $table = 'category_handovers';
    protected $guarded = [
        'id'
    ];
    public function HandoverBudget()
    {
        return $this->belongsTo(HandoverBudget::class, 'handover_budgets_id');
    }
    public function HandoverBudgetItem()
    {
        return $this->hasMany(HandoverBudgetItem::class, 'category_handover_budgets_id');
    }
}
