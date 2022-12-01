<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class CreateUsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert([
            'role_id' => '1',
            'name' => 'Admin',
            'email' => 'admin@gmail.com',
            'password' => bcrypt('123456'),
        ]);
        
        DB::table('users')->insert([
            'role_id' => '2',
            'name' => 'Teacher',
            'email' => 'teacher@gmail.com',
            'password' => bcrypt('123456'),
        ]);
        
        DB::table('users')->insert([
            'role_id' => '3',
            'name' => 'Student',
            'email' => 'student@gmail.com',
            'password' => bcrypt('123456'),
        ]);
    }
}
