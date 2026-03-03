<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $faker = \Faker\Factory::create('vi_VN');
        User::truncate();
        User::create([
            'username' => 'user',
            'email' => 'user@gmail.com',
            'password' => bcrypt('123456'),
            'phone' => preg_replace('/[\+\(\)\-\s]/', '', $faker->unique()->phoneNumber()),
            'gender' => rand(1,2),
            'address' => $faker->address
        ]);
//        for ($i = 0; $i < 1500; $i++) {
//            User::create([
//                'username' => $faker->name,
//                'email' => $faker->unique()->email,
//                'password' => bcrypt('123456'),
//                'phone' => preg_replace('/[\+\(\)\-\s]/', '', $faker->unique()->phoneNumber()),
//                'birthday' => $faker->dateTimeBetween('1990-01-01', '2012-12-31')->format('Y-m-d'),
//                'gender' => rand(1,2),
//                'address' => $faker->address
//            ]);
//        }
    }
}
