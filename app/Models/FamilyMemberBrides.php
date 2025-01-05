<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FamilyMemberBrides extends Model
{
    use HasFactory;
    protected $guarded = [
        'id'
    ];
    protected $casts = [
        'bride_id' => 'integer',
    ];
    public function bride()
    {
        return $this->belongsTo(Brides::class, 'bride_id');
    }
}
