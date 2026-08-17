<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            AdminSeeder::class,
            RolePermissionSeeder::class,
            AmatangazoSeeder::class,
            InyandikoSeeder::class,
            ChatQuestionSeeder::class,
            BadgeSeeder::class,
            FeatureFlagSeeder::class,
            StudentSeeder::class,
        ]);
    }
}