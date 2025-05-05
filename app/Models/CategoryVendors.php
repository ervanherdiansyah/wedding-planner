<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CategoryVendors extends Model
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
    public function listVendor()
    {
        return $this->hasMany(ListVendors::class, 'category_vendor_id');
    }
}
