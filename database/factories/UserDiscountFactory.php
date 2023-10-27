<?php

namespace Database\Factories;
use Faker\Generator as Faker;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\UserDiscount;
class UserDiscountFactory extends Factory
{
    protected $model = UserDiscount::class;

    public function definition()
    {
        return [
            'store_id' => $this->faker->numberBetween(1, 10), // Adjust the store IDs
            'user_id' => $this->faker->numberBetween(1, 100), // Adjust the user IDs
            'discount_id' => $this->faker->numberBetween(1, 5), // Adjust the discount IDs
            'total_payment' => $this->faker->randomFloat(2, 10, 100),
            'after_discount' => $this->faker->randomFloat(2, 5, 90),
            'date' => $this->faker->dateTimeBetween('-1 year', 'now'),
        ];
    }
}
