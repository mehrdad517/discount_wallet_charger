<?php

namespace Database\Factories;

use App\Models\Discount;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Discount>
 */
class DiscountFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'id' => $this->faker->randomNumber(),
            'discount_code' => $this->faker->text(10),
            'total_count' => 1000,
            'usage_count' => 0,
            'type' => Discount::DISCOUNT_FINANCE_CHARGER
        ];
    }
}
