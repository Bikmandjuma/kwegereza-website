<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Group Chat Message Retention
    |--------------------------------------------------------------------------
    |
    | Spec §28: "Implement soft deletion (deleted_at) and scheduled cleanup...
    | Do NOT blindly permanently delete everything every month. Make
    | retention configurable." Two separate windows:
    |
    | - soft_delete_after_days: messages older than this get deleted_at set
    |   (still recoverable, just hidden from normal listing).
    | - purge_after_days: messages that have BEEN soft-deleted for at least
    |   this many additional days get permanently removed from the database.
    |
    | Set either to null to disable that stage entirely (e.g. never
    | auto-soft-delete, or soft-delete but never actually purge).
    |
    */

    'soft_delete_after_days' => env('GROUP_CHAT_SOFT_DELETE_AFTER_DAYS', 180),

    'purge_after_days' => env('GROUP_CHAT_PURGE_AFTER_DAYS', 365),

];
