<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FamilyMembers extends Model
{
    use HasFactory;
    protected $guarded = [
        'id'
    ];
    public function brideGroom()
    {
        return $this->belongsTo(BrideGrooms::class, 'bride_groom_id');
    }
}
