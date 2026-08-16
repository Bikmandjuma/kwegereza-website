<?php

namespace App\Console\Commands;

use App\Services\GroupChatService;
use Illuminate\Console\Command;

class CleanupGroupChatRetention extends Command
{
    protected $signature = 'group-chat:cleanup-retention';

    protected $description = 'Soft-delete old group chat messages and purge long-soft-deleted ones, per config/group_chat.php retention windows.';

    public function handle(GroupChatService $groupChat): int
    {
        $result = $groupChat->runRetentionCleanup();

        $this->info("Soft-deleted: {$result['soft_deleted']} · Purged: {$result['purged']}");

        return self::SUCCESS;
    }
}
