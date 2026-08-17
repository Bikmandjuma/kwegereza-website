<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Services\BackupService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BackupController extends Controller
{
    public function __construct(private BackupService $backups)
    {
        $this->middleware('permission:backups.view')->only(['index']);
        $this->middleware('permission:backups.create')->only(['store']);
        $this->middleware('permission:backups.delete')->only(['destroy']);
    }

    public function index()
    {
        $backups = $this->backups->listBackups();

        return view('Users.admin.backups', compact('backups'));
    }

    public function store()
    {
        set_time_limit(120); // a full-table dump can take a moment on a larger DB

        try {
            $filename = $this->backups->createBackup();

            return back()->with('success', "Backup yakozwe: {$filename}");
        } catch (\Throwable $e) {
            return back()->with('error', 'Backup ntiyakunze: ' . $e->getMessage());
        }
    }

    public function download($filename)
    {
        if (!preg_match('/^backup-[\d_-]+\.sql$/', $filename)) {
            abort(404);
        }

        $path = 'backups/' . $filename;

        if (!Storage::disk('local')->exists($path)) {
            abort(404);
        }

        return Storage::disk('local')->download($path);
    }

    public function destroy($filename)
    {
        if ($this->backups->deleteBackup($filename)) {
            return back()->with('success', 'Backup yasibwe.');
        }

        return back()->with('error', 'Ntibyashobotse gusiba iyi backup.');
    }
}
