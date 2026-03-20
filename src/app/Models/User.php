<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable;


    protected $fillable = [
        'name',
        'email',
        'password',
        'profile_image',
        'postal_code',
        'address',
        'building_name',
    ];


    protected $hidden = [
        'password',
        'remember_token',
    ];


    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /*
    |--------------------------------------------------------------------------
    | リレーション
    |--------------------------------------------------------------------------
    */
    // 出品した商品
    public function items()
    {
        return $this->hasMany(Item::class);
    }

    // いいね
    public function likes()
    {
        return $this->hasMany(Like::class);
    }
    // いいねした商品
    public function likedItems()
    {
        return $this->belongsToMany(Item::class, 'likes')
            ->withTimestamps();
    }

    // コメント
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    // 購入履歴
    public function purchases()
    {
        return $this->hasMany(Purchase::class);
    }

    /*
    |--------------------------------------------------------------------------
    | アクセサ
    |--------------------------------------------------------------------------
    */
    public function getProfileImageUrlAttribute()
    {
        if (!$this->profile_image) {
            return null;
        }

        if (str_starts_with($this->profile_image, 'images/')) {
            return asset($this->profile_image);
        }

        return Storage::url($this->profile_image);
    }

}
