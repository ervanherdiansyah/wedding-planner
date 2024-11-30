<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ListVendors extends Model
{
    use HasFactory;
    protected $guarded = [
        'id'
    ];
    public function categoryVendor()
    {
        return $this->belongsTo(CategoryVendors::class, 'category_vendor_id');
    }
}
