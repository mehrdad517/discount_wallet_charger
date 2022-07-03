<?php

namespace Database\Factories;

use App\Models\Discount;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Finance>
 */
class FinanceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {

        return [
            'user_id' => User::factory(),
            'creditor' => $this->faker->numberBetween(100, 1000),
            'debtor' => 0,
            'type' => 'credit',
            'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'financeable_id' => Discount::factory(),
            'financeable_type' => 'discount'

        ];
    }
}
