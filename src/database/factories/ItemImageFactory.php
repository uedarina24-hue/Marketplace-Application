<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Item;

class ItemImageFactory extends Factory
{
    protected $model = \App\Models\ItemImage::class;

    public function definition()
    {
        return [
            'item_id' => Item::factory(),
            'image_path' => 'items/' . $this->faker->image('storage/app/public/items', 640, 480, null, false),
        ];
    }
}
