<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Item;
use App\Models\User;

class LikeFactory extends Factory
{
    protected $model = \App\Models\Like::class;

    public function definition()
    {
        return [
            'user_id' => User::factory(),
            'item_id' => Item::factory(),
        ];
    }
}