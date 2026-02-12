<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Like extends Model
{
    protected $table = 'likes';

    protected $fillable = [
    'user_id',
    'item_id',
    ];

    public function user() {
    return $this->belongsTo(User::class, 'user_id');
    }
    public function item() {
    return $this->belongsTo(Item::class, 'item_id');
    }
}