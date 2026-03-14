<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;

class ItemFactory extends Factory
{
    protected $model = \App\Models\Item::class;

    public function definition()
    {
        return [
            'user_id' => User::factory(),
            'name' => $this->faker->words(3, true),
            'description' => $this->faker->sentence(),
            'price' => $this->faker->numberBetween(500, 50000),
            'brand_name' => null,
            'condition' => $this->faker->randomElement(['新品', '良好', 'やや傷や汚れあり', '状態が悪い']),
        ];
    }

    public function withName(string $name)
    {
        return $this->state(fn(array $attrs) => ['name' => $name]);
    }
}