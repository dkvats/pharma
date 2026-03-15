<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Order;
use App\Models\Module;
use App\Models\ActivityLog;
use App\Services\SystemSettingService;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // System statistics
        $stats = [
            'total_users' => User::count(),
            'active_users' => User::where('status', 'active')->count(),
            'pending_users' => User::where('status', 'pending')->count(),
            'total_doctors' => User::role('Doctor')->count(),
            'total_stores' => User::role('Store')->count(),
            'total_mrs' => User::role('MR')->count(),
            'total_admins' => User::role('Admin')->count() + User::role('Sub Admin')->count(),
            
            'total_orders' => Order::count(),
            'pending_orders' => Order::where('status', 'pending')->count(),
            'delivered_orders' => Order::where('status', 'delivered')->count(),
            
            'total_revenue' => Order::where('status', 'delivered')->sum('total_amount'),
            'monthly_revenue' => Order::where('status', 'delivered')
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->sum('total_amount'),
            
            'active_modules' => Module::where('status', 'active')->count(),
            'inactive_modules' => Module::where('status', 'inactive')->count(),
            'total_modules' => Module::count(),
        ];

        // Active modules
        $modules = Module::orderBy('sort_order')->get();

        // Recent activities
        $recentActivities = ActivityLog::with('user')
            ->latest()
            ->limit(20)
            ->get();

        // System health
        $systemHealth = [
            'php_version' => PHP_VERSION,
            'laravel_version' => app()->version(),
            'database_size' => $this->getDatabaseSize(),
            'storage_usage' => $this->getStorageUsage(),
            'cache_driver' => config('cache.default'),
            'queue_driver' => config('queue.default'),
        ];

        return view('super-admin.dashboard.index', compact('stats', 'modules', 'recentActivities', 'systemHealth'));
    }

    private function getDatabaseSize(): string
    {
        try {
            $result = DB::select("SELECT SUM(page_count * page_size) as size FROM pragma_page_count(), pragma_page_size()");
            $size = $result[0]->size ?? 0;
            return $this->formatBytes($size);
        } catch (\Exception $e) {
            return 'Unknown';
        }
    }

    private function getStorageUsage(): string
    {
        try {
            $path = storage_path();
            $size = $this->folderSize($path);
            return $this->formatBytes($size);
        } catch (\Exception $e) {
            return 'Unknown';
        }
    }

    private function folderSize(string $path): int
    {
        $size = 0;
        foreach (glob(rtrim($path, '/') . '/*', GLOB_NOSORT) as $each) {
            $size += is_dir($each) ? $this->folderSize($each) : filesize($each);
        }
        return $size;
    }

    private function formatBytes(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        return round($bytes / pow(1024, $pow), 2) . ' ' . $units[$pow];
    }
}
