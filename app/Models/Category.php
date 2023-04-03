<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    public $fillable = [
        'name',
        'description'
    ];

    /* 
    * Una categoria tienen muchos productos 
    *
    */
    public function category()
    {
    	 return $this->hasMany(Product::class);
    }
}
