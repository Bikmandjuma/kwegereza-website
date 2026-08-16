<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

/**
 * Sample student accounts for testing the student-facing side of the
 * platform (dashboard, quizzes, badges, certificates, events, comments,
 * 2FA, support tickets, etc.).
 *
 * Deliberately uses firstOrCreate rather than truncate() — unlike
 * AdminSeeder, which truncates the (small, admin-only) owners table on
 * every run, this seeder must NOT wipe the users table: by the time
 * anyone runs this, the site may already have real registered students,
 * and destroying their accounts to insert test data would be a serious,
 * unrecoverable mistake. Running this seeder twice is safe — it will
 * just skip any student whose email already exists.
 */
class StudentSeeder extends Seeder
{
    public function run(): void
    {
        $testPassword = bcrypt('student123');

        $students = [
            [
                'firstname' => 'Aisha',
                'lastname'  => 'Uwimana',
                'gender'    => 'female',
                'phone'     => '0788123001',
                'email'     => 'aisha.uwimana@example.com',
                'birthdate' => '2001-03-14',
            ],
            [
                'firstname' => 'Abdul',
                'lastname'  => 'Nshimiyimana',
                'gender'    => 'male',
                'phone'     => '0788123002',
                'email'     => 'abdul.nshimiyimana@example.com',
                'birthdate' => '1999-07-22',
            ],
            [
                'firstname' => 'Fatima',
                'lastname'  => 'Mukamana',
                'gender'    => 'female',
                'phone'     => '0788123003',
                'email'     => 'fatima.mukamana@example.com',
                'birthdate' => '2002-11-05',
            ],
            [
                'firstname' => 'Ibrahim',
                'lastname'  => 'Habimana',
                'gender'    => 'male',
                'phone'     => '0788123004',
                'email'     => 'ibrahim.habimana@example.com',
                'birthdate' => '2000-01-30',
            ],
            [
                'firstname' => 'Khadija',
                'lastname'  => 'Ingabire',
                'gender'    => 'female',
                'phone'     => '0788123005',
                'email'     => 'khadija.ingabire@example.com',
                'birthdate' => '2003-05-18',
            ],
            [
                'firstname' => 'Yusuf',
                'lastname'  => 'Niyonzima',
                'gender'    => 'male',
                'phone'     => '0788123006',
                'email'     => 'yusuf.niyonzima@example.com',
                'birthdate' => '1998-09-09',
            ],
            [
                'firstname' => 'Mariam',
                'lastname'  => 'Uwase',
                'gender'    => 'female',
                'phone'     => '0788123007',
                'email'     => 'mariam.uwase@example.com',
                'birthdate' => '2001-12-25',
            ],
            [
                'firstname' => 'Hamza',
                'lastname'  => 'Mugisha',
                'gender'    => 'male',
                'phone'     => '0788123008',
                'email'     => 'hamza.mugisha@example.com',
                'birthdate' => '1997-04-11',
            ],
        ];

        foreach ($students as $student) {
            User::firstOrCreate(
                ['email' => $student['email']],
                [
                    'firstname'         => $student['firstname'],
                    'lastname'          => $student['lastname'],
                    'gender'            => $student['gender'],
                    'phone'             => $student['phone'],
                    'birthdate'         => $student['birthdate'],
                    'password'          => $testPassword,
                    'email_verified_at' => now(),
                    'profile_visible'   => true,
                ]
            );
        }
    }
}
