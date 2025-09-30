<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MenuPackage extends Model
{
    protected $guarded = ['id'];

    public function menu()
    {
        return $this->belongsTo(Menu::class);
    }

    public function permission()
    {
        return $this->belongsTo(Permission::class);
    }

    public function package()
    {
        return $this->belongsTo(Package::class);
    }
}
