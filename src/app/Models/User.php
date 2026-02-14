<?php

namespace App\Models;

use App\Notifications\VerifyEmail as VerifyEmailNotification;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'email_verified_at',
        'first_login_email_verified_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'first_login_email_verified_at' => 'datetime',
    ];

    /**
     * Send the email verification notification.
     *
     * @return void
     */
    public function sendEmailVerificationNotification()
    {
        $this->notify(new VerifyEmailNotification);
    }

    public function account()
    {
        return $this->hasOne(Account::class);
    }

    /**
     * いいねとのリレーション
     */
    public function likes()
    {
        return $this->hasMany(Like::class, 'user_id');
    }

    /**
     * 出品した商品とのリレーション（売却済み含む）
     */
    public function soldItems()
    {
        return $this->hasMany(Item::class, 'seller_id');
    }

    /**
     * 購入した商品とのリレーション
     */
    public function purchasedItems()
    {
        return $this->hasMany(Item::class, 'buyer_id');
    }
}
