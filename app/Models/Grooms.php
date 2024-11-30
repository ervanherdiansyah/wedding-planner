<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Grooms extends Model
{
    use HasFactory;
    protected $guarded = [
        'id'
    ];

    public function project()
    {
        return $this->belongsTo(Projects::class, 'project_id');
    }
    public function familyMemberGroom()
    {
        return $this->hasMany(FamilyMemberGrooms::class, 'groom_id');
    }
}
