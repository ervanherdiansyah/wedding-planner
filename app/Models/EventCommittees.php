<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EventCommittees extends Model
{
    use HasFactory;
    protected $table = 'event_committees';
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
