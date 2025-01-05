<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FamilyMemberGrooms extends Model
{
    use HasFactory;
    protected $guarded = [
        'id'
    ];
    protected $casts = [
        'groom_id' => 'integer',
    ];
    public function groom()
    {
        return $this->belongsTo(Grooms::class, 'groom_id');
    }
}
