<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\WebAuthController;
use App\Http\Controllers\Web\AdminController;
use App\Http\Controllers\Web\GuestController;
use Illuminate\Support\Facades\Http;
use App\Http\Controllers\ChatController;


Route::group(['prefix'=>'owner' , 'middleware'=>'ownerAuth','throttle:100,1'],function(){
    
    Route::get('/dashboard', [AdminController::class, 'home'])->name('owner.dashboard');
    
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
    route::get('/amatangazo', [AdminController::class, 'amatangazo'])->name('owner.amatangazo');
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

    Route::post('/books/store', [AdminController::class,'storeBook'])
            ->name('owner.storeBook');

    Route::get('/books/view', [AdminController::class,'viewBooks'])
            ->name('owner.viewBooks');

    Route::delete('/books/{id}', [AdminController::class,'destroyBook'])
            ->name('owner.deleteBook');



    // Route::delete('/books/{id}', [BookController::class, 'destroy'])->name('owner.deleteBook');

    Route::get('/books/{id}/edit', [BookController::class, 'editBook'])->name('owner.editBook');

});

Route::get('/refresh_counts', [AdminController::class, 'refresh_counts'])->name('owner.refresh_counts');

Route::get('/login', [WebAuthController::class, 'login_form'])->name('owner.login');
Route::post('/submit_login', [WebAuthController::class, 'submit_login'])->name('owner.submit.login');

Route::get('/forgot-password', [WebAuthController::class, 'forgot_password'])->name('guest.forgot-password');
Route::post('submit-forgot-password',[WebAuthController::class, 'submit_forgot_password'])->name('guest.submit-forgot-password');

// Route::get('/', [GuestController::class, 'home'])->name('guest.home');
Route::get('/', [GuestController::class, 'home'])
    ->middleware('track.visit')
    ->name('guest.home');
Route::get('/ibitabo', [GuestController::class, 'books'])->name('guest.books');
Route::get('/abasheikh', [GuestController::class, 'teachers'])->name('guest.teachers');
Route::get('/teacher/{id}/darsa', [GuestController::class, 'teacherDarsa'])
    ->name('guest.teacher-darsa');
// Route::get('/inyigisho-zabasheikh', [GuestController::class, 'teacher_darsa'])->name('guest.teacher-darsa');

Route::get('/amatangazo', [GuestController::class, 'news'])->name('guest.news');
Route::get('/inyandiko-zabamenyi', [GuestController::class, 'inyandiko_zabamenyi'])->name('guest.inyandiko_zabamenyi');
Route::get('/twandikire', [GuestController::class, 'twandikire'])->name('guest.twandikire');
Route::get('/shakisha', [GuestController::class, 'search'])->name('guest.search');
// Route::get('/inyigisho-zabasheikh', [GuestController::class, 'teacher_darsa'])->name('guest.teacher-darsa');

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