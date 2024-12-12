<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SongLists extends Model
{
    use HasFactory;
    protected $guarded = [
        'id'
    ];

    public function project()
    {
        return $this->belongsTo(Projects::class, 'project_id');
    }
}
