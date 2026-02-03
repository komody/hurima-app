<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $table = 'table_products';

    protected $fillable = [
        'name',
        'price',
        'brand_name',
        'description',
        'image_url',
        'condition_id',
        'seller_id',
        'buyer_id',
        'delivery_address',
        'likes_count',
        'comments_count',
        'sold_out',
    ];

    protected $casts = [
        'sold_out' => 'boolean',
        'price' => 'integer',
        'likes_count' => 'integer',
        'comments_count' => 'integer',
    ];

    /**
     * コンディションとのリレーション
     */
    public function condition()
    {
        return $this->belongsTo(Condition::class, 'condition_id');
    }

    /**
     * 売り手とのリレーション
     */
    public function seller()
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    /**
     * 買い手とのリレーション
     */
    public function buyer()
    {
        return $this->belongsTo(User::class, 'buyer_id');
    }
}
