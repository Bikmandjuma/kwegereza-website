<?php

namespace Database\Seeders;

use App\Models\Owner;
use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        Owner::truncate();

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
            'email' => 'islamailemunyawera@gmail.com',
            'role' => 'AssistantAdmin',
            'title' => 'Admin',
            'image' => 'user.png',
            'dob' => '2000-15-09',
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