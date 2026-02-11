<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Condition extends Model
{
    use HasFactory;

    protected $table = 'conditions';

    protected $fillable = [
        'name',
    ];

    /**
     * 商品とのリレーション
     */
    public function products()
    {
        return $this->hasMany(Product::class, 'condition_id');
    }
}
