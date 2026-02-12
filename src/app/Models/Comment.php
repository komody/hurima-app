<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    protected $table = 'comments';

    protected $fillable = [
      'content',
      'user_id',
      'item_id',
    ];

    public function user()
    {
      return $this->belongsTo(User::class, 'user_id');
    }
    public function item()
    {
      return $this->belongsTo(Item::class, 'item_id');
    }
}