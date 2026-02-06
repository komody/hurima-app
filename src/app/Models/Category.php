<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $table = 'table_categories';

    protected $fillable = [
        'name',
    ];

    /**
     * 商品とのリレーション（多対多）
     */
    public function products()
    {
        return $this->belongsToMany(Product::class, 'table_product_categories', 'category_id', 'product_id');
    }
}
