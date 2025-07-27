<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Package extends Model
{
    use HasFactory;
    protected $guarded = ['id'];
    public function menus()
    {
        return $this->belongsToMany(Menu::class, 'menu_packages', 'package_id', 'menu_id')
            ->withTimestamps();
    }
    protected $casts = [
        'price' => 'integer',
    ];
    public function user()
    {
        return $this->hasOne(User::class, 'package');
    }
    public function detailPackage()
    {
        return $this->hasMany(DetailPackages::class, 'package_id');
    }
}
