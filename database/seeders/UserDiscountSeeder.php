<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\UserDiscount;

class UserDiscountSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $numberOfRecords = 20; // Adjust as needed

        // Use the factory to create fake UserDiscount records
        UserDiscount::factory($numberOfRecords)->create();
    }
}
