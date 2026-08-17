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
use App\Models\Amatangazo;
use App\Models\Inyandiko;
use App\Notifications\NewAmatangazoNotification;
use App\Notifications\NewDarsatNotification;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use App\Models\GuestVisit;
use App\Services\DashboardChartService;

class AdminController extends Controller
{
    use \App\Traits\HandlesFileUploads;

    public function __construct()
    {
        // Users
        $this->middleware('permission:users.view')->only([
            'view_all_users', 'view_all_users_joined_today', 'search_users_payment',
            'ViewUser', 'ownershowUser', 'show', 'index', 'display_paid_users',
        ]);
        $this->middleware('permission:users.create')->only(['AddUser', 'create', 'store']);
        $this->middleware('permission:users.update')->only([
            'ownerEditUser', 'edit', 'update', 'assign_payment_ToUser', 'submit_payment_ToUser',
        ]);
        $this->middleware('permission:users.delete')->only(['destroy']);

        // Darsat
        $this->middleware('permission:darsat.view')->only(['darsat', 'viewDarsat']);
        $this->middleware('permission:darsat.create')->only(['storeDarsat']);
        $this->middleware('permission:darsat.update')->only(['updateDarsat']);
        $this->middleware('permission:darsat.delete')->only(['destroyDarsat']);

        // Inyandiko
        $this->middleware('permission:inyandiko.view')->only(['inyandiko_zabamenyi']);
        $this->middleware('permission:inyandiko.create')->only(['storeInyandiko']);
        $this->middleware('permission:inyandiko.update')->only(['updateInyandiko']);
        $this->middleware('permission:inyandiko.delete')->only(['destroyInyandiko']);

        // Amatangazo
        $this->middleware('permission:amatangazo.view')->only(['amatangazo']);
        $this->middleware('permission:amatangazo.create')->only(['storeAmatangazo']);
        $this->middleware('permission:amatangazo.update')->only(['updateAmatangazo', 'togglePublishAmatangazo']);
        $this->middleware('permission:amatangazo.delete')->only(['destroyAmatangazo']);

        // Books
        $this->middleware('permission:books.view')->only(['ibitabo', 'viewBooks']);
        $this->middleware('permission:books.create')->only(['storeBook']);
        $this->middleware('permission:books.update')->only(['updateBook']);
        $this->middleware('permission:books.delete')->only(['destroyBook']);

        // Deliberately NOT gated: home (dashboard), refresh_counts, and the
        // self-service profile actions — every logged-in owner needs those
        // regardless of role, or nobody could ever reach the page that lets
        // them see they have no permissions.
    }

    public function home(DashboardChartService $charts){
        $allSystemUsersCount = User::count();
        $teachersCount = Owner::whereIn('title', ['sheikh', 'ustadh'])->count();
        $allDarsatCount = DarsatTable::count();
        $booksCount = Book::count();

        $todaysVisitCount = Visit::whereDate('date', Carbon::today())->sum('count');
        $amatangazoCount = Amatangazo::count();
        $onlineUsersCount = User::where('last_active_at', '>=', now()->subMinutes(5))->count();
        $onlineGuestCount = GuestVisit::where('last_visit_at', '>=', now()->subMinutes(5))->count();

        // Opportunistically record today's peak online count for the
        // "online users over time" chart — see DashboardChartService's
        // class doc for why this is a lazy snapshot rather than a cron job.
        $charts->recordTodaysSnapshot($onlineUsersCount);

        return view('Users.admin.home', [
            'allSystemUsersCount' => $allSystemUsersCount,
            'teachersCount'       => $teachersCount,
            'allDarsatCount'      => $allDarsatCount,
            'booksCount'          => $booksCount,
            'todaysVisitCount'    => $todaysVisitCount,
            'amatangazoCount'     => $amatangazoCount,
            'onlineUsersCount'    => $onlineUsersCount,
            'onlineGuestCount'    => $onlineGuestCount,
        ]);
    }

    /**
     * Paginated list of currently-online students, for the click-to-see-
     * names modal on the dashboard's online-users indicator.
     */
    public function onlineUsersList(Request $request)
    {
        $users = User::where('last_active_at', '>=', now()->subMinutes(5))
            ->orderByDesc('last_active_at')
            ->paginate(10);

        return response()->json([
            'users' => collect($users->items())->map(fn($u) => [
                'name'            => trim(($u->firstname ?? '') . ' ' . ($u->lastname ?? '')) ?: 'Umunyeshuri',
                'last_active_ago' => $u->last_active_at?->diffForHumans(),
            ]),
            'current_page' => $users->currentPage(),
            'last_page'    => $users->lastPage(),
            'total'        => $users->total(),
        ]);
    }

    /**
     * Real time-series data for the dashboard's 4 independent charts.
     * ?metric=students|darsat|amatangazo|online_users
     * ?period=day|week|month|year (default day)
     */
    public function dashboardChartData(Request $request, DashboardChartService $charts)
    {
        $request->validate([
            'metric' => 'required|in:students,darsat,amatangazo,online_users',
            'period' => 'nullable|in:day,week,month,year',
        ]);

        return response()->json(
            $charts->seriesFor($request->metric, $request->period ?? 'day')
        );
    }

    /**
     * Kept for backward compatibility with any cached/bookmarked link —
     * returns the same 8 real metrics as home(), as JSON, for a
     * lightweight periodic refresh of just the online-count card without
     * a full page reload.
     */
    public function refresh_counts(){
        $onlineUsersCount = User::where('last_active_at', '>=', now()->subMinutes(5))->count();
        $onlineGuestCount = \App\Models\GuestVisit::where('last_visit_at', '>=', now()->subMinutes(5))->count();
        $todaysVisitCount = Visit::whereDate('date', Carbon::today())->sum('count');

        return response()->json([
            'allSystemUsersCount' => User::count(),
            'teachersCount'       => Owner::whereIn('title', ['sheikh', 'ustadh'])->count(),
            'allDarsatCount'      => DarsatTable::count(),
            'booksCount'          => Book::count(),
            'todaysVisitCount'    => $todaysVisitCount,
            'amatangazoCount'     => Amatangazo::count(),
            'onlineUsersCount'    => $onlineUsersCount,
            'onlineGuestCount'    => $onlineGuestCount,
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

    public function storeDarsat(Request $request)
    {
        $request->validate([
            'title'       => 'required|string|max:255',
            'teachers'    => 'required|exists:owners,id',
            'type'        => 'required|string|max:100',
            'description' => 'nullable|string',
            'audio'       => 'required|mimes:mp3,wav,ogg,m4a|max:51200',
            'thumbnail'   => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
            'status'      => 'required|in:draft,published',
        ]);

        $audioName = $this->storeUploadedFile($request->file('audio'), 'audio');
        $thumbnailName = $this->storeUploadedFile($request->file('thumbnail'), 'darsat/thumbnails');

        $darsat = DarsatTable::create([
            'title'        => $request->title,
            'teachers'     => $request->teachers,
            'type'         => $request->type,
            'description'  => $request->description,
            'audio'        => $audioName,
            'thumbnail'    => $thumbnailName,
            'status'       => $request->status,
            'created_by'   => auth('owner')->id(),
            'published_at' => $request->status === 'published' ? now() : null,
        ]);

        if ($darsat->status === 'published') {
            User::whereNotNull('id')->chunk(200, function ($students) use ($darsat) {
                Notification::send($students, new NewDarsatNotification($darsat));
            });
        }

        return back()->with('info', 'Darsat added successfully!');
    }

    public function updateDarsat(Request $request, $id)
    {
        $darsat = DarsatTable::findOrFail($id);

        $request->validate([
            'title'       => 'required|string|max:255',
            'teachers'    => 'required|exists:owners,id',
            'type'        => 'required|string|max:100',
            'description' => 'nullable|string',
            'audio'       => 'nullable|mimes:mp3,wav,ogg,m4a|max:51200',
            'thumbnail'   => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
            'status'      => 'required|in:draft,published',
        ]);

        $audioName = $darsat->audio;
        if ($request->hasFile('audio')) {
            $this->deleteUploadedFile($darsat->audio, 'audio');
            $audioName = $this->storeUploadedFile($request->file('audio'), 'audio');
        }

        $thumbnailName = $darsat->thumbnail;
        if ($request->hasFile('thumbnail')) {
            $this->deleteUploadedFile($darsat->thumbnail, 'darsat/thumbnails');
            $thumbnailName = $this->storeUploadedFile($request->file('thumbnail'), 'darsat/thumbnails');
        }

        $darsat->update([
            'title'        => $request->title,
            'teachers'     => $request->teachers,
            'type'         => $request->type,
            'description'  => $request->description,
            'audio'        => $audioName,
            'thumbnail'    => $thumbnailName,
            'status'       => $request->status,
            'updated_by'   => auth('owner')->id(),
            'published_at' => $request->status === 'published' ? ($darsat->published_at ?? now()) : null,
        ]);

        return back()->with('info', 'Darsat updated successfully!');
    }

    public function destroyDarsat($id)
    {
        $darsat = DarsatTable::findOrFail($id);

        $this->deleteUploadedFile($darsat->audio, 'audio');
        $this->deleteUploadedFile($darsat->thumbnail, 'darsat/thumbnails');

        $darsat->delete();

        return back()->with('info', 'Darsat deleted.');
    }

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

    /**
     * Inyandiko CRUD
     */
    public function inyandiko_zabamenyi()
    {
        $inyandiko = Inyandiko::latest()->paginate(10);

        return view('Users.admin.inyandiko_zabamenyi', compact('inyandiko'));
    }

    public function storeInyandiko(Request $request)
    {
        $request->validate([
            'title'    => 'required|string|max:255',
            'category' => 'nullable|string|max:100',
            'author'   => 'nullable|string|max:255',
            'summary'  => 'nullable|string|max:500',
            'content'  => 'nullable|string',
            'status'   => 'required|in:draft,published',
            'image'    => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
            'file'     => 'nullable|mimes:pdf,doc,docx|max:20480',
        ]);

        $imageName = $this->storeUploadedFile($request->file('image'), 'inyandiko');
        $fileName = $this->storeUploadedFile($request->file('file'), 'inyandiko/files');

        $baseSlug = Str::slug($request->title);
        $slug = $baseSlug;
        $i = 1;
        while (Inyandiko::where('slug', $slug)->exists()) {
            $slug = $baseSlug . '-' . $i++;
        }

        Inyandiko::create([
            'title'        => $request->title,
            'slug'         => $slug,
            'category'     => $request->category,
            'author'       => $request->author,
            'summary'      => $request->summary,
            'content'      => $request->content,
            'image'        => $imageName,
            'file'         => $fileName,
            'status'       => $request->status,
            'published_at' => $request->status === 'published' ? now() : null,
            'created_by'   => auth('owner')->id(),
        ]);

        return redirect()->route('owner.inyandiko_zabamenyi')->with('success', 'Inyandiko yashyizweho neza.');
    }

    public function updateInyandiko(Request $request, $id)
    {
        $item = Inyandiko::findOrFail($id);

        $request->validate([
            'title'    => 'required|string|max:255',
            'category' => 'nullable|string|max:100',
            'author'   => 'nullable|string|max:255',
            'summary'  => 'nullable|string|max:500',
            'content'  => 'nullable|string',
            'status'   => 'required|in:draft,published',
            'image'    => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
            'file'     => 'nullable|mimes:pdf,doc,docx|max:20480',
        ]);

        $imageName = $item->image;
        if ($request->hasFile('image')) {
            $this->deleteUploadedFile($item->image, 'inyandiko');
            $imageName = $this->storeUploadedFile($request->file('image'), 'inyandiko');
        }

        $fileName = $item->file;
        if ($request->hasFile('file')) {
            $this->deleteUploadedFile($item->file, 'inyandiko/files');
            $fileName = $this->storeUploadedFile($request->file('file'), 'inyandiko/files');
        }

        $item->update([
            'title'        => $request->title,
            'category'     => $request->category,
            'author'       => $request->author,
            'summary'      => $request->summary,
            'content'      => $request->content,
            'image'        => $imageName,
            'file'         => $fileName,
            'status'       => $request->status,
            'published_at' => $request->status === 'published' ? ($item->published_at ?? now()) : null,
            'updated_by'   => auth('owner')->id(),
        ]);

        return redirect()->route('owner.inyandiko_zabamenyi')->with('success', 'Inyandiko yahinduwe neza.');
    }

    public function destroyInyandiko($id)
    {
        $item = Inyandiko::findOrFail($id);

        $this->deleteUploadedFile($item->image, 'inyandiko');
        $this->deleteUploadedFile($item->file, 'inyandiko/files');

        $item->delete();

        return redirect()->route('owner.inyandiko_zabamenyi')->with('success', 'Inyandiko yasibwe.');
    }

    /**
     * Amatangazo CRUD
     */
    public function amatangazo()
    {
        $amatangazo = Amatangazo::latest()->paginate(10);

        return view('Users.admin.amatangazo', compact('amatangazo'));
    }

    public function storeAmatangazo(Request $request)
    {
        $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string',
            'presenter'   => 'nullable|string|max:255',
            'status'      => 'required|in:live,upcoming,done',
            'image'       => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
            'is_published'=> 'nullable|boolean',
        ]);

        $imageName = $this->storeUploadedFile($request->file('image'), 'amatangazo');

        $announcement = Amatangazo::create([
            'title'        => $request->title,
            'description'  => $request->description,
            'presenter'    => $request->presenter,
            'status'       => $request->status,
            'image'        => $imageName,
            'is_published' => $request->boolean('is_published', true),
            'published_at' => now(),
            'created_by'   => auth('owner')->id(),
        ]);

        if ($announcement->is_published) {
            User::whereNotNull('id')->chunk(200, function ($students) use ($announcement) {
                Notification::send($students, new NewAmatangazoNotification($announcement));
            });
        }

        return redirect()
            ->route('owner.amatangazo')
            ->with('success', 'Itangazo ryashyizweho neza.');
    }

    public function updateAmatangazo(Request $request, $id)
    {
        $announcement = Amatangazo::findOrFail($id);

        $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string',
            'presenter'   => 'nullable|string|max:255',
            'status'      => 'required|in:live,upcoming,done',
            'image'       => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
        ]);

        $imageName = $announcement->image;

        if ($request->hasFile('image')) {
            $this->deleteUploadedFile($announcement->image, 'amatangazo');
            $imageName = $this->storeUploadedFile($request->file('image'), 'amatangazo');
        }

        $announcement->update([
            'title'        => $request->title,
            'description'  => $request->description,
            'presenter'    => $request->presenter,
            'status'       => $request->status,
            'image'        => $imageName,
            'is_published' => $request->boolean('is_published', true),
            'updated_by'   => auth('owner')->id(),
        ]);

        return redirect()
            ->route('owner.amatangazo')
            ->with('success', 'Itangazo ryahinduwe neza.');
    }

    public function destroyAmatangazo($id)
    {
        $announcement = Amatangazo::findOrFail($id);

        $this->deleteUploadedFile($announcement->image, 'amatangazo');

        $announcement->delete();

        return redirect()
            ->route('owner.amatangazo')
            ->with('success', 'Itangazo ryasibwe.');
    }

    public function togglePublishAmatangazo($id)
    {
        $announcement = Amatangazo::findOrFail($id);
        $announcement->update(['is_published' => !$announcement->is_published]);

        return redirect()
            ->route('owner.amatangazo')
            ->with('success', $announcement->is_published ? 'Itangazo ryerekanwa.' : 'Itangazo ryahishwe.');
    }

    public function ibitabo(){
        return view('Users.admin.ibitabo');
    }

    public function storeBook(Request $request)
    {
        $request->validate([
            'title'       => 'required|string|max:255',
            'author'      => 'nullable|string|max:255',
            'category'    => 'nullable|string|max:100',
            'description' => 'nullable|string',
            'book'        => 'required|mimes:pdf|max:51200',
            'cover_image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
            'status'      => 'required|in:draft,published',
        ]);

        $bookName = $this->storeUploadedFile($request->file('book'), 'books');
        $coverName = $this->storeUploadedFile($request->file('cover_image'), 'books/covers');

        Book::create([
            'title'           => $request->title,
            'author'          => $request->author,
            'category'        => $request->category,
            'description'     => $request->description,
            'book'            => $bookName,
            'cover_image'     => $coverName,
            'status'          => $request->status,
            'is_downloadable' => $request->boolean('is_downloadable', true),
            'created_by'      => auth('owner')->id(),
            'published_at'    => $request->status === 'published' ? now() : null,
        ]);

        return redirect()
                ->back()
                ->with('success','Book uploaded successfully.');
    }

    /**
     * View Books (with optional search)
     */
    public function viewBooks(Request $request)
    {
        $search = $request->input('search');

        $books = Book::when($search, function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('author', 'like', "%{$search}%")
                  ->orWhere('category', 'like', "%{$search}%");
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('Users.admin.ViewBooks', compact('books', 'search'));
    }

    public function updateBook(Request $request, $id)
    {
        $book = Book::findOrFail($id);

        $request->validate([
            'title'       => 'required|string|max:255',
            'author'      => 'nullable|string|max:255',
            'category'    => 'nullable|string|max:100',
            'description' => 'nullable|string',
            'book'        => 'nullable|mimes:pdf|max:51200',
            'cover_image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
            'status'      => 'required|in:draft,published',
        ]);

        $bookName = $book->book;

        if ($request->hasFile('book')) {
            $this->deleteUploadedFile($book->book, 'books');
            $bookName = $this->storeUploadedFile($request->file('book'), 'books');
        }

        $coverName = $book->cover_image;

        if ($request->hasFile('cover_image')) {
            $this->deleteUploadedFile($book->cover_image, 'books/covers');
            $coverName = $this->storeUploadedFile($request->file('cover_image'), 'books/covers');
        }

        $book->update([
            'title'           => $request->title,
            'author'          => $request->author,
            'category'        => $request->category,
            'description'     => $request->description,
            'book'            => $bookName,
            'cover_image'     => $coverName,
            'status'          => $request->status,
            'is_downloadable' => $request->boolean('is_downloadable', true),
            'updated_by'      => auth('owner')->id(),
            'published_at'    => $request->status === 'published' ? ($book->published_at ?? now()) : null,
        ]);

        return redirect()->route('owner.viewBooks')->with('success', 'Book updated successfully.');
    }

    /**
     * Delete Book
     */
    public function destroyBook($id)
    {
        $book = Book::findOrFail($id);

        $this->deleteUploadedFile($book->book, 'books');
        $this->deleteUploadedFile($book->cover_image, 'books/covers');

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
            $imageName = $this->storeUploadedFile($request->file('image'), 'images/users');
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
            if ($user->image && $user->image !== 'user.png') {
                $this->deleteUploadedFile($user->image, 'images/users');
            }
            $imageName = $this->storeUploadedFile($request->file('image'), 'images/users');
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
            $this->deleteUploadedFile($user->image, 'images/users');
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
