<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetailPaymentBudget extends Model
{
    use HasFactory;
    protected $guarded = [
        'id'
    ];
    protected $casts = [
        'list_budgets_id' => 'integer',
    ];
    public function listBudget()
    {
        return $this->belongsTo(ListBudgets::class, 'list_budgets_id');
    }
}
