<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Todolists extends Model
{
    use HasFactory;
    protected $guarded = [
        'id'
    ];
    public function categoryTodolist()
    {
        return $this->belongsTo(CategoryTodolists::class, 'category_todolist_id');
    }
    public function subTodolist()
    {
        return $this->hasMany(SubTodolists::class, 'todolist_id');
    }
}
