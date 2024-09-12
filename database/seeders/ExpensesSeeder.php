<?php

namespace Database\Seeders;

use App\Models\Expenses;
use Illuminate\Database\Seeder;

class ExpensesSeeder extends Seeder
{
    public function run(): void
    {
        Expenses::factory()->count(100)->create();
    }
}
