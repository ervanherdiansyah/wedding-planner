<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    use HasFactory;
    protected $guarded = ['id'];

    public function menus()
    {
        return $this->belongsToMany(Menu::class, 'menu_packages')
            ->withPivot('package_id')
            ->withTimestamps();
    }

    public function packages()
    {
        return $this->belongsToMany(Package::class, 'menu_packages')
            ->withPivot('menu_id')
            ->withTimestamps();
    }
}
