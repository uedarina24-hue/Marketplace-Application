<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;
use App\Models\Item;

class PurchaseFactory extends Factory
{
    protected $model = \App\Models\Purchase::class;

    public function definition()
    {
        return [
            'user_id' => User::factory(),
            'item_id' => Item::factory(),
            'payment_method' => 'card', // カード決済のみ
            'stripe_session_id' => $this->faker->uuid(),
            'postal_code' => $this->faker->postcode(),
            'address' => $this->faker->address(),
            'building_name' => $this->faker->secondaryAddress(),
        ];
    }
}