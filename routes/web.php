<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\WebAuthController;
use App\Http\Controllers\Web\AdminController;
use App\Http\Controllers\Web\GuestController;
use Illuminate\Support\Facades\Http;
use App\Http\Controllers\ChatController;


Route::group(['prefix'=>'owner' , 'middleware'=>'ownerAuth','throttle:100,1'],function(){
    
    Route::get('/dashboard', [AdminController::class, 'home'])->name('owner.dashboard');
    Route::get('/refresh_counts', [AdminController::class, 'refresh_counts'])->name('owner.refresh_counts');
    Route::get('/online-users', [AdminController::class, 'onlineUsersList'])->name('owner.onlineUsers');
    Route::get('/dashboard-chart-data', [AdminController::class, 'dashboardChartData'])->name('owner.dashboardChartData');
    
    Route::get('/view_info', [AdminController::class, 'View_information']);
    
    Route::get('/count_seekers', [AdminController::class, 'count_seekers']);
    
    Route::get('/count_seekers_today', [AdminController::class, 'count_seekers_today']);
    
    Route::post('/update_password', [AdminController::class, 'update_password']);
    
    Route::post('/update_info', [AdminController::class, 'edit_info']);
    
    Route::post('/logout', [WebAuthController::class, 'logout'])->name('owner.logout');
    
    Route::get('/display_paid_users', [AdminController::class, 'display_paid_users'])->name('owner.display_paid_users');

    Route::get('/view_all_users',[AdminController::class,'view_all_users'])->name('owner.view_all_users');

    Route::get('/view_all_users_joined_today',[AdminController::class,'view_all_users_joined_today'])->name('owner.view_all_users_joined_today');
    
    Route::get('/search_users_payment', [AdminController::class, 'search_users_payment'])->name('owner.search_users_payment');

    Route::get('/assign_payment_ToUser/{id}', [AdminController::class, 'assign_payment_ToUser'])->name('owner.assign_payment_ToUser');

    Route::post('/submit_payment_ToUser/{id}', [AdminController::class, 'submit_payment_ToUser'])->name('owner.submit_payment_ToUser');
    route::get('/addUser', [AdminController::class, 'AddUser'])->name('owner.addUser');
    route::get('/viewUser', [AdminController::class, 'ViewUser'])->name('owner.viewUser');
    route::get('/darsat', [AdminController::class, 'darsat'])->name('owner.darsat');
    route::get('/inyandiko_zabamenyi', [AdminController::class, 'inyandiko_zabamenyi'])->name('owner.inyandiko_zabamenyi');
    Route::post('/inyandiko_zabamenyi', [AdminController::class, 'storeInyandiko'])->name('owner.inyandiko.store');
    Route::put('/inyandiko_zabamenyi/{id}', [AdminController::class, 'updateInyandiko'])->name('owner.inyandiko.update');
    Route::delete('/inyandiko_zabamenyi/{id}', [AdminController::class, 'destroyInyandiko'])->name('owner.inyandiko.destroy');
    // Guest FAQ knowledge base + analytics
    Route::get('/faq', [\App\Http\Controllers\Web\FaqController::class, 'index'])->name('owner.faq');
    Route::post('/faq', [\App\Http\Controllers\Web\FaqController::class, 'store'])->name('owner.faq.store');
    Route::put('/faq/{id}', [\App\Http\Controllers\Web\FaqController::class, 'update'])->name('owner.faq.update');
    Route::delete('/faq/{id}', [\App\Http\Controllers\Web\FaqController::class, 'destroy'])->name('owner.faq.destroy');
    Route::get('/faq-analytics', [\App\Http\Controllers\Web\FaqController::class, 'analytics'])->name('owner.faq.analytics');

    Route::get('/audit-logs', [\App\Http\Controllers\Web\AuditLogController::class, 'index'])->name('owner.auditLogs');

    // Courses / Learning Paths
    Route::get('/courses', [\App\Http\Controllers\Web\CourseController::class, 'index'])->name('owner.courses');
    Route::post('/courses', [\App\Http\Controllers\Web\CourseController::class, 'store'])->name('owner.courses.store');
    Route::put('/courses/{id}', [\App\Http\Controllers\Web\CourseController::class, 'update'])->name('owner.courses.update');
    Route::delete('/courses/{id}', [\App\Http\Controllers\Web\CourseController::class, 'destroy'])->name('owner.courses.destroy');
    Route::get('/courses/{id}/builder', [\App\Http\Controllers\Web\CourseController::class, 'show'])->name('owner.courses.builder');
    Route::post('/courses/{course}/lessons', [\App\Http\Controllers\Web\CourseController::class, 'storeLesson'])->name('owner.courses.lessons.store');
    Route::put('/courses/lessons/{lesson}', [\App\Http\Controllers\Web\CourseController::class, 'updateLesson'])->name('owner.courses.lessons.update');
    Route::delete('/courses/lessons/{lesson}', [\App\Http\Controllers\Web\CourseController::class, 'destroyLesson'])->name('owner.courses.lessons.destroy');
    Route::post('/courses/{course}/lessons/reorder', [\App\Http\Controllers\Web\CourseController::class, 'reorderLessons'])->name('owner.courses.lessons.reorder');

    // Quizzes
    Route::get('/quizzes', [\App\Http\Controllers\Web\QuizController::class, 'index'])->name('owner.quizzes');
    Route::post('/quizzes', [\App\Http\Controllers\Web\QuizController::class, 'store'])->name('owner.quizzes.store');
    Route::put('/quizzes/{id}', [\App\Http\Controllers\Web\QuizController::class, 'update'])->name('owner.quizzes.update');
    Route::delete('/quizzes/{id}', [\App\Http\Controllers\Web\QuizController::class, 'destroy'])->name('owner.quizzes.destroy');
    Route::get('/quizzes/{id}/builder', [\App\Http\Controllers\Web\QuizController::class, 'show'])->name('owner.quizzes.builder');
    Route::post('/quizzes/{quiz}/questions', [\App\Http\Controllers\Web\QuizController::class, 'storeQuestion'])->name('owner.quizzes.questions.store');
    Route::put('/quizzes/questions/{question}', [\App\Http\Controllers\Web\QuizController::class, 'updateQuestion'])->name('owner.quizzes.questions.update');
    Route::delete('/quizzes/questions/{question}', [\App\Http\Controllers\Web\QuizController::class, 'destroyQuestion'])->name('owner.quizzes.questions.destroy');

    // Certificates
    Route::get('/certificates', [\App\Http\Controllers\Web\CertificateController::class, 'index'])->name('owner.certificates');
    Route::post('/certificates', [\App\Http\Controllers\Web\CertificateController::class, 'store'])->name('owner.certificates.store');

    // Gamification (badges)
    Route::get('/badges', [\App\Http\Controllers\Web\BadgeController::class, 'index'])->name('owner.badges');
    Route::post('/badges', [\App\Http\Controllers\Web\BadgeController::class, 'store'])->name('owner.badges.store');
    Route::delete('/badges/{id}', [\App\Http\Controllers\Web\BadgeController::class, 'destroy'])->name('owner.badges.destroy');

    // Feature Flags
    Route::get('/feature-flags', [\App\Http\Controllers\Web\FeatureFlagController::class, 'index'])->name('owner.featureFlags');
    Route::patch('/feature-flags/{id}/toggle', [\App\Http\Controllers\Web\FeatureFlagController::class, 'toggle'])->name('owner.featureFlags.toggle');

    // Events
    Route::get('/events', [\App\Http\Controllers\Web\EventController::class, 'index'])->name('owner.events');
    Route::post('/events', [\App\Http\Controllers\Web\EventController::class, 'store'])->name('owner.events.store');
    Route::put('/events/{id}', [\App\Http\Controllers\Web\EventController::class, 'update'])->name('owner.events.update');
    Route::delete('/events/{id}', [\App\Http\Controllers\Web\EventController::class, 'destroy'])->name('owner.events.destroy');

    // Comment Moderation
    Route::get('/comments', [\App\Http\Controllers\Web\CommentModerationController::class, 'index'])->name('owner.comments');
    Route::patch('/comments/{id}/hide', [\App\Http\Controllers\Web\CommentModerationController::class, 'hide'])->name('owner.comments.hide');
    Route::patch('/comments/{id}/approve', [\App\Http\Controllers\Web\CommentModerationController::class, 'approve'])->name('owner.comments.approve');
    Route::delete('/comments/{id}', [\App\Http\Controllers\Web\CommentModerationController::class, 'destroy'])->name('owner.comments.destroy');

    // Teacher/Leader Verification
    Route::get('/teacher-verification', [\App\Http\Controllers\Web\TeacherVerificationController::class, 'index'])->name('owner.teacherVerification');
    Route::patch('/teacher-verification/{id}/verify', [\App\Http\Controllers\Web\TeacherVerificationController::class, 'verify'])->name('owner.teacherVerification.verify');
    Route::patch('/teacher-verification/{id}/unverify', [\App\Http\Controllers\Web\TeacherVerificationController::class, 'unverify'])->name('owner.teacherVerification.unverify');
    Route::put('/teacher-verification/{id}/profile', [\App\Http\Controllers\Web\TeacherVerificationController::class, 'updateProfile'])->name('owner.teacherVerification.updateProfile');

    // Support Tickets
    Route::get('/support', [\App\Http\Controllers\Web\SupportTicketController::class, 'index'])->name('owner.support');
    Route::get('/support/{id}', [\App\Http\Controllers\Web\SupportTicketController::class, 'show'])->name('owner.support.show');
    Route::post('/support/{id}/reply', [\App\Http\Controllers\Web\SupportTicketController::class, 'reply'])->name('owner.support.reply');
    Route::patch('/support/{id}/status', [\App\Http\Controllers\Web\SupportTicketController::class, 'updateStatus'])->name('owner.support.updateStatus');

    // Account Deletion Requests
    Route::get('/account-deletions', [\App\Http\Controllers\Web\AccountDeletionController::class, 'index'])->name('owner.accountDeletions');
    Route::patch('/account-deletions/{id}/approve', [\App\Http\Controllers\Web\AccountDeletionController::class, 'approve'])->name('owner.accountDeletions.approve');
    Route::patch('/account-deletions/{id}/reject', [\App\Http\Controllers\Web\AccountDeletionController::class, 'reject'])->name('owner.accountDeletions.reject');

    // System Monitoring
    Route::get('/system-monitor', [\App\Http\Controllers\Web\SystemMonitorController::class, 'index'])->name('owner.systemMonitor');

    // Backups
    Route::get('/backups', [\App\Http\Controllers\Web\BackupController::class, 'index'])->name('owner.backups');
    Route::post('/backups', [\App\Http\Controllers\Web\BackupController::class, 'store'])->name('owner.backups.store');
    Route::get('/backups/{filename}/download', [\App\Http\Controllers\Web\BackupController::class, 'download'])->name('owner.backups.download')->where('filename', '[^/]+');
    Route::delete('/backups/{filename}', [\App\Http\Controllers\Web\BackupController::class, 'destroy'])->name('owner.backups.destroy')->where('filename', '[^/]+');

    // Student Management
    Route::get('/students', [\App\Http\Controllers\Web\StudentManagementController::class, 'index'])->name('owner.students');
    Route::get('/students/{id}', [\App\Http\Controllers\Web\StudentManagementController::class, 'show'])->name('owner.students.show');
    Route::patch('/students/{id}/block', [\App\Http\Controllers\Web\StudentManagementController::class, 'block'])->name('owner.students.block');
    Route::patch('/students/{id}/unblock', [\App\Http\Controllers\Web\StudentManagementController::class, 'unblock'])->name('owner.students.unblock');

    // Roles & Permissions (RBAC)
    Route::get('/roles', [\App\Http\Controllers\Web\RoleController::class, 'index'])->name('owner.roles');
    Route::post('/roles', [\App\Http\Controllers\Web\RoleController::class, 'store'])->name('owner.roles.store');
    Route::put('/roles/{id}', [\App\Http\Controllers\Web\RoleController::class, 'update'])->name('owner.roles.update');
    Route::delete('/roles/{id}', [\App\Http\Controllers\Web\RoleController::class, 'destroy'])->name('owner.roles.destroy');
    Route::post('/owners/{id}/assign-roles', [\App\Http\Controllers\Web\RoleController::class, 'assign'])->name('owner.roles.assign');

    Route::get('/permissions', [\App\Http\Controllers\Web\PermissionController::class, 'index'])->name('owner.permissions');
    Route::post('/permissions', [\App\Http\Controllers\Web\PermissionController::class, 'store'])->name('owner.permissions.store');
    Route::put('/permissions/{id}', [\App\Http\Controllers\Web\PermissionController::class, 'update'])->name('owner.permissions.update');
    Route::delete('/permissions/{id}', [\App\Http\Controllers\Web\PermissionController::class, 'destroy'])->name('owner.permissions.destroy');

    route::get('/amatangazo', [AdminController::class, 'amatangazo'])->name('owner.amatangazo');
    Route::post('/amatangazo', [AdminController::class, 'storeAmatangazo'])->name('owner.amatangazo.store');
    Route::put('/amatangazo/{id}', [AdminController::class, 'updateAmatangazo'])->name('owner.amatangazo.update');
    Route::delete('/amatangazo/{id}', [AdminController::class, 'destroyAmatangazo'])->name('owner.amatangazo.destroy');
    Route::patch('/amatangazo/{id}/toggle', [AdminController::class, 'togglePublishAmatangazo'])->name('owner.amatangazo.toggle');
    route::get('/Ibitabo', [AdminController::class, 'Ibitabo'])->name('owner.ibitabo');

    Route::resource('users', AdminController::class);
    route::get('/Edit/{id}', [AdminController::class, 'ownerEditUser'])->name('owner.EditUser');
    route::get('/show/{id}', [AdminController::class, 'ownershowUser'])->name('owner.showUser');
    route::get('/chatroom', [ChatController::class, 'ownerchatroom'])->name('owner.chatroom');

    Route::get('/chat/conversations', [ChatController::class, 'conversations']);
    Route::get('/chat/messages/{guest_id}', [ChatController::class, 'adminMessages']);
    Route::post('/chat/send', [ChatController::class, 'adminSend']);
    Route::post('chat/read', [ChatController::class,'markAsRead']);
    Route::get('chat/typing/{guest_id}', [ChatController::class,'typingStatus']);
    //darsat
    Route::post('/storeDarsat', [AdminController::class, 'storeDarsat'])->name('owner.storeDarsat');
    Route::get('/viewDarsat', [AdminController::class, 'viewDarsat'])->name('owner.viewDarsat');
    Route::put('/darsat/{id}', [AdminController::class, 'updateDarsat'])->name('owner.updateDarsat');
    Route::delete('/darsat/{id}', [AdminController::class, 'destroyDarsat'])->name('owner.destroyDarsat');

    Route::post('/books/store', [AdminController::class,'storeBook'])
            ->name('owner.storeBook');

    Route::get('/books/view', [AdminController::class,'viewBooks'])
            ->name('owner.viewBooks');

    Route::delete('/books/{id}', [AdminController::class,'destroyBook'])
            ->name('owner.deleteBook');



    // Route::delete('/books/{id}', [BookController::class, 'destroy'])->name('owner.deleteBook');

    Route::put('/books/{id}', [AdminController::class, 'updateBook'])->name('owner.updateBook');

});

// ---- Student (learner) web auth + dashboard ----
Route::get('/student/register', [\App\Http\Controllers\Web\StudentAuthController::class, 'registerForm'])->name('student.register');
Route::post('/student/register', [\App\Http\Controllers\Web\StudentAuthController::class, 'submitRegister'])->name('student.register.submit');
Route::get('/student/login', [\App\Http\Controllers\Web\StudentAuthController::class, 'loginForm'])->name('student.login');
Route::post('/student/login', [\App\Http\Controllers\Web\StudentAuthController::class, 'submitLogin'])->name('student.login.submit');
Route::get('/student/2fa/challenge', [\App\Http\Controllers\Web\TwoFactorController::class, 'challengeForm'])->name('student.2fa.challenge');
Route::post('/student/2fa/challenge', [\App\Http\Controllers\Web\TwoFactorController::class, 'challengeSubmit'])->name('student.2fa.challenge.submit');
Route::post('/student/logout', [\App\Http\Controllers\Web\StudentAuthController::class, 'logout'])->name('student.logout');

Route::group(['prefix' => 'student', 'middleware' => 'studentAuth'], function () {
    Route::get('/dashboard', [\App\Http\Controllers\Web\StudentDashboardController::class, 'dashboard'])->name('student.dashboard');
    Route::get('/profile', [\App\Http\Controllers\Web\StudentDashboardController::class, 'profile'])->name('student.profile');
    Route::post('/profile', [\App\Http\Controllers\Web\StudentDashboardController::class, 'updateProfile'])->name('student.profile.update');
    Route::get('/settings', [\App\Http\Controllers\Web\StudentDashboardController::class, 'settings'])->name('student.settings');
    Route::post('/settings/password', [\App\Http\Controllers\Web\StudentDashboardController::class, 'updatePassword'])->name('student.settings.password');

    // Darsat learning progress
    Route::get('/progress', [\App\Http\Controllers\Web\DarsatProgressController::class, 'myProgress'])->name('student.progress');
    Route::post('/darsat/{darsat}/play', [\App\Http\Controllers\Web\DarsatProgressController::class, 'trackPlay'])->name('student.darsat.play');
    Route::post('/darsat/{darsat}/complete', [\App\Http\Controllers\Web\DarsatProgressController::class, 'markCompleted'])->name('student.darsat.complete');

    // Favorites
    Route::get('/favorites', [\App\Http\Controllers\Web\FavoriteController::class, 'myFavorites'])->name('student.favorites');
    Route::post('/favorites/{type}/{id}', [\App\Http\Controllers\Web\FavoriteController::class, 'toggle'])->name('student.favorites.toggle');

    // Notifications
    Route::get('/notifications', [\App\Http\Controllers\Web\NotificationController::class, 'index'])->name('student.notifications');
    Route::post('/notifications/{id}/read', [\App\Http\Controllers\Web\NotificationController::class, 'markRead'])->name('student.notifications.read');
    Route::post('/notifications/read-all', [\App\Http\Controllers\Web\NotificationController::class, 'markAllRead'])->name('student.notifications.readAll');
    Route::get('/notifications/unread-count', [\App\Http\Controllers\Web\NotificationController::class, 'unreadCount'])->name('student.notifications.unreadCount');

    // Courses
    Route::get('/courses', [\App\Http\Controllers\Web\StudentCourseController::class, 'myCourses'])->name('student.courses');
    Route::post('/courses/{slug}/enroll', [\App\Http\Controllers\Web\StudentCourseController::class, 'enroll'])->name('student.courses.enroll');
    Route::post('/courses/lessons/{lessonId}/complete', [\App\Http\Controllers\Web\StudentCourseController::class, 'completeLesson'])->name('student.courses.lessons.complete');

    // Quizzes
    Route::post('/quizzes/{id}/start', [\App\Http\Controllers\Web\StudentQuizController::class, 'start'])->name('student.quizzes.start');
    Route::get('/quizzes/attempt/{attemptId}/take', [\App\Http\Controllers\Web\StudentQuizController::class, 'take'])->name('student.quizzes.take');
    Route::post('/quizzes/attempt/{attemptId}/submit', [\App\Http\Controllers\Web\StudentQuizController::class, 'submit'])->name('student.quizzes.submit');
    Route::get('/quizzes/result/{attemptId}', [\App\Http\Controllers\Web\StudentQuizController::class, 'result'])->name('student.quizzes.result');
    Route::get('/quizzes/history', [\App\Http\Controllers\Web\StudentQuizController::class, 'history'])->name('student.quizzes.history');

    // Certificates
    Route::get('/certificates', [\App\Http\Controllers\Web\PublicCertificateController::class, 'myCertificates'])->name('student.certificates');
    Route::get('/certificates/{id}', [\App\Http\Controllers\Web\PublicCertificateController::class, 'show'])->name('student.certificates.show');

    // Badges / Gamification
    Route::get('/badges', [\App\Http\Controllers\Web\StudentBadgeController::class, 'index'])->name('student.badges');

    // Events
    Route::get('/events', [\App\Http\Controllers\Web\StudentEventController::class, 'myEvents'])->name('student.events');
    Route::post('/events/{slug}/register', [\App\Http\Controllers\Web\StudentEventController::class, 'register'])->name('student.events.register');
    Route::post('/events/{slug}/unregister', [\App\Http\Controllers\Web\StudentEventController::class, 'unregister'])->name('student.events.unregister');

    // Comments
    Route::post('/inyandiko/{id}/comments', [\App\Http\Controllers\Web\CommentController::class, 'store'])->name('student.comments.store');
    Route::post('/comments/{id}/like', [\App\Http\Controllers\Web\CommentController::class, 'toggleLike'])->name('student.comments.like');
    Route::post('/comments/{id}/report', [\App\Http\Controllers\Web\CommentController::class, 'report'])->name('student.comments.report');
    Route::delete('/comments/{id}', [\App\Http\Controllers\Web\CommentController::class, 'destroy'])->name('student.comments.destroy');

    // Support Tickets
    Route::get('/support', [\App\Http\Controllers\Web\StudentSupportController::class, 'index'])->name('student.support');
    Route::get('/support/create', [\App\Http\Controllers\Web\StudentSupportController::class, 'create'])->name('student.support.create');
    Route::post('/support', [\App\Http\Controllers\Web\StudentSupportController::class, 'store'])->name('student.support.store');
    Route::get('/support/{id}', [\App\Http\Controllers\Web\StudentSupportController::class, 'show'])->name('student.support.show');
    Route::post('/support/{id}/reply', [\App\Http\Controllers\Web\StudentSupportController::class, 'reply'])->name('student.support.reply');

    // Privacy Controls
    Route::get('/privacy', [\App\Http\Controllers\Web\PrivacyController::class, 'settings'])->name('student.privacy');
    Route::post('/privacy/visibility', [\App\Http\Controllers\Web\PrivacyController::class, 'updateVisibility'])->name('student.privacy.visibility');
    Route::get('/privacy/export', [\App\Http\Controllers\Web\PrivacyController::class, 'exportData'])->name('student.privacy.export');
    Route::post('/privacy/delete-request', [\App\Http\Controllers\Web\PrivacyController::class, 'requestDeletion'])->name('student.privacy.deleteRequest');

    // Two-Factor Authentication (management, requires being logged in already)
    Route::get('/2fa/setup', [\App\Http\Controllers\Web\TwoFactorController::class, 'setup'])->name('student.2fa.setup');
    Route::post('/2fa/enable', [\App\Http\Controllers\Web\TwoFactorController::class, 'enable'])->name('student.2fa.enable');
    Route::get('/2fa/manage', [\App\Http\Controllers\Web\TwoFactorController::class, 'manage'])->name('student.2fa.manage');
    Route::post('/2fa/disable', [\App\Http\Controllers\Web\TwoFactorController::class, 'disable'])->name('student.2fa.disable');
});

Route::get('/login', [WebAuthController::class, 'login_form'])->name('owner.login');
Route::post('/submit_login', [WebAuthController::class, 'submit_login'])->name('owner.submit.login');

Route::get('/forgot-password', [WebAuthController::class, 'forgot_password'])->name('guest.forgot-password');
Route::post('submit-forgot-password',[WebAuthController::class, 'submit_forgot_password'])->name('guest.submit-forgot-password');

// Route::get('/', [GuestController::class, 'home'])->name('guest.home');
Route::get('/', [GuestController::class, 'home'])
    ->middleware('track.visit')
    ->name('guest.home');
Route::get('/quiz-alert/upcoming', [\App\Http\Controllers\Web\QuizAlertController::class, 'upcoming'])->name('quiz.alert.upcoming');
Route::post('/push/subscribe', [\App\Http\Controllers\Web\PushSubscriptionController::class, 'store'])->name('push.subscribe');
Route::post('/push/unsubscribe', [\App\Http\Controllers\Web\PushSubscriptionController::class, 'destroy'])->name('push.unsubscribe');
Route::get('/ibitabo', [GuestController::class, 'books'])->name('guest.books');
Route::get('/abasheikh', [GuestController::class, 'teachers'])->name('guest.teachers');
Route::get('/teacher/{id}/darsa', [GuestController::class, 'teacherDarsa'])
    ->name('guest.teacher-darsa');
// Route::get('/inyigisho-zabasheikh', [GuestController::class, 'teacher_darsa'])->name('guest.teacher-darsa');

Route::get('/amatangazo', [GuestController::class, 'news'])->name('guest.news');
Route::get('/amasomo-agenda', [GuestController::class, 'courses'])->name('guest.courses');
Route::get('/amasomo-agenda/{slug}', [GuestController::class, 'courseShow'])->name('guest.course.show');
Route::get('/ibikorwa', [GuestController::class, 'events'])->name('guest.events');
Route::get('/ibikorwa/{slug}', [GuestController::class, 'eventShow'])->name('guest.event.show');

Route::get('/certificate/verify/{code?}', [\App\Http\Controllers\Web\PublicCertificateController::class, 'verify'])->name('certificate.verify');
Route::get('/sitemap.xml', [\App\Http\Controllers\Web\SitemapController::class, 'index'])->name('sitemap');
Route::get('/inyandiko-zabamenyi', [GuestController::class, 'inyandiko_zabamenyi'])->name('guest.inyandiko_zabamenyi');
Route::get('/inyandiko-zabamenyi/{slug}', [GuestController::class, 'inyandikoShow'])->name('guest.inyandiko.show');
Route::get('/twandikire', [GuestController::class, 'twandikire'])->name('guest.twandikire');
Route::get('/shakisha', [\App\Http\Controllers\Web\SearchController::class, 'search'])->name('guest.search');
Route::get('/shakisha/suggest', [\App\Http\Controllers\Web\SearchController::class, 'suggest'])->name('guest.search.suggest');
// Route::get('/inyigisho-zabasheikh', [GuestController::class, 'teacher_darsa'])->name('guest.teacher-darsa');

Route::post('/faq/ask', [\App\Http\Controllers\Web\GuestFaqChatController::class, 'ask'])->name('guest.faq.ask');

Route::post('/chat/presence',[ChatController::class,'presence']);
Route::get('/chat/messages/{guest_id}', [ChatController::class,'messages']);
Route::post('/chat/send', [ChatController::class,'sendMessage']);
Route::get('/live-visits', [GuestController::class, 'liveVisits'])->name('guest.live.visits');
Route::get('/guest/ping', [GuestController::class, 'ping']);

//verify otp
Route::get('/verify_otp', [WebAuthController::class, 'verify_otp'])
    ->name('guest.verify.otp');
Route::post('/submit_verify_otp', [WebAuthController::class, 'SubmitverifyOtp'])->name('guest.submit.verify.otp');

Route::get('/reset-password', [WebAuthController::class, 'reset_password'])
    ->name('guest.reset.password');

Route::post('/submit-reset-password', [WebAuthController::class, 'submit_reset_password'])
    ->name('guest.submit.reset.password');

Route::get('/test-mail', function () {
    \Mail::raw('Hello test email', function ($msg) {
        $msg->to('bikmangeek@gmail.com')
            ->subject('Test Email');
    });

    return 'Mail sent!';
});

Route::get('/debug-storage', function () {

    return [
        'public_path' => public_path(),
        'uploads_exists' => is_dir(public_path('uploads/audio')),
        'storage_exists' => is_dir(storage_path('app/public')),
        'public_audio_files' => is_dir(public_path('uploads/audio'))
            ? scandir(public_path('uploads/audio'))
            : [],
    ];

});

Route::get('/storage-debug', function () {

    return [
        'public_storage_link' => public_path('storage'),
        'is_link' => is_link(public_path('storage')),
        'storage_exists' => file_exists(public_path('storage')),

        'storage_audio_exists' => is_dir(storage_path('app/public/audio')),
        'storage_audio_files' => is_dir(storage_path('app/public/audio'))
            ? scandir(storage_path('app/public/audio'))
            : [],

        'public_storage_audio_exists' => is_dir(public_path('storage/audio')),
        'public_storage_audio_files' => is_dir(public_path('storage/audio'))
            ? scandir(public_path('storage/audio'))
            : [],
    ];

});