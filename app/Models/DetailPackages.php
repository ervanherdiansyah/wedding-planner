<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetailPackages extends Model
{
    use HasFactory;
    protected $guarded = ['id'];
    public function package()
    {
        return $this->belongsTo(Package::class, 'package_id');
    }
}
