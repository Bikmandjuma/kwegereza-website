<?php

use App\Models\Owner;
use Illuminate\Support\Facades\Broadcast;

/**
 * Private, per-owner channel — only the authenticated owner (via Sanctum,
 * per BroadcastServiceProvider) whose ID matches the channel name can
 * subscribe. This is the same RBAC-adjacent boundary as the polling
 * NotificationController from the Notifications phase: an owner only ever
 * sees their own inbox, never anyone else's.
 */
Broadcast::channel('owner.{ownerId}.notifications', function (Owner $owner, int $ownerId) {
    return $owner->id === $ownerId;
});

/**
 * Group chat channels (Student/Leader Groups phase). Two separate
 * authorization paths because owners and students authenticate
 * differently in this app: owners via Sanctum tokens (BroadcastServiceProvider
 * registers /api/broadcasting/auth with auth:sanctum), students via the
 * existing session-based 'student' guard — students don't have API tokens
 * yet (a gap flagged back in the Books phase), so a second auth route
 * using their real session guard was added specifically for this
 * (see routes/web.php: POST /student/broadcasting/auth).
 */
Broadcast::channel('group.leaders', function (Owner $owner) {
    return $owner->hasPermission('group_chat.leaders');
});

Broadcast::channel('group.male-students', function (\App\Models\User $user) {
    return strtolower((string) $user->gender) === 'male';
}, ['guards' => ['student']]);

Broadcast::channel('group.female-students', function (\App\Models\User $user) {
    return strtolower((string) $user->gender) === 'female';
}, ['guards' => ['student']]);

/**
 * Online Presence phase: who's allowed to watch students coming online in
 * real time — reuses students.view, the same permission that already
 * gates the Student Management feature (Phase 5), rather than inventing a
 * new one for what's really the same underlying concern.
 */
Broadcast::channel('online-students', function (Owner $owner) {
    return $owner->hasPermission('students.view');
});

/**
 * Live Classroom channel — a participant can be either an authenticated
 * owner (host/leader, via Sanctum) or an authenticated student (via
 * session guard), so this can't be a single typed-model callback the way
 * every other channel in this file is. ['guards' => ['sanctum', 'student']]
 * tries both in order; whichever one actually resolved a user on this
 * specific request (only one ever will, depending on which broadcasting
 * auth route handled it) is what's injected here, typed against the
 * common Authenticatable base both Owner and User share.
 */
Broadcast::channel('live-class.{classId}', function (\Illuminate\Foundation\Auth\User $user, int $classId) {
    $type = $user instanceof Owner ? 'owner' : 'student';

    return \App\Models\LiveClassParticipant::where('live_class_id', $classId)
        ->where('participant_type', $type)
        ->where('participant_id', $user->id)
        ->whereNull('left_at')
        ->exists();
}, ['guards' => ['sanctum', 'student']]);
