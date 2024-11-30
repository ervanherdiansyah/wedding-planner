<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Projects extends Model
{
    use HasFactory;
    protected $guarded = [
        'id'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function invitations()
    {
        return $this->hasMany(ProjectMemberships::class, 'project_id');
    }
    public function brideGroom()
    {
        return $this->hasMany(BrideGrooms::class, 'project_id');
    }
    public function event()
    {
        return $this->hasMany(Events::class, 'project_id');
    }
    public function categoryTodolist()
    {
        return $this->hasMany(CategoryTodolists::class, 'project_id');
    }
    public function budget()
    {
        return $this->hasMany(Budgets::class, 'project_id');
    }
}
