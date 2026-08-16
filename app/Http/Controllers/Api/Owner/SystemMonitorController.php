<?php

namespace App\Http\Controllers\Api\Owner;

use App\Http\Controllers\Controller;
use App\Models\Amatangazo;
use App\Models\Book;
use App\Models\Course;
use App\Models\DarsatTable;
use App\Models\Inyandiko;
use App\Models\Owner;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

/**
 * System Monitoring phase: ported as-is - the existing controller is
 * genuinely read-only (its storage check writes and immediately deletes
 * a tiny probe file) and already defensively coded. No changes needed
 * beyond the port itself.
 */
class SystemMonitorController extends Controller
{
    public function index()
    {
        return response()->json([
            'success' => true, 'message' => null,
            'data' => [
                'checks' => [
                    'database' => $this->checkDatabase(),
                    'cache' => $this->checkCache(),
                    'storage' => $this->checkStorage(),
                ],
                'config' => [
                    'app_env' => config('app.env'),
                    'app_debug' => config('app.debug'),
                    'php_version' => PHP_VERSION,
                    'laravel_version' => app()->version(),
                    'cache_driver' => config('cache.default'),
                    'queue_driver' => config('queue.default'),
                    'session_driver' => config('session.driver'),
                    'filesystem_disk' => config('filesystems.default'),
                ],
                'disk' => $this->diskUsage(),
                'recent_errors' => $this->recentLogErrors(),
                'counts' => [
                    'students' => User::count(), 'owners' => Owner::count(),
                    'darsat' => DarsatTable::count(), 'books' => Book::count(),
                    'inyandiko' => Inyandiko::count(), 'amatangazo' => Amatangazo::count(),
                    'courses' => Course::count(),
                ],
            ],
        ]);
    }

    private function checkDatabase(): array
    {
        $start = microtime(true);
        try {
            DB::select('select 1');
            $ms = round((microtime(true) - $start) * 1000, 1);
            return ['status' => 'ok', 'message' => "Query responded in {$ms}ms", 'connection' => config('database.default')];
        } catch (\Throwable $e) {
            return ['status' => 'fail', 'message' => $e->getMessage(), 'connection' => config('database.default')];
        }
    }

    private function checkCache(): array
    {
        try {
            $key = 'system_monitor_check_'.time();
            Cache::put($key, 'ok', 10);
            $value = Cache::get($key);
            Cache::forget($key);
            if ($value === 'ok') {
                return ['status' => 'ok', 'message' => 'Write/read round-trip succeeded', 'driver' => config('cache.default')];
            }
            return ['status' => 'fail', 'message' => 'Cache write succeeded but read returned unexpected value', 'driver' => config('cache.default')];
        } catch (\Throwable $e) {
            return ['status' => 'fail', 'message' => $e->getMessage(), 'driver' => config('cache.default')];
        }
    }

    private function checkStorage(): array
    {
        try {
            $path = 'system_monitor_check.txt';
            Storage::disk('public')->put($path, 'ok:'.now());
            $exists = Storage::disk('public')->exists($path);
            Storage::disk('public')->delete($path);
            if ($exists) {
                return ['status' => 'ok', 'message' => 'public disk is writable', 'disk' => config('filesystems.default')];
            }
            return ['status' => 'fail', 'message' => 'Write succeeded but file was not found afterward', 'disk' => config('filesystems.default')];
        } catch (\Throwable $e) {
            return ['status' => 'fail', 'message' => $e->getMessage(), 'disk' => config('filesystems.default')];
        }
    }

    private function diskUsage(): ?array
    {
        $path = storage_path();
        if (! function_exists('disk_free_space') || @disk_free_space($path) === false) {
            return null;
        }
        $free = disk_free_space($path);
        $total = disk_total_space($path);
        $used = $total - $free;
        return [
            'free_gb' => round($free / 1073741824, 2), 'total_gb' => round($total / 1073741824, 2),
            'used_gb' => round($used / 1073741824, 2), 'used_pct' => $total > 0 ? round(($used / $total) * 100, 1) : 0,
        ];
    }

    private function recentLogErrors(int $limit = 15): array
    {
        $logPath = storage_path('logs/laravel.log');
        if (! file_exists($logPath)) {
            return [];
        }
        $maxBytes = 500000;
        $size = filesize($logPath);
        $handle = fopen($logPath, 'r');
        fseek($handle, max(0, $size - $maxBytes));
        $tail = fread($handle, $maxBytes);
        fclose($handle);
        $lines = explode("\n", $tail);
        $errors = [];
        foreach (array_reverse($lines) as $line) {
            if (preg_match('/\.(ERROR|CRITICAL|EMERGENCY|ALERT)\:/', $line)) {
                $errors[] = ['level' => 'error', 'line' => mb_substr($line, 0, 300)];
            }
            if (count($errors) >= $limit) {
                break;
            }
        }
        return $errors;
    }
}
