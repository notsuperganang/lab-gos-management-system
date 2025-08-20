<?php

namespace App\Services;

use App\Models\BorrowRequest;
use App\Models\VisitRequest;
use App\Models\TestingRequest;
use App\Models\Equipment;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

class DashboardService
{
    /**
     * Get comprehensive dashboard statistics with optimized queries
     */
    public function getDashboardStats(string $dateFrom, string $dateTo, bool $refreshCache = false): array
    {
        $cacheKey = 'dashboard_stats_' . md5($dateFrom . $dateTo);
        $cacheDuration = 300; // 5 minutes

        if ($refreshCache) {
            Cache::forget($cacheKey);
        }

        return Cache::remember($cacheKey, $cacheDuration, function () use ($dateFrom, $dateTo) {
            return [
                'summary' => $this->getSummaryStats(),
                'equipment_analytics' => $this->getEquipmentAnalytics(),
                'request_analytics' => $this->getRequestAnalytics($dateFrom, $dateTo),
                'trend_data' => $this->getTrendData($dateFrom, $dateTo),
                'quick_insights' => $this->getQuickInsights($dateFrom, $dateTo),
                'alerts' => $this->getSystemAlerts(),
            ];
        });
    }

    /**
     * Get real-time summary statistics
     */
    private function getSummaryStats(): array
    {
        // Use single queries with subqueries for efficiency
        $pendingCounts = DB::table(DB::raw('(
            SELECT "borrow" as type, COUNT(*) as count FROM borrow_requests WHERE status = "pending"
            UNION ALL
            SELECT "visit" as type, COUNT(*) as count FROM visit_requests WHERE status = "pending"
            UNION ALL
            SELECT "testing" as type, COUNT(*) as count FROM testing_requests WHERE status = "pending"
        ) as pending_counts'))
            ->selectRaw('SUM(count) as total_pending, SUM(CASE WHEN type = "borrow" THEN count ELSE 0 END) as pending_borrow, SUM(CASE WHEN type = "visit" THEN count ELSE 0 END) as pending_visit, SUM(CASE WHEN type = "testing" THEN count ELSE 0 END) as pending_testing')
            ->first();

        $activeCounts = DB::table(DB::raw('(
            SELECT "borrow" as type, COUNT(*) as count FROM borrow_requests WHERE status IN ("approved", "active")
            UNION ALL
            SELECT "visit" as type, COUNT(*) as count FROM visit_requests WHERE status IN ("approved", "ready")
            UNION ALL
            SELECT "testing" as type, COUNT(*) as count FROM testing_requests WHERE status IN ("in_progress", "analysis")
        ) as active_counts'))
            ->selectRaw('SUM(count) as total_active, SUM(CASE WHEN type = "borrow" THEN count ELSE 0 END) as active_borrow, SUM(CASE WHEN type = "visit" THEN count ELSE 0 END) as active_visit, SUM(CASE WHEN type = "testing" THEN count ELSE 0 END) as active_testing')
            ->first();

        $equipmentStats = Equipment::selectRaw('
            COUNT(*) as total_equipment,
            SUM(available_quantity) as available_equipment,
            ROUND(AVG((total_quantity - available_quantity) / total_quantity * 100), 1) as equipment_utilization_rate
        ')
            ->where('status', 'active')
            ->first();

        $recentActivityCount = ActivityLog::where('created_at', '>=', now()->subDays(7))->count();

        // Calculate trend (compare with last period)
        $lastPeriodPending = DB::table(DB::raw('(
            SELECT COUNT(*) as count FROM borrow_requests WHERE status = "pending" AND created_at < DATE_SUB(NOW(), INTERVAL 7 DAY)
            UNION ALL
            SELECT COUNT(*) as count FROM visit_requests WHERE status = "pending" AND created_at < DATE_SUB(NOW(), INTERVAL 7 DAY)
            UNION ALL
            SELECT COUNT(*) as count FROM testing_requests WHERE status = "pending" AND created_at < DATE_SUB(NOW(), INTERVAL 7 DAY)
        ) as last_period'))
            ->selectRaw('SUM(count) as total')
            ->value('total') ?? 1;

        $pendingTrend = $lastPeriodPending > 0 ? 
            round((($pendingCounts->total_pending - $lastPeriodPending) / $lastPeriodPending) * 100, 1) : 0;

        return [
            'total_pending_requests' => (int) $pendingCounts->total_pending,
            'total_equipment' => (int) $equipmentStats->total_equipment,
            'available_equipment' => (int) $equipmentStats->available_equipment,
            'equipment_utilization_rate' => (float) ($equipmentStats->equipment_utilization_rate ?? 0),
            'recent_activity_count' => $recentActivityCount,
            'pending_trend' => $pendingTrend,
            'total_active_requests' => (int) $activeCounts->total_active,
            'pending_borrow_requests' => (int) $pendingCounts->pending_borrow,
            'active_borrow_requests' => (int) $activeCounts->active_borrow,
            'pending_visit_requests' => (int) $pendingCounts->pending_visit,
            'active_visit_requests' => (int) $activeCounts->active_visit,
            'pending_testing_requests' => (int) $pendingCounts->pending_testing,
            'active_testing_requests' => (int) $activeCounts->active_testing,
        ];
    }

    /**
     * Get equipment analytics with proper relationships
     */
    private function getEquipmentAnalytics(): array
    {
        $totalCount = Equipment::count();

        $availability = Equipment::selectRaw('
            SUM(CASE WHEN available_quantity > 0 THEN 1 ELSE 0 END) as available,
            SUM(CASE WHEN available_quantity = 0 THEN 1 ELSE 0 END) as fully_utilized,
            SUM(CASE WHEN available_quantity <= (total_quantity * 0.2) AND available_quantity > 0 THEN 1 ELSE 0 END) as low_stock
        ')
            ->where('status', 'active')
            ->first();

        $statusDistribution = Equipment::selectRaw('
            status,
            COUNT(*) as count
        ')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        return [
            'total_count' => $totalCount,
            'availability' => [
                'available' => (int) ($availability->available ?? 0),
                'fully_utilized' => (int) ($availability->fully_utilized ?? 0),
                'low_stock' => (int) ($availability->low_stock ?? 0),
            ],
            'status_distribution' => [
                'active' => (int) ($statusDistribution['active'] ?? 0),
                'maintenance' => (int) ($statusDistribution['maintenance'] ?? 0),
                'retired' => (int) ($statusDistribution['retired'] ?? 0),
            ],
        ];
    }

    /**
     * Get request analytics for the specified period
     */
    private function getRequestAnalytics(string $dateFrom, string $dateTo): array
    {
        $periodSummary = DB::table(DB::raw('(
            SELECT "borrow" as type, COUNT(*) as count FROM borrow_requests WHERE created_at BETWEEN ? AND ?
            UNION ALL
            SELECT "visit" as type, COUNT(*) as count FROM visit_requests WHERE created_at BETWEEN ? AND ?
            UNION ALL
            SELECT "testing" as type, COUNT(*) as count FROM testing_requests WHERE created_at BETWEEN ? AND ?
        ) as period_summary'))
            ->setBindings([$dateFrom, $dateTo, $dateFrom, $dateTo, $dateFrom, $dateTo])
            ->selectRaw('
                SUM(count) as total_requests,
                SUM(CASE WHEN type = "borrow" THEN count ELSE 0 END) as borrow_requests,
                SUM(CASE WHEN type = "visit" THEN count ELSE 0 END) as visit_requests,
                SUM(CASE WHEN type = "testing" THEN count ELSE 0 END) as testing_requests
            ')
            ->first();

        return [
            'period_summary' => [
                'total_requests' => (int) $periodSummary->total_requests,
                'borrow_requests' => (int) $periodSummary->borrow_requests,
                'visit_requests' => (int) $periodSummary->visit_requests,
                'testing_requests' => (int) $periodSummary->testing_requests,
            ],
        ];
    }

    /**
     * Get trend data for charts
     */
    private function getTrendData(string $dateFrom, string $dateTo): array
    {
        $dailyTrends = [];
        $start = Carbon::parse($dateFrom);
        $end = Carbon::parse($dateTo);

        // Get daily trends with a single query per type to avoid N+1
        $borrowTrends = BorrowRequest::selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->whereBetween('created_at', [$dateFrom, $dateTo])
            ->groupBy('date')
            ->pluck('count', 'date');

        $visitTrends = VisitRequest::selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->whereBetween('created_at', [$dateFrom, $dateTo])
            ->groupBy('date')
            ->pluck('count', 'date');

        $testingTrends = TestingRequest::selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->whereBetween('created_at', [$dateFrom, $dateTo])
            ->groupBy('date')
            ->pluck('count', 'date');

        while ($start <= $end) {
            $dateKey = $start->format('Y-m-d');
            $dailyTrends[] = [
                'date' => $dateKey,
                'borrow_requests' => $borrowTrends[$dateKey] ?? 0,
                'visit_requests' => $visitTrends[$dateKey] ?? 0,
                'testing_requests' => $testingTrends[$dateKey] ?? 0,
            ];
            $start->addDay();
        }

        return [
            'daily_trends' => $dailyTrends,
        ];
    }

    /**
     * Get quick insights including most requested equipment
     */
    private function getQuickInsights(string $dateFrom, string $dateTo): array
    {
        $mostRequestedEquipment = DB::table('borrow_request_items')
            ->join('equipment', 'borrow_request_items.equipment_id', '=', 'equipment.id')
            ->join('categories', 'equipment.category_id', '=', 'categories.id')
            ->join('borrow_requests', 'borrow_request_items.borrow_request_id', '=', 'borrow_requests.id')
            ->whereBetween('borrow_requests.created_at', [$dateFrom, $dateTo])
            ->select(
                'equipment.id',
                'equipment.name',
                'categories.name as category',
                DB::raw('COUNT(borrow_request_items.id) as request_count'),
                DB::raw('SUM(borrow_request_items.quantity_requested) as total_quantity'),
                DB::raw('AVG(borrow_request_items.quantity_requested) as avg_quantity')
            )
            ->groupBy('equipment.id', 'equipment.name', 'categories.name')
            ->orderByDesc('request_count')
            ->limit(5)
            ->get()
            ->map(function ($equipment) {
                return [
                    'id' => $equipment->id,
                    'name' => $equipment->name,
                    'category' => $equipment->category,
                    'request_count' => (int) $equipment->request_count,
                    'total_quantity' => (int) $equipment->total_quantity,
                    'avg_quantity' => round((float) $equipment->avg_quantity, 2),
                ];
            })
            ->toArray();

        return [
            'most_requested_equipment' => $mostRequestedEquipment,
        ];
    }

    /**
     * Get system alerts based on current conditions
     */
    private function getSystemAlerts(): array
    {
        $alerts = [];

        // Check for overdue requests
        $overdueRequests = BorrowRequest::where('status', 'pending')
            ->where('created_at', '<', now()->subDays(3))
            ->count();

        if ($overdueRequests > 0) {
            $alerts[] = [
                'type' => 'warning',
                'title' => 'Overdue Requests',
                'message' => "{$overdueRequests} requests have been pending for more than 3 days",
                'count' => $overdueRequests,
                'category' => 'requests',
                'created_at' => now()->format('Y-m-d H:i:s'),
            ];
        }

        // Check for low stock equipment
        $lowStockCount = Equipment::where('status', 'active')
            ->whereRaw('available_quantity <= (total_quantity * 0.1)')
            ->where('available_quantity', '>', 0)
            ->count();

        if ($lowStockCount > 0) {
            $alerts[] = [
                'type' => 'warning',
                'title' => 'Low Stock Alert',
                'message' => "{$lowStockCount} equipment items are running low on stock",
                'count' => $lowStockCount,
                'category' => 'equipment',
                'created_at' => now()->format('Y-m-d H:i:s'),
            ];
        }

        // Check for maintenance overdue
        $maintenanceOverdue = Equipment::where('status', 'active')
            ->where('next_maintenance_date', '<', now()->subDays(7))
            ->whereNotNull('next_maintenance_date')
            ->count();

        if ($maintenanceOverdue > 0) {
            $alerts[] = [
                'type' => 'error',
                'title' => 'Maintenance Overdue',
                'message' => "{$maintenanceOverdue} equipment items require immediate maintenance",
                'count' => $maintenanceOverdue,
                'category' => 'equipment',
                'created_at' => now()->format('Y-m-d H:i:s'),
            ];
        }

        return $alerts;
    }

    /**
     * Get recent activity logs with proper relationships
     */
    public function getRecentActivities(int $limit = 10, array $filters = []): array
    {
        $query = ActivityLog::with('causer:id,name')
            ->latest('created_at');

        // Apply filters
        if (!empty($filters['type'])) {
            $query->where('event', $filters['type']);
        }

        if (!empty($filters['user_id'])) {
            $query->where('causer_id', $filters['user_id']);
        }

        if (!empty($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('description', 'like', "%{$filters['search']}%")
                  ->orWhereHas('causer', function ($userQuery) use ($filters) {
                      $userQuery->where('name', 'like', "%{$filters['search']}%");
                  });
            });
        }

        return $query->limit($limit)
            ->get()
            ->map(function ($log) {
                return [
                    'id' => $log->id,
                    'event' => $log->event,
                    'description' => $log->description,
                    'subject_type' => $log->subject_type,
                    'subject_id' => $log->subject_id,
                    'causer' => $log->causer ? [
                        'id' => $log->causer->id,
                        'name' => $log->causer->name,
                    ] : null,
                    'properties' => $log->properties,
                    'created_at' => $log->created_at->format('Y-m-d H:i:s'),
                    'created_at_human' => $log->created_at->diffForHumans(),
                ];
            })
            ->toArray();
    }
}