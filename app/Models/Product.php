<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    public $fillable = [
        'name',
        'description',
        'sku',
        'price',
        'image',
        'stock',
        'category_id',
    ];


    /*
    * Un producto le pertenece una gategoria 
    */
    public function category()
    {
    	 return $this->belongsTo(Category::class);
    }
}
