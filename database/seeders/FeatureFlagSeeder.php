<?php

namespace Database\Seeders;

use App\Models\FeatureFlag;
use Illuminate\Database\Seeder;

class FeatureFlagSeeder extends Seeder
{
    public function run(): void
    {
        $flags = [
            ['key' => 'guest_chat', 'label' => 'Guest FAQ Chat', 'description' => "Floating chat bubble on public pages."],
            ['key' => 'live_chat', 'label' => 'Twandikire (Leader Chat)', 'description' => "Guest-to-leader messaging."],
            ['key' => 'registration', 'label' => 'Student Registration', 'description' => "Allow new students to sign up."],
            ['key' => 'public_books', 'label' => 'Public Books', 'description' => "Show the Ibitabo page to guests."],
            ['key' => 'comments', 'label' => 'Comments', 'description' => "Not built yet reserved for when comments ship."],
            ['key' => 'video_classes', 'label' => 'Video Classes', 'description' => "Not built yet reserved, blocked on a hosting provider decision."],
            ['key' => 'audio_classes', 'label' => 'Audio Classes (beyond Darsat)', 'description' => "Not built yet reserved."],
            ['key' => 'ai_assistant', 'label' => 'AI Assistant', 'description' => "Not built yet the guest chat is keyword-matching, not AI, today."],
        ];

        foreach ($flags as $flag) {
            FeatureFlag::firstOrCreate(['key' => $flag['key']], $flag + ['is_enabled' => true]);
        }
    }
}
