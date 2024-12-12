<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UniformCategories extends Model
{
    use HasFactory;
    protected $guarded = [
        'id'
    ];

    public function project()
    {
        return $this->belongsTo(Projects::class, 'project_id');
    }
    public function uniform()
    {
        return $this->hasMany(Uniform::class, 'uniform_category_id');
    }
}
