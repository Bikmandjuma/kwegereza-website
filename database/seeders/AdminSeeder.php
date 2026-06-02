<?php

namespace Database\Seeders;

use App\Models\Owner;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Owner::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        Owner::create([
            'firstname' => 'Bikman',
            'lastname' => 'Djuma',
            'gender' => 'male',
            'phone' => '0785389000',
            'email' => 'ntiruhungwab@gmail.com',
            'role' => 'superAdmin',
            'title' => 'Admin',
            'image' => 'user.png',
            'dob' => '1994-12-20',
            'password' => bcrypt('bugarama'),
        ]);

        Owner::create([
            'firstname' => 'Munyawera',
            'lastname' => 'Ismaile',
            'gender' => 'male',
            'phone' => '+250790338841',
            'email' => 'ismailemunyawera@gmail.com',
            'role' => 'AssistantAdmin',
            'title' => 'Admin',
            'image' => 'user.png',
            'dob' => '2000-09-15',
            'password' => bcrypt('Rwanda65'),
        ]);

        Owner::create([
            'firstname' => 'Iradukunda',
            'lastname' => 'Aboubakr',
            'gender' => 'male',
            'phone' => '0785632322',
            'email' => 'aboubairadukunda@gmail.com',
            'role' => 'superSheikh',
            'title' => 'Sheikh',
            'image' => 'user.png',
            'dob' => '1998-12-20',
            'password' => bcrypt('password'),
        ]);
    }
}