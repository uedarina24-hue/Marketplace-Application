<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Item;
use App\Models\Category;

class CategoryItemFactory extends Factory
{
    protected $model = \App\Models\CategoryItem::class;

    public function definition()
    {
        return [
            'item_id' => Item::factory(),
            'category_id' => Category::factory(),
        ];
    }
}