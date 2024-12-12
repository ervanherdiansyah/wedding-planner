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
    public function groom()
    {
        return $this->hasMany(Grooms::class, 'project_id');
    }
    public function bride()
    {
        return $this->hasMany(Brides::class, 'project_id');
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
    public function eventCommite()
    {
        return $this->hasMany(EventCommittees::class, 'project_id');
    }
    public function listPhoto()
    {
        return $this->hasMany(ListPhoto::class, 'project_id');
    }
    public function songList()
    {
        return $this->hasMany(SongLists::class, 'project_id');
    }
    public function vipGuestList()
    {
        return $this->hasMany(VipGuestLists::class, 'project_id');
    }
    public function uniformCategory()
    {
        return $this->hasMany(UniformCategories::class, 'project_id');
    }
}
