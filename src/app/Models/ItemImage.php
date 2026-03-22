<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class ItemImage extends Model
{
    use HasFactory;

    protected $fillable = [
        'item_id',
        'image_path',
    ];

    /*
    |--------------------------------------------------------------------------
    | リレーション
    |--------------------------------------------------------------------------
    */
    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    /*
    |--------------------------------------------------------------------------
    | 業務ロジック
    |--------------------------------------------------------------------------
    */
    public static function storeForItem(Item $item, UploadedFile $imageFile)
    {

        $path = $imageFile->store('items', 'public');

        return $item->images()->create([
            'image_path' => $path,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | アクセサ
    |--------------------------------------------------------------------------
    */
    public function getImageUrlAttribute()
    {
        if (!$this->image_path) {
            return null;
        }

        // storageに存在するなら最優先
        if (Storage::disk('public')->exists($this->image_path)) {
            return Storage::url($this->image_path);
        }

        // 保険：public/images
        if (str_starts_with($this->image_path, 'images/')) {
            return asset($this->image_path);
        }

        return null;
    }
}
