<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rundowns extends Model
{
    use HasFactory;
    protected $guarded = [
        'id'
    ];
    protected $casts = [
        'project_id' => 'integer',
        'status' => 'boolean',
    ];
    public function project()
    {
        return $this->belongsTo(Projects::class, 'project_id');
    }
}
