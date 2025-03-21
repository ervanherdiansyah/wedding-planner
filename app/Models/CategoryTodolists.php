<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CategoryTodolists extends Model
{
    use HasFactory;
    protected $guarded = [
        'id'
    ];
    protected $casts = [
        'project_id' => 'integer',
    ];
    public function project()
    {
        return $this->belongsTo(Projects::class, 'project_id');
    }
    public function todolist()
    {
        return $this->hasMany(Todolists::class, 'category_todolist_id');
    }
}
