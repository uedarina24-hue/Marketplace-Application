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

    //検索機能
    public function scopeSearch($query, $keyword)
    {
        if ($keyword) {
            $query->where('name','like',"%{$keyword}%");
        }
        return $query;
    }

    // 自分の商品を除外
    public function scopeExcludeOwn($query, $userId)
    {
        if ($userId) {
            $query->where('user_id','!=',$userId);
        }
        return $query;
    }

    // いいねした商品
    public function scopeLikedBy($query, $userId)
    {
        $query->whereHas('likes',function($q) use ($userId){
            $q->where('user_id',$userId);
        });
        return $query;
    }

    //いいね判定
    public function isLikedBy($user)
    {
        if (!$user) {
            return false;
        }

        return $this->likes()
            ->where('user_id', $user->id)
            ->exists();
    }

    //商品購入
    public function isPurchasableBy($user)
    {
        if ($this->purchase) {
            return false;
        }

        return true;
    }

    //マイページ
    public function scopeOwnedBy($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopePurchasedBy($query, $userId)
    {
        return $query->whereHas('purchase', function ($q) use ($userId) {
            $q->where('user_id', $userId);
        });
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
