<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;

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
        // Laravel標準の画像保存
        $path = $imageFile->store('items', 'public');

        return $item->images()->create([
            'image_path' => $path,
        ]);
    }
}
