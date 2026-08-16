<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ApiAuthController;
use App\Http\Controllers\Api\UserController;
use App\Models\Payment;
use Carbon\Carbon;
use App\Http\Controllers\TestEmailController;

Route::get('/send-test-email', [TestEmailController::class, 'sendTest']);


Route::post('/login', [ApiAuthController::class, 'login']);
Route::post('/logout', [ApiAuthController::class, 'logout']);

Route::post('/user/initial_registration', [UserController::class, 'register']);
Route::post('/user/verify/code_to_register/{email}', [UserController::class, 'verify_code_to_register']);
Route::post('/user/fill_missed_info/{email}', [UserController::class, 'fill_missed_info']);

Route::get('/getVisitCount', [UserController::class, 'getVisitCount']);
Route::post('/incrementVisitCount', [UserController::class, 'incrementVisitCount'])->name('increment.visit.count');
Route::get('/visit/Count/total', [UserController::class, 'getTotalVisits']);

Route::post('/user/forgot-password',[UserController::class,'submit_forgot_password']);

Route::post('/code_to_reset_pswd/{email}',[UserController::class,'code_to_reset_pswd']);

Route::post('/reset/password/{email}/{code}',[UserController::class,'resetPassword']);

//User/Seeker routes
Route::group(['prefix'=>'user' , 'middleware'=>'userAuth'],function(){
    Route::get('/dashboard', [UserController::class, 'dashboard']);
    Route::get('/profile', [UserController::class, 'profile_picture']);
    Route::get('/view_info', [UserController::class, 'View_information']);
    Route::post('/update_info', [UserController::class, 'edit_info']);
    Route::post('/submit_job_category', [UserController::class, 'submitCategories']);
    Route::get('/fetch_user_job_categories', [UserController::class, 'fetch_user_job_Categories']);
    Route::get('/UserCount_job_category', [UserController::class, 'count_job_category']);
    Route::delete('/remove_job_category/{id}', [UserController::class, 'remove_job_category']);
    Route::post('/modify_password', [UserController::class, 'modify_password']);
    // Route::post('/payment-callback', [PaymentController::class, 'handleCallback']);
    
    Route::get('/checkUserAccess',[UserController::class,'checkUserAccess']);
});

    Route::post('/guest/request_advertisment',[UserController::class,'request_advertisment']);
    Route::post('/guest/contact_us',[UserController::class,'contact_us']);

Route::get('/mifotra-proxy', function () {
    $json = file_get_contents('https://recruitment.mifotra.gov.rw/api/recruitment/open-advertisements');
    return response($json)->header('Content-Type', 'application/json');
});

/*
|--------------------------------------------------------------------------
| KWEGEREZA ISLAMIC E-LEARNING API (React frontend)
|--------------------------------------------------------------------------
| Deliberately isolated under its own prefix, its own auth controller, and
| its own guard-agnostic permission middleware ('permission.api') so nothing
| here can accidentally touch the legacy job-seeker JWT API above. Uses
| Sanctum tokens against the existing Owner model/RBAC — no new tables,
| no duplicated business logic (CourseController delegates to the same
| CourseService the web admin panel uses).
*/
Route::prefix('owner')->group(function () {
    /**
     * Security phase: this login endpoint had NO rate limiting at all —
     * confirmed by checking every layer (route definition, controller,
     * global middleware) rather than assuming Laravel's defaults covered
     * it. Unlimited login attempts from a single IP were possible,
     * meaning an unthrottled brute-force attack against any known owner
     * email was fully viable. 5 attempts/minute per IP is deliberately
     * tight for a login endpoint specifically (contrast with the much
     * more permissive throttle:100,1 already on the Blade owner web
     * routes, which covers general usage, not a single high-value,
     * credential-guessing-prone endpoint).
     */
    Route::post('/auth/login', [\App\Http\Controllers\Api\Owner\AuthController::class, 'login'])
        ->middleware('throttle:5,1');

    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/auth/me', [\App\Http\Controllers\Api\Owner\AuthController::class, 'me']);
        Route::post('/auth/logout', [\App\Http\Controllers\Api\Owner\AuthController::class, 'logout']);

        Route::get('/courses', [\App\Http\Controllers\Api\Owner\CourseController::class, 'index'])
            ->middleware('permission.api:courses.view');
        Route::get('/courses/{id}', [\App\Http\Controllers\Api\Owner\CourseController::class, 'show'])
            ->middleware('permission.api:courses.view')->where('id', '[0-9]+');
        Route::post('/courses', [\App\Http\Controllers\Api\Owner\CourseController::class, 'store'])
            ->middleware('permission.api:courses.create');
        Route::put('/courses/{id}', [\App\Http\Controllers\Api\Owner\CourseController::class, 'update'])
            ->middleware('permission.api:courses.update')->where('id', '[0-9]+');
        Route::delete('/courses/{id}', [\App\Http\Controllers\Api\Owner\CourseController::class, 'destroy'])
            ->middleware('permission.api:courses.delete')->where('id', '[0-9]+');

        Route::get('/darsat', [\App\Http\Controllers\Api\Owner\DarsatController::class, 'index'])
            ->middleware('permission.api:darsat.view');
        Route::get('/darsat/{id}', [\App\Http\Controllers\Api\Owner\DarsatController::class, 'show'])
            ->middleware('permission.api:darsat.view')->where('id', '[0-9]+');
        Route::post('/darsat', [\App\Http\Controllers\Api\Owner\DarsatController::class, 'store'])
            ->middleware('permission.api:darsat.create');
        Route::put('/darsat/{id}', [\App\Http\Controllers\Api\Owner\DarsatController::class, 'update'])
            ->middleware('permission.api:darsat.update')->where('id', '[0-9]+');
        Route::delete('/darsat/{id}', [\App\Http\Controllers\Api\Owner\DarsatController::class, 'destroy'])
            ->middleware('permission.api:darsat.delete')->where('id', '[0-9]+');

        // Read-only support endpoint for the Darsat form's teacher picker —
        // gated on darsat.view since it's only ever consumed alongside the
        // Darsat create/edit screens, not a standalone user-management view.
        Route::get('/teachers', [\App\Http\Controllers\Api\Owner\TeacherOptionsController::class, 'index'])
            ->middleware('permission.api:darsat.view');

        Route::get('/students', [\App\Http\Controllers\Api\Owner\StudentController::class, 'index'])
            ->middleware('permission.api:students.view');
        Route::get('/students/{id}', [\App\Http\Controllers\Api\Owner\StudentController::class, 'show'])
            ->middleware('permission.api:students.view')->where('id', '[0-9]+');
        Route::patch('/students/{id}/block', [\App\Http\Controllers\Api\Owner\StudentController::class, 'block'])
            ->middleware('permission.api:students.manage')->where('id', '[0-9]+');
        Route::patch('/students/{id}/unblock', [\App\Http\Controllers\Api\Owner\StudentController::class, 'unblock'])
            ->middleware('permission.api:students.manage')->where('id', '[0-9]+');
        Route::get('/students-online', [\App\Http\Controllers\Api\Owner\StudentController::class, 'online'])
            ->middleware('permission.api:students.view');

        Route::get('/analytics/overview', [\App\Http\Controllers\Api\Owner\AnalyticsController::class, 'overview'])
            ->middleware('permission.api:analytics.view');

        Route::get('/books', [\App\Http\Controllers\Api\Owner\BookController::class, 'index'])
            ->middleware('permission.api:books.view');
        Route::get('/books/{id}', [\App\Http\Controllers\Api\Owner\BookController::class, 'show'])
            ->middleware('permission.api:books.view')->where('id', '[0-9]+');
        Route::post('/books', [\App\Http\Controllers\Api\Owner\BookController::class, 'store'])
            ->middleware('permission.api:books.create');
        Route::put('/books/{id}', [\App\Http\Controllers\Api\Owner\BookController::class, 'update'])
            ->middleware('permission.api:books.update')->where('id', '[0-9]+');
        Route::delete('/books/{id}', [\App\Http\Controllers\Api\Owner\BookController::class, 'destroy'])
            ->middleware('permission.api:books.delete')->where('id', '[0-9]+');

        Route::get('/amatangazo', [\App\Http\Controllers\Api\Owner\AnnouncementController::class, 'index'])
            ->middleware('permission.api:amatangazo.view');
        Route::get('/amatangazo/{id}', [\App\Http\Controllers\Api\Owner\AnnouncementController::class, 'show'])
            ->middleware('permission.api:amatangazo.view')->where('id', '[0-9]+');
        Route::post('/amatangazo', [\App\Http\Controllers\Api\Owner\AnnouncementController::class, 'store'])
            ->middleware('permission.api:amatangazo.create');
        Route::put('/amatangazo/{id}', [\App\Http\Controllers\Api\Owner\AnnouncementController::class, 'update'])
            ->middleware('permission.api:amatangazo.update')->where('id', '[0-9]+');
        Route::delete('/amatangazo/{id}', [\App\Http\Controllers\Api\Owner\AnnouncementController::class, 'destroy'])
            ->middleware('permission.api:amatangazo.delete')->where('id', '[0-9]+');
        Route::patch('/amatangazo/{id}/toggle', [\App\Http\Controllers\Api\Owner\AnnouncementController::class, 'togglePublish'])
            ->middleware('permission.api:amatangazo.update')->where('id', '[0-9]+');

        Route::get('/quizzes', [\App\Http\Controllers\Api\Owner\QuizController::class, 'index'])
            ->middleware('permission.api:quizzes.view');
        Route::get('/quizzes/{id}', [\App\Http\Controllers\Api\Owner\QuizController::class, 'show'])
            ->middleware('permission.api:quizzes.view')->where('id', '[0-9]+');
        Route::post('/quizzes', [\App\Http\Controllers\Api\Owner\QuizController::class, 'store'])
            ->middleware('permission.api:quizzes.create');
        Route::put('/quizzes/{id}', [\App\Http\Controllers\Api\Owner\QuizController::class, 'update'])
            ->middleware('permission.api:quizzes.update')->where('id', '[0-9]+');
        Route::delete('/quizzes/{id}', [\App\Http\Controllers\Api\Owner\QuizController::class, 'destroy'])
            ->middleware('permission.api:quizzes.delete')->where('id', '[0-9]+');

        Route::post('/quizzes/{quiz}/questions', [\App\Http\Controllers\Api\Owner\QuizController::class, 'storeQuestion'])
            ->middleware('permission.api:quizzes.create')->where('quiz', '[0-9]+');
        Route::put('/quizzes/questions/{question}', [\App\Http\Controllers\Api\Owner\QuizController::class, 'updateQuestion'])
            ->middleware('permission.api:quizzes.update')->where('question', '[0-9]+');
        Route::delete('/quizzes/questions/{question}', [\App\Http\Controllers\Api\Owner\QuizController::class, 'destroyQuestion'])
            ->middleware('permission.api:quizzes.delete')->where('question', '[0-9]+');

        Route::get('/notifications', [\App\Http\Controllers\Api\Owner\NotificationController::class, 'index']);
        Route::get('/notifications/unread-count', [\App\Http\Controllers\Api\Owner\NotificationController::class, 'unreadCount']);
        Route::post('/notifications/{id}/read', [\App\Http\Controllers\Api\Owner\NotificationController::class, 'markRead']);
        Route::post('/notifications/read-all', [\App\Http\Controllers\Api\Owner\NotificationController::class, 'markAllRead']);

        Route::get('/chat/conversations', [\App\Http\Controllers\Api\Owner\ChatController::class, 'conversations'])
            ->middleware('permission.api:chat.view');
        Route::get('/chat/messages/{guestId}', [\App\Http\Controllers\Api\Owner\ChatController::class, 'messages'])
            ->middleware('permission.api:chat.view');
        Route::post('/chat/messages/{guestId}', [\App\Http\Controllers\Api\Owner\ChatController::class, 'send'])
            ->middleware('permission.api:chat.reply');
        Route::post('/chat/messages/{guestId}/read', [\App\Http\Controllers\Api\Owner\ChatController::class, 'markRead'])
            ->middleware('permission.api:chat.reply');

        Route::get('/group-chat/leaders', [\App\Http\Controllers\Api\Owner\GroupChatController::class, 'leadersMessages'])
            ->middleware('permission.api:group_chat.leaders');
        Route::post('/group-chat/leaders', [\App\Http\Controllers\Api\Owner\GroupChatController::class, 'leadersSend'])
            ->middleware('permission.api:group_chat.leaders');
        Route::post('/group-chat/messages/{message}/react', [\App\Http\Controllers\Api\Owner\GroupChatController::class, 'react'])
            ->middleware('permission.api:group_chat.leaders');
        Route::delete('/group-chat/messages/{message}', [\App\Http\Controllers\Api\Owner\GroupChatController::class, 'destroy'])
            ->middleware('permission.api:group_chat.leaders');
        Route::post('/group-chat/messages/{message}/report', [\App\Http\Controllers\Api\Owner\GroupChatController::class, 'report'])
            ->middleware('permission.api:group_chat.leaders');
        Route::post('/group-chat/messages/{message}/pin', [\App\Http\Controllers\Api\Owner\GroupChatController::class, 'pin'])
            ->middleware('permission.api:group_chat.moderate');
        Route::post('/group-chat/messages/{message}/unpin', [\App\Http\Controllers\Api\Owner\GroupChatController::class, 'unpin'])
            ->middleware('permission.api:group_chat.moderate');
        Route::post('/group-chat/{group}/mute', [\App\Http\Controllers\Api\Owner\GroupChatController::class, 'mute'])
            ->middleware('permission.api:group_chat.moderate');
        Route::post('/group-chat/{group}/unmute', [\App\Http\Controllers\Api\Owner\GroupChatController::class, 'unmute'])
            ->middleware('permission.api:group_chat.moderate');

        Route::get('/live-classes', [\App\Http\Controllers\Api\Owner\LiveClassController::class, 'index'])
            ->middleware('permission.api:live_class.manage');
        Route::get('/live-classes/{id}', [\App\Http\Controllers\Api\Owner\LiveClassController::class, 'show'])
            ->middleware('permission.api:live_class.manage')->where('id', '[0-9]+');
        Route::post('/live-classes', [\App\Http\Controllers\Api\Owner\LiveClassController::class, 'store'])
            ->middleware('permission.api:live_class.create');
        Route::post('/live-classes/{id}/start', [\App\Http\Controllers\Api\Owner\LiveClassController::class, 'start'])
            ->middleware('permission.api:live_class.manage')->where('id', '[0-9]+');
        Route::post('/live-classes/{id}/end', [\App\Http\Controllers\Api\Owner\LiveClassController::class, 'end'])
            ->middleware('permission.api:live_class.manage')->where('id', '[0-9]+');
        Route::get('/live-classes/{id}/participants', [\App\Http\Controllers\Api\Owner\LiveClassController::class, 'participants'])
            ->middleware('permission.api:live_class.manage')->where('id', '[0-9]+');
        Route::post('/live-classes/{id}/participants/{participantId}/approve-hand', [\App\Http\Controllers\Api\Owner\LiveClassController::class, 'approveHand'])
            ->middleware('permission.api:live_class.manage')->where(['id' => '[0-9]+', 'participantId' => '[0-9]+']);
        Route::post('/live-classes/{id}/participants/{participantId}/reject-hand', [\App\Http\Controllers\Api\Owner\LiveClassController::class, 'rejectHand'])
            ->middleware('permission.api:live_class.manage')->where(['id' => '[0-9]+', 'participantId' => '[0-9]+']);
        Route::post('/live-classes/{id}/participants/{participantId}/mute', [\App\Http\Controllers\Api\Owner\LiveClassController::class, 'muteParticipant'])
            ->middleware('permission.api:live_class.manage')->where(['id' => '[0-9]+', 'participantId' => '[0-9]+']);
        Route::post('/live-classes/{id}/mute-everyone', [\App\Http\Controllers\Api\Owner\LiveClassController::class, 'muteEveryone'])
            ->middleware('permission.api:live_class.manage')->where('id', '[0-9]+');
        Route::post('/live-classes/{id}/participants/{participantId}/remove', [\App\Http\Controllers\Api\Owner\LiveClassController::class, 'removeParticipant'])
            ->middleware('permission.api:live_class.manage')->where(['id' => '[0-9]+', 'participantId' => '[0-9]+']);
        Route::post('/live-classes/{id}/signal', [\App\Http\Controllers\Api\Owner\LiveClassController::class, 'signal'])
            ->middleware('permission.api:live_class.manage')->where('id', '[0-9]+');

        Route::get('/profile', [\App\Http\Controllers\Api\Owner\ProfileController::class, 'show']);
        Route::put('/profile', [\App\Http\Controllers\Api\Owner\ProfileController::class, 'update']);
        Route::post('/profile/password', [\App\Http\Controllers\Api\Owner\ProfileController::class, 'updatePassword']);
        Route::post('/profile/avatar', [\App\Http\Controllers\Api\Owner\ProfileController::class, 'updateAvatar']);

        Route::get('/staff', [\App\Http\Controllers\Api\Owner\OwnerManagementController::class, 'index'])
            ->middleware('permission.api:users.view');
        Route::get('/staff/{id}', [\App\Http\Controllers\Api\Owner\OwnerManagementController::class, 'show'])
            ->middleware('permission.api:users.view')->where('id', '[0-9]+');
        Route::post('/staff', [\App\Http\Controllers\Api\Owner\OwnerManagementController::class, 'store'])
            ->middleware('permission.api:users.create');
        Route::post('/staff/{id}', [\App\Http\Controllers\Api\Owner\OwnerManagementController::class, 'update'])
            ->middleware('permission.api:users.update')->where('id', '[0-9]+');
        Route::delete('/staff/{id}', [\App\Http\Controllers\Api\Owner\OwnerManagementController::class, 'destroy'])
            ->middleware('permission.api:users.delete')->where('id', '[0-9]+');

        Route::get('/roles', [\App\Http\Controllers\Api\Owner\RoleController::class, 'index'])
            ->middleware('permission.api:roles.view');
        Route::get('/roles/{id}', [\App\Http\Controllers\Api\Owner\RoleController::class, 'show'])
            ->middleware('permission.api:roles.view')->where('id', '[0-9]+');
        Route::post('/roles', [\App\Http\Controllers\Api\Owner\RoleController::class, 'store'])
            ->middleware('permission.api:roles.create');
        Route::put('/roles/{id}', [\App\Http\Controllers\Api\Owner\RoleController::class, 'update'])
            ->middleware('permission.api:roles.update')->where('id', '[0-9]+');
        Route::delete('/roles/{id}', [\App\Http\Controllers\Api\Owner\RoleController::class, 'destroy'])
            ->middleware('permission.api:roles.delete')->where('id', '[0-9]+');
        Route::put('/staff/{ownerId}/roles', [\App\Http\Controllers\Api\Owner\RoleController::class, 'assign'])
            ->middleware('permission.api:roles.update')->where('ownerId', '[0-9]+');

        Route::get('/permissions', [\App\Http\Controllers\Api\Owner\PermissionController::class, 'index'])
            ->middleware('permission.api:permissions.view');
        Route::post('/permissions', [\App\Http\Controllers\Api\Owner\PermissionController::class, 'store'])
            ->middleware('permission.api:permissions.create');
        Route::put('/permissions/{id}', [\App\Http\Controllers\Api\Owner\PermissionController::class, 'update'])
            ->middleware('permission.api:permissions.update')->where('id', '[0-9]+');
        Route::delete('/permissions/{id}', [\App\Http\Controllers\Api\Owner\PermissionController::class, 'destroy'])
            ->middleware('permission.api:permissions.delete')->where('id', '[0-9]+');

        Route::get('/audit-logs', [\App\Http\Controllers\Api\Owner\AuditLogController::class, 'index'])
            ->middleware('permission.api:audit_logs.view');

        Route::get('/certificates', [\App\Http\Controllers\Api\Owner\CertificateController::class, 'index'])
            ->middleware('permission.api:certificates.view');
        Route::post('/certificates', [\App\Http\Controllers\Api\Owner\CertificateController::class, 'store'])
            ->middleware('permission.api:certificates.issue');

        Route::get('/feature-flags', [\App\Http\Controllers\Api\Owner\FeatureFlagController::class, 'index'])
            ->middleware('permission.api:feature_flags.manage');
        Route::post('/feature-flags/{id}/toggle', [\App\Http\Controllers\Api\Owner\FeatureFlagController::class, 'toggle'])
            ->middleware('permission.api:feature_flags.manage')->where('id', '[0-9]+');

        Route::get('/badges', [\App\Http\Controllers\Api\Owner\BadgeController::class, 'index'])
            ->middleware('permission.api:gamification.view');
        Route::post('/badges', [\App\Http\Controllers\Api\Owner\BadgeController::class, 'store'])
            ->middleware('permission.api:gamification.manage');
        Route::put('/badges/{id}', [\App\Http\Controllers\Api\Owner\BadgeController::class, 'update'])
            ->middleware('permission.api:gamification.manage')->where('id', '[0-9]+');
        Route::delete('/badges/{id}', [\App\Http\Controllers\Api\Owner\BadgeController::class, 'destroy'])
            ->middleware('permission.api:gamification.manage')->where('id', '[0-9]+');

        Route::get('/events', [\App\Http\Controllers\Api\Owner\EventController::class, 'index'])
            ->middleware('permission.api:events.view');
        Route::post('/events', [\App\Http\Controllers\Api\Owner\EventController::class, 'store'])
            ->middleware('permission.api:events.create');
        Route::post('/events/{id}', [\App\Http\Controllers\Api\Owner\EventController::class, 'update'])
            ->middleware('permission.api:events.update')->where('id', '[0-9]+');
        Route::put('/events/{id}', [\App\Http\Controllers\Api\Owner\EventController::class, 'update'])
            ->middleware('permission.api:events.update')->where('id', '[0-9]+');
        Route::delete('/events/{id}', [\App\Http\Controllers\Api\Owner\EventController::class, 'destroy'])
            ->middleware('permission.api:events.delete')->where('id', '[0-9]+');

        Route::get('/comment-moderation', [\App\Http\Controllers\Api\Owner\CommentModerationController::class, 'index'])
            ->middleware('permission.api:comments.moderate');
        Route::post('/comment-moderation/{id}/hide', [\App\Http\Controllers\Api\Owner\CommentModerationController::class, 'hide'])
            ->middleware('permission.api:comments.moderate')->where('id', '[0-9]+');
        Route::post('/comment-moderation/{id}/approve', [\App\Http\Controllers\Api\Owner\CommentModerationController::class, 'approve'])
            ->middleware('permission.api:comments.moderate')->where('id', '[0-9]+');
        Route::delete('/comment-moderation/{id}', [\App\Http\Controllers\Api\Owner\CommentModerationController::class, 'destroy'])
            ->middleware('permission.api:comments.moderate')->where('id', '[0-9]+');

        Route::get('/teacher-verification', [\App\Http\Controllers\Api\Owner\TeacherVerificationController::class, 'index'])
            ->middleware('permission.api:teacher_verification.manage');
        Route::post('/teacher-verification/{id}/verify', [\App\Http\Controllers\Api\Owner\TeacherVerificationController::class, 'verify'])
            ->middleware('permission.api:teacher_verification.manage')->where('id', '[0-9]+');
        Route::post('/teacher-verification/{id}/unverify', [\App\Http\Controllers\Api\Owner\TeacherVerificationController::class, 'unverify'])
            ->middleware('permission.api:teacher_verification.manage')->where('id', '[0-9]+');
        Route::put('/teacher-verification/{id}/profile', [\App\Http\Controllers\Api\Owner\TeacherVerificationController::class, 'updateProfile'])
            ->middleware('permission.api:teacher_verification.manage')->where('id', '[0-9]+');

        Route::get('/backups', [\App\Http\Controllers\Api\Owner\BackupController::class, 'index'])
            ->middleware('permission.api:backups.view');
        Route::post('/backups', [\App\Http\Controllers\Api\Owner\BackupController::class, 'store'])
            ->middleware('permission.api:backups.create');
        Route::get('/backups/{filename}/download', [\App\Http\Controllers\Api\Owner\BackupController::class, 'download'])
            ->middleware('permission.api:backups.view');
        Route::delete('/backups/{filename}', [\App\Http\Controllers\Api\Owner\BackupController::class, 'destroy'])
            ->middleware('permission.api:backups.delete');

        Route::get('/system-monitor', [\App\Http\Controllers\Api\Owner\SystemMonitorController::class, 'index'])
            ->middleware('permission.api:system_monitoring.view');

        Route::get('/account-deletions', [\App\Http\Controllers\Api\Owner\AccountDeletionController::class, 'index'])
            ->middleware('permission.api:account_deletion.manage');
        Route::post('/account-deletions/{id}/approve', [\App\Http\Controllers\Api\Owner\AccountDeletionController::class, 'approve'])
            ->middleware('permission.api:account_deletion.manage')->where('id', '[0-9]+');
        Route::post('/account-deletions/{id}/reject', [\App\Http\Controllers\Api\Owner\AccountDeletionController::class, 'reject'])
            ->middleware('permission.api:account_deletion.manage')->where('id', '[0-9]+');
    });

    // Deliberately OUTSIDE the auth:sanctum block — books are publicly
    // browsable to guests (GuestController::books()), so a download must
    // work with or without a student session, not just for owners.
    Route::post('/books/{id}/download', [\App\Http\Controllers\Api\Owner\BookController::class, 'download'])
        ->where('id', '[0-9]+');
    Route::post('/books/{id}/view', [\App\Http\Controllers\Api\Owner\BookController::class, 'recordView'])
        ->where('id', '[0-9]+');
});


