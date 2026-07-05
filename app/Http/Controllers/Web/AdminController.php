<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use App\Models\Owner;
use App\Models\User;
use Carbon\Carbon;
use App\Models\Visit;
use App\Models\Payment;
use App\Models\DarsatTable;
use Illuminate\Support\Str;
use App\Models\Book;

class AdminController extends Controller
{
    public function home(){
        $all_count_users = collect(User::all())->count();
        $count_users = collect(User::all()->where('firstname','!=',null))->count();
        $partial_count_users = collect(User::all()->where('firstname',null))->count();
        $paidUsersCount = Payment::all()->count();
    	
        $user_joined_today_count = User::whereDate('created_at', now()->toDateString())->where('firstname','!=',null)->count();
        
        $onlineUsersCount = User::where('last_active_at', '>=', now()->subMinutes(5))->count();

        // $percent_online_user_count = ( $onlineUsersCount * 100 ) / $count_users;
        // $percent_today_count_users = ( $user_joined_today_count * 100 ) / $count_users; 

         if ($count_users > 0) {
            // Calculate percentages
            $percent_online_user_count = ($onlineUsersCount * 100) / $count_users;
            $percent_today_count_users = ($user_joined_today_count * 100) / $count_users;
            $percentPaidUsersCount = ($paidUsersCount * 100) / $count_users;
        } else {
            // Default values if $count_users is zero
            $percent_online_user_count = 0;
            $percent_today_count_users = 0;
            $percentPaidUsersCount = 0;
        }

        #start of visit count
        $todaysVisitCount = Visit::whereDate('date', Carbon::today())->sum('count');    
        $yesterdaysVisitCount = Visit::whereDate('date', Carbon::yesterday())->sum('count');
        $allVisitCount = Visit::all()->sum('count');
        #end of visit count

        return view('Users.admin.home',[
            'allUsersCount' => $all_count_users,
            'partialCountUsers' => $partial_count_users,
            'user_joined_today_count' => $user_joined_today_count,
            'online_user_count' => $onlineUsersCount,
            'percent_online_user_count' => substr(number_format($percent_online_user_count, 2, '.', ''), 0, -1).'%',
            'percent_user_joined_today' => substr(number_format($percent_today_count_users, 2, '.', ''), 0, -1).'%',
            'todaysVisitCount' => $todaysVisitCount,
            'yesterdaysVisitCount' => $yesterdaysVisitCount,
            'allVisitCount' => $allVisitCount,
            'paidUsersCount' => $paidUsersCount,
            'percentPaidUsersCount' => $percentPaidUsersCount,
    	]);
    }

    public function refresh_counts(){
        $all_count_users = collect(User::all())->count();
        $count_users = collect(User::all()->where('firstname','!=',null))->count();
        $partial_count_users = collect(User::all()->where('firstname',null))->count();
        
        $onlineUsersCount = User::where('last_active_at', '>=', now()->subMinutes(5))->count();

        // $percent_online_user_count = ( $onlineUsersCount * 100 ) / $count_users;

        #start User_joined_today
        // $user_joined_today_count = User::whereDate('created_at', now()->toDateString())->count();
        // $percent_user_joined_today = ( $user_joined_today_count * 100 ) / $count_users;
        #end User_joined_today
        if ($count_users > 0) {
            $percent_online_user_count = ($onlineUsersCount * 100) / $count_users;
        } else {
            $percent_online_user_count = 0;
        }

        #start User_joined_today
        $user_joined_today_count = User::whereDate('created_at', now()->toDateString())->where('firstname','!=',null)->count();
        // $percent_user_joined_today = ( $user_joined_today_count * 100 ) / $count_users;
        if ($count_users > 0) {
            $percent_user_joined_today = ($user_joined_today_count * 100) / $count_users;
        } else {
            $percent_user_joined_today = 0;
        }

        #start visit count
        $todaysVisitCount = Visit::whereDate('date', Carbon::today())->sum('count');    
        $yesterdaysVisitCount = Visit::whereDate('date', Carbon::yesterday())->sum('count');

        $allVisitCount = Visit::all()->sum('count');
        #end of visit count

        function todaysVisitCountFN($todaysVisitCount) {
            if ($todaysVisitCount >= 1000000000) {
                // For billions
                return number_format($todaysVisitCount / 1000000000, 1) . 'B';
            } elseif ($todaysVisitCount >= 1000000) {
                // For millions
                return number_format($todaysVisitCount / 1000000, 1) . 'M';
            } elseif ($todaysVisitCount >= 1000) {
                // For thousands
                return number_format($todaysVisitCount / 1000, 1) . 'K';
            } else {
                // Return the number as is if it's less than 1000
                return $todaysVisitCount;
            }
        }

        function yesterdaysVisitCountFN($yesterdaysVisitCount) {
            if ($yesterdaysVisitCount >= 1000000000) {
                // For billions
                return number_format($yesterdaysVisitCount / 1000000000, 1) . 'B';
            } elseif ($yesterdaysVisitCount >= 1000000) {
                // For millions
                return number_format($yesterdaysVisitCount / 1000000, 1) . 'M';
            } elseif ($yesterdaysVisitCount >= 1000) {
                // For thousands
                return number_format($yesterdaysVisitCount / 1000, 1) . 'K';
            } else {
                // Return the number as is if it's less than 1000
                return $yesterdaysVisitCount;
            }
        }

        function allVisitCountFN($allVisitCount) {
            if ($allVisitCount >= 1000000000) {
                // For billions
                return number_format($allVisitCount / 1000000000, 1) . 'B';
            } elseif ($allVisitCount >= 1000000) {
                // For millions
                return number_format($allVisitCount / 1000000, 1) . 'M';
            } elseif ($allVisitCount >= 1000) {
                // For thousands
                return number_format($allVisitCount / 1000, 1) . 'K';
            } else {
                // Return the number as is if it's less than 1000
                return $allVisitCount;
            }
        }

        return response()->json([
            'allUsersCount' => $all_count_users,
            'partialCountUsers' => $partial_count_users,
            'online_user_count' => $onlineUsersCount,
            'percent_online_user_count' => substr(number_format($percent_online_user_count, 2, '.', ''), 0, -1).'%',
            'user_joined_today_count' => $user_joined_today_count,
            'percent_user_joined_today' => substr(number_format($percent_user_joined_today, 2, '.', ''), 0, -1).'%',
            'todaysVisitCount' => todaysVisitCountFN($todaysVisitCount),
            'yesterdaysVisitCount' => yesterdaysVisitCountFN($yesterdaysVisitCount),
            'allVisitCount' => allVisitCountFN($allVisitCount),
        ]);

    }

    public function display_paid_users(Request $request){
        
        // $users = User::where('firstname','!=',null)->orderBy('user_code','desc')->paginate(10);

        $payments = Payment::with('user')->paginate(10);
        $totatAmount = Payment::all()->sum('amount');
        $count_payee = collect(Payment::all())->count();
        $count = 1;
        return view('Users.admin.paid_users', [
            'payments' => $payments,
            'totatAmount' => $totatAmount,
            'count_payee' => $count_payee,
            'count' => $count,
        ]);
    }

    public function search_users_payment(Request $request){
        
        $query = User::query();

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->Where('user_code', 'like', "%$search%")
                  ->orWhere('firstname', 'like', "%$search%")
                  ->orWhere('lastname', 'like', "%$search%");
            });
        }
        $users = $query->paginate(10);
        return view('Users.admin.SearchUser_ready_ToPay', compact('users'));
    }

    public function assign_payment_ToUser($id){
        $user_id=Crypt::decrypt($id);
        $users = User::all()->where('id',$user_id);
        return view('Users.admin.assign_payment_ToUser',[
            'user_id' => $user_id,
            'users' => $users
        ]);
    }

    // public function submit_payment_ToUser(Request $request,$id){
    //     $request->validate([
    //         'amount' => 'required|numeric',
    //         'duration' => 'required|integer|min:1',
    //     ]);

    //     $user_id = $id;
    //     $currentDate = Carbon::now();

    //     $payment = new Payment();
    //     $payment->user_id = $user_id;
    //     $payment->amount = $request->amount;
    //     $payment->duration = $request->duration;
    //     $payment->start_date = $currentDate;
    //     $payment->end_date = $currentDate->copy()->addMonths($request->duration);
    //     $payment->active_days = $request->duration * 30;
    //     $payment->save();

    //     return redirect()->route('admin.display_paid_users')->with(
    //         'info' , 'Payment created successfully',
    //     );
    // }

    public function submit_payment_ToUser(Request $request, $id){
        $request->validate([
            'amount' => 'required|numeric',
            'duration' => 'required|integer|min:1',
        ]);

        $user_id = $id;
        $currentDate = Carbon::now();

        // Find the latest payment for the user
        $latestPayment = Payment::where('user_id', $user_id)
            ->orderBy('created_at', 'desc')
            ->first();

        if ($latestPayment) {
            $endDate = Carbon::parse($latestPayment->end_date);

            if ($currentDate->lessThanOrEqualTo($endDate)) {
                return redirect()->back()->with('error', 'User already has an active subscription. Please wait until it expires.');
            }
        }

        $payment = new Payment();
        $payment->user_id = $user_id;
        $payment->amount = $request->amount;
        $payment->duration = $request->duration;
        $payment->start_date = $currentDate;
        $payment->end_date = $currentDate->copy()->addMonths($request->duration);
        $payment->active_days = $request->duration * 30;
        $payment->save();

        return redirect()->route('owner.display_paid_users')->with(
            'info', 'Payment created successfully'
        );
    }

    public function view_all_users(){
        $users = User::where('firstname','!=',null)->paginate(10);
        $count_users = $users->count();

        return view('Users.admin.view_all_users',compact('users','count_users'));
    }

    public function view_all_users_joined_today(){
        $users = User::whereDate('created_at', now()->toDateString())->where('firstname','!=',null)->paginate(10);

        $count_users = $users->count();

        return view('Users.admin.view_all_users_joined_today',compact('users','count_users'));
    }

    public function AddUser(){
        return view('Users.admin.addUsers');
    }

    public function darsat(Request $request)
    {
        $title = $request->title;

        $users = Owner::when($title, function ($query) use ($title) {
                return $query->where('title', $title);
            }, function ($query) {
                return $query->whereIn('title', ['sheikh','ustadh']);
            })
            ->latest()
            ->orderBy('title')
            ->get();

        return view('Users.admin.darsat', compact('users'));
    }

    // public function storeDarsat(Request $request)
    // {
    //     $request->validate([
    //         'title'    => 'required|string|max:255',
    //         'teachers' => 'required|exists:owners,id',
    //         'type'     => 'required|string|max:100',
    //         'audio'    => 'required|mimes:mp3,wav,ogg,m4a|max:51200|unique:darsat_tables,audio',
    //     ]);

    //     // dd($request);

    //     $audioName = null;

    //     if ($request->hasFile('audio')) {

    //         // $audioName = $request->audio->getClientOriginalName();
    //         $originalName = $request->file('audio')->getClientOriginalName();

    //         if (DarsatTable::where('audio', 'like', '%_'.$originalName)->exists()) {
    //             return back()
    //                 ->withInput()
    //                 ->withErrors([
    //                     'audio' => 'This audio file has already been uploaded.'
    //                 ]);
    //         }
    //         // $audioName = time().'_'.$request->audio->getClientOriginalName();

    //         $request->audio->move(public_path('uploads/audio'), $audioName);
    //     }

    //     DarsatTable::create([
    //         'title'    => $request->title,
    //         'teachers' => $request->teachers,
    //         'type'     => $request->type,
    //         'audio'    => $audioName,
    //     ]);

    //     return redirect()
    //         ->back()
    //         ->with('info', 'Darsat added successfully !');
    // }

    // public function storeDarsat(Request $request){
    //     $request->validate([
    //         'title'    => 'required|string|max:255',
    //         'teachers' => 'required|exists:owners,id',
    //         'type'     => 'required|string|max:100',
    //         'audio'    => 'required|mimes:mp3,wav,ogg,m4a|max:51200',
    //     ]);

    //     $audioName = null;

    //     if ($request->hasFile('audio')) {

    //         // Original file name
    //         $originalName = $request->file('audio')->getClientOriginalName();

    //         // Check if a file with the same original name has already been uploaded
    //         if (DarsatTable::where('audio', 'like', '%_'.$originalName)->exists()) {
    //             return back()
    //                 ->withInput()
    //                 ->withErrors([
    //                     'audio' => 'This audio file has already been uploaded.'
    //                     // 'audio' => 'Iyi audio iri muri mububiko !'

    //                 ]);
    //         }

    //         // Create a unique file name
    //         $audioName = time() . '_' . $originalName;

    //         // Move file
    //         $request->file('audio')->move(
    //             public_path('uploads/audio'),
    //             $audioName
    //         );
    //     }

    //     DarsatTable::create([
    //         'title'    => $request->title,
    //         'teachers' => $request->teachers,
    //         'type'     => $request->type,
    //         'audio'    => $audioName,
    //     ]);

    //     return redirect()
    //         ->back()
    //         ->with('info', 'Darsat added successfully!');
    // }

    public function storeDarsat(Request $request)
    {
        $request->validate([
            'title'    => 'required|string|max:255',
            'teachers' => 'required|exists:owners,id',
            'type'     => 'required|string|max:100',
            'audio'    => 'required|mimes:mp3,wav,ogg,m4a|max:51200',
        ]);

        $audioName = null;

        if ($request->hasFile('audio')) {

            $file = $request->file('audio');

            // Original filename without extension
            $originalName = pathinfo(
                $file->getClientOriginalName(),
                PATHINFO_FILENAME
            );

            // File extension
            $extension = strtolower($file->getClientOriginalExtension());

            // Clean filename (replace spaces & special characters with underscores)
            $cleanName = Str::slug($originalName, '_');

            // Check if the same audio has already been uploaded
            $existingAudio = DarsatTable::where('audio', 'like', '%_' . $cleanName . '.' . $extension)
                ->exists();

            if ($existingAudio) {
                return back()
                    ->withInput()
                    ->withErrors([
                        'audio' => 'This audio file has already been uploaded.',
                        // 'audio' => 'Iyi audio iri muri mubiko!'
                    ]);
            }

            // Create unique filename
            $audioName = time() . '_' . $cleanName . '.' . $extension;

            // Ensure upload directory exists
            $destination = public_path('uploads/audio');

            if (!is_dir($destination)) {
                mkdir($destination, 0755, true);
            }

            // Move uploaded file
            $file->move($destination, $audioName);
        }

        DarsatTable::create([
            'title'    => $request->title,
            'teachers' => $request->teachers,
            'type'     => $request->type,
            'audio'    => $audioName,
        ]);

        return back()->with('info', 'Darsat added successfully!');
    }

    // public function viewDarsat()
    // {
    //     $users = Owner::whereIn('title', ['sheikh', 'ustadh'])
    //         ->orderBy('firstname')
    //         ->get();

    //     $darsat = DarsatTable::latest()->get()->groupBy('teachers');

    //     return view('Users.admin.ViewDarsat', compact(
    //         'users',
    //         'darsat'
    //     ));
    // }

    public function viewDarsat(){
        $users = Owner::whereIn('title', ['sheikh', 'ustadh'])
            ->withCount('darsat')
            ->orderBy('firstname')
            ->get();

        $darsat = DarsatTable::latest()
            ->get()
            ->groupBy('teachers');

        return view('Users.admin.ViewDarsat', compact(
            'users',
            'darsat'
        ));
    }

    public function inyandiko_zabamenyi(){
        return view('Users.admin.inyandiko_zabamenyi');
    }

    public function amatangazo(){
        return view('Users.admin.amatangazo');
    }

    public function ibitabo(){
        return view('Users.admin.ibitabo');
    }

    public function storeBook(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'book'  => 'required|mimes:pdf|max:51200',
        ]);

        $bookName = null;

        if ($request->hasFile('book')) {

            $file = $request->file('book');

            $bookName = time().'_'.$file->getClientOriginalName();

            $file->move(public_path('books'), $bookName);
        }

        Book::create([

            'title' => $request->title,

            'book' => $bookName,

        ]);

        return redirect()
                ->back()
                ->with('success','Book uploaded successfully.');
    }

    /**
     * View Books
     */
    public function viewBooks()
    {
        $books = Book::latest()->paginate(10);

        return view('Users.admin.view_books', compact('books'));
    }

    /**
     * Delete Book
     */
    public function destroyBook($id)
    {
        $book = Book::findOrFail($id);

        if(file_exists(public_path('books/'.$book->book))){

            unlink(public_path('books/'.$book->book));

        }

        $book->delete();

        return back()->with('success','Book deleted successfully.');
    }

    /*
    |--------------------------------------------------------------------------
    | VIEW USERS
    |--------------------------------------------------------------------------
    */

    public function ViewUser(Request $request)
    {
        /*
        |--------------------------------------------------------------------------
        | GET FILTER TITLE
        |--------------------------------------------------------------------------
        */

        $title = $request->title;

        /*
        |--------------------------------------------------------------------------
        | USERS
        |--------------------------------------------------------------------------
        */

        $users = Owner::when($title, function ($query) use ($title) {

                return $query->where('title', $title);

            })
            ->latest()
            ->get();

        /*
        |--------------------------------------------------------------------------
        | GET DISTINCT TITLES
        |--------------------------------------------------------------------------
        */

        // $titles = Owner::select('title')
        //                 ->distinct()
        //                 ->get();
        $titles = Owner::select('title')
                ->selectRaw('COUNT(*) as total')
                ->groupBy('title')
                ->get();

        /*
        |--------------------------------------------------------------------------
        | RETURN VIEW
        |--------------------------------------------------------------------------
        */

        return view('Users.admin.viewUsers', compact(
            'users',
            'titles'
        ));
    }

    /*
    |--------------------------------------------------------------------------
    | CREATE PAGE
    |--------------------------------------------------------------------------
    */

    public function create()
    {

        return view('Users.admin.addUser');
    }

    /*
    |--------------------------------------------------------------------------
    | STORE USER
    |--------------------------------------------------------------------------
    */

    public function store(Request $request)
    {

        /*
        |--------------------------------------------------------------------------
        | VALIDATION
        |--------------------------------------------------------------------------
        */

        $request->validate([

            'firstname' => 'required',
            'lastname'  => 'required',
            'gender'    => 'required',
            'phone'     => 'required|unique:users',
            'dob'       => 'required',
            'email'     => 'required|email|unique:users',
            'role'      => 'required',
            'title'     => 'required',
            'password'  => 'required|min:6',
            'image'     => 'nullable|image|mimes:jpg,jpeg,png'

        ]);

        /*
        |--------------------------------------------------------------------------
        | IMAGE
        |--------------------------------------------------------------------------
        */

        $imageName = 'user.png';

        if ($request->hasFile('image')) {

            $image = $request->file('image');

            $imageName = time().'.'.$image->getClientOriginalExtension();

            $image->move(public_path('images/users'), $imageName);
        }

        /*
        |--------------------------------------------------------------------------
        | CREATE USER
        |--------------------------------------------------------------------------
        */

        Owner::create([

            'firstname' => $request->firstname,
            'lastname'  => $request->lastname,
            'gender'    => $request->gender,
            'phone'     => $request->phone,
            'image'     => $imageName,
            'dob'       => $request->dob,
            'email'     => $request->email,
            'role'      => $request->role,
            'title'     => $request->title,
            'password'  => bcrypt($request->password),

        ]);

        /*
        |--------------------------------------------------------------------------
        | REDIRECT
        |--------------------------------------------------------------------------
        */

        return redirect()
                ->route('owner.addUser')
                ->with('success', 'User created successfully');
    }

    /*
    |--------------------------------------------------------------------------
    | SHOW USER
    |--------------------------------------------------------------------------
    */

    // public function show($id)
    // {

    //     $user = User::findOrFail($id);

    //     return view('Users.admin.viewUsers', compact('user'));
    // }

    public function ownershowUser($id)
    {
        
        $user = Owner::findOrFail($id);
        return view('Users.admin.showUser', compact('user'));
    }

    public function ownerEditUser($id)
    {

        $user = User::findOrFail($id);

        return view('Users.admin.editUser', compact('user'));
    }

    public function update(Request $request, $id)
    {

        $user = User::findOrFail($id);

        /*
        |--------------------------------------------------------------------------
        | VALIDATION
        |--------------------------------------------------------------------------
        */

        $request->validate([

            'firstname' => 'required',
            'lastname'  => 'required',
            'gender'    => 'required',
            'phone'     => 'required|unique:users,phone,'.$user->id,
            'dob'       => 'required',
            'email'     => 'required|email|unique:users,email,'.$user->id,
            'role'      => 'required',
            'title'     => 'required',

        ]);

        /*
        |--------------------------------------------------------------------------
        | IMAGE UPDATE
        |--------------------------------------------------------------------------
        */

        $imageName = $user->image;

        if ($request->hasFile('image')) {

            $image = $request->file('image');

            $imageName = time().'.'.$image->getClientOriginalExtension();

            $image->move(public_path('images/users'), $imageName);
        }

        /*
        |--------------------------------------------------------------------------
        | UPDATE DATA
        |--------------------------------------------------------------------------
        */

        $user->update([

            'firstname' => $request->firstname,
            'lastname'  => $request->lastname,
            'gender'    => $request->gender,
            'phone'     => $request->phone,
            'image'     => $imageName,
            'dob'       => $request->dob,
            'email'     => $request->email,
            'role'      => $request->role,
            'title'     => $request->title,

        ]);

        /*
        |--------------------------------------------------------------------------
        | PASSWORD UPDATE
        |--------------------------------------------------------------------------
        */

        if ($request->password != null) {

            $user->update([

                'password' => bcrypt($request->password)

            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | REDIRECT
        |--------------------------------------------------------------------------
        */

        return redirect()
                ->route('users.index')
                ->with('success', 'User updated successfully');
    }

    /*
    |--------------------------------------------------------------------------
    | DELETE USER
    |--------------------------------------------------------------------------
    */

    public function destroy($id)
    {

        $user = User::findOrFail($id);

        /*
        |--------------------------------------------------------------------------
        | DELETE IMAGE
        |--------------------------------------------------------------------------
        */

        if ($user->image != 'user.png') {

            $path = public_path('images/users/'.$user->image);

            if (file_exists($path)) {

                unlink($path);
            }
        }

        /*
        |--------------------------------------------------------------------------
        | DELETE USER
        |--------------------------------------------------------------------------
        */

        $user->delete();

        /*
        |--------------------------------------------------------------------------
        | REDIRECT
        |--------------------------------------------------------------------------
        */

        return redirect()
                ->back()
                ->with('success', 'User deleted successfully');
    }

}
