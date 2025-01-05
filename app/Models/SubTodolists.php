<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubTodolists extends Model
{
    use HasFactory;
    protected $guarded = [
        'id'
    ];
    protected $casts = [
        'todolist_id' => 'integer',
    ];
    public function todolist()
    {
        return $this->belongsTo(Todolists::class, 'todolist_id');
    }
}
