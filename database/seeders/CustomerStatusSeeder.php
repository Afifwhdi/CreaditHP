<?php

namespace Database\Seeders;

use App\Models\Customer;
use Illuminate\Database\Seeder;

class CustomerStatusSeeder extends Seeder
{
    public function run(): void
    {
        Customer::query()->update(['status' => 'active']);
    }
}
