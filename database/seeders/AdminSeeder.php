<?php

namespace Database\Seeders;

use App\Models\Admin;
use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        Admin::truncate();
        $admin = [
            'email' => 'admin@gmail.com',
            'password' => bcrypt('admin')
        ];
        Admin::create($admin);
    }
}
