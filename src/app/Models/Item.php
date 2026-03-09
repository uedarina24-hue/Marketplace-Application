<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;

class Item extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'brand_name',
        'description',
        'price',
        'condition',
    ];

    /*
    |--------------------------------------------------------------------------
    | リレーション
    |--------------------------------------------------------------------------
    */
    // 出品者
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // 商品画像
    public function images()
    {
        return $this->hasMany(ItemImage::class);
    }

    //商品詳細の最初の画像
    public function firstImage()
    {
        return $this->hasOne(ItemImage::class)->latestOfMany();
    }

    // いいねした商品
    public function likes()
    {
        return $this->hasMany(Like::class);
    }

    // いいねしたユーザー
    public function likedUsers()
    {
        return $this->belongsToMany(User::class, 'likes');
    }

    // コメント
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    // カテゴリ（多対多）
    public function categories()
    {
        return $this->belongsToMany(Category::class);
    }

    // 購入情報
    public function purchase()
    {
        return $this->hasOne(Purchase::class);
    }

    /*
    |--------------------------------------------------------------------------
    |アクセサ
    |--------------------------------------------------------------------------
    */
    // Sold判定
    public function getIsSoldAttribute()
    {
        return $this->purchase !== null;
    }

    /*
    |--------------------------------------------------------------------------
    | 業務ロジック
    |--------------------------------------------------------------------------
    */
    //いいね判定
    public function isLikedBy($user)
    {
        if (!$user) return false;

        return $this->likes->contains('user_id', $user->id);
    }

    //商品作成
    public static function createWithRelations(array $data, $imageFile = null)
    {
        return DB::transaction(function () use ($data, $imageFile) {

            $item = self::create([
                'user_id' => auth()->id(),
                'name' => $data['name'],
                'brand_name' => $data['brand_name'] ?? null,
                'description' => $data['description'],
                'price' => $data['price'],
                'condition' => $data['condition'],
            ]);

            if ($imageFile) {
                ItemImage::storeForItem($item, $imageFile);
            }

            if (!empty($data['categories'])) {
                $item->categories()->sync($data['categories']);
            }

            return $item;
        });
    }

}
