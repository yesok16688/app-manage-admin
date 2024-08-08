<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
         User::factory()->create([
             'name' => 'Super Admin',
             'password' => Hash::make('appadm.qwer.123456'),
             'email' => 'admin@xxx.com',
         ]);
    }
}
