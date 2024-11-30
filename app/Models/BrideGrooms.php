<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BrideGrooms extends Model
{
    use HasFactory;
    protected $guarded = [
        'id'
    ];

    public function project()
    {
        return $this->belongsTo(Projects::class, 'project_id');
    }
    public function familyMembers()
    {
        return $this->hasMany(familyMembers::class, 'bride_groom_id');
    }
}
