<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ListPhoto extends Model
{
    use HasFactory;
    protected $table = "list_photos";
    protected $guarded = [
        'id'
    ];
    protected $casts = [
        'project_id' => 'integer',
    ];
    public function project()
    {
        return $this->belongsTo(Projects::class, 'project_id');
    }
}
