<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $table = 'products';

    protected $fillable = [
        'name',
        'price',
        'brand_name',
        'description',
        'image_url',
        'condition_id',
        'seller_id',
        'buyer_id',
        'sold_out',
    ];

    protected $casts = [
        'sold_out' => 'boolean',
        'price' => 'integer',
    ];

    public function condition()
    {
        return $this->belongsTo(Condition::class, 'condition_id');
    }

    public function seller()
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    public function buyer()
    {
        return $this->belongsTo(User::class, 'buyer_id');
    }

    public function likes()
    {
        return $this->hasMany(Like::class, 'product_id');
    }

    public function comments()
    {
        return $this->hasMany(Comment::class, 'product_id');
    }

    /**
     * カテゴリーとのリレーション（多対多）
     */
    public function categories()
    {
        return $this->belongsToMany(Category::class, 'category_product', 'product_id', 'category_id');
    }
}
