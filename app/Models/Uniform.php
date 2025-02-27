<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Uniform extends Model
{
    use HasFactory;
    protected $table = "uniforms";

    protected $guarded = [
        'id'
    ];
    protected $casts = [
        'uniform_category_id' => 'integer',
    ];
    public function uniformCateegory()
    {
        return $this->belongsTo(UniformCategories::class, 'uniform_category_id');
    }
}
