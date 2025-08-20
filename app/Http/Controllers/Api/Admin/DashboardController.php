<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\ApiResponse;
use App\Models\BorrowRequest;
use App\Models\VisitRequest;
use App\Models\TestingRequest;
use App\Models\Equipment;
use App\Models\User;
use App\Models\ActivityLog;
use App\Models\Notification;
use App\Models\BorrowRequestItem;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Get comprehensive dashboard statistics with caching
     */
    public function statistics(Request $request): JsonResponse
    {
        try {
            // Date range for filtering
            $dateFrom = $request->get('date_from', now()->subDays(30)->format('Y-m-d'));
            $dateTo = $request->get('date_to', now()->format('Y-m-d'));
            $refreshCache = $request->boolean('refresh_cache', false);

            // Create cache key based on parameters
            $cacheKey = 'dashboard_stats_' . md5($dateFrom . $dateTo);
            $cacheDuration = 300; // 5 minutes

            if ($refreshCache) {
                Cache::forget($cacheKey);
            }

            $stats = Cache::remember($cacheKey, $cacheDuration, function () use ($dateFrom, $dateTo) {
                return [
                    'summary' => $this->getRealTimeCounters(),
                    'equipment_analytics' => $this->getEquipmentAnalytics(),
                    'request_analytics' => $this->getRequestAnalytics($dateFrom, $dateTo),
                    'trend_data' => $this->getTrendData($dateFrom, $dateTo),
                    'peak_analysis' => $this->getPeakAnalysis(),
                    'quick_insights' => $this->getQuickInsights($dateFrom, $dateTo),
                    'performance_metrics' => $this->getPerformanceMetrics($dateFrom, $dateTo),
                    'alerts' => $this->getSystemAlerts(),
                ];
            });

            // Add non-cached real-time data
            $stats['realtime'] = [
                'timestamp' => now()->format('Y-m-d H:i:s'),
                'pending_actions_count' => $this->getPendingActionsCount(),
                'system_health' => $this->getSystemHealth(),
            ];

            $stats['meta'] = [
                'cached' => !$refreshCache,
                'cache_key' => $cacheKey,
                'generated_at' => now()->format('Y-m-d H:i:s'),
                'filters' => [
                    'date_from' => $dateFrom,
                    'date_to' => $dateTo,
                ],
            ];

            return ApiResponse::success($stats, 'Dashboard statistics retrieved successfully');

        } catch (\Exception $e) {
            return ApiResponse::error('Failed to retrieve dashboard statistics', 500, null, $e->getMessage());
        }
    }

    /**
     * Get enhanced activity logs with improved filtering and analytics
     */
    public function activityLogs(Request $request): JsonResponse
    {
        try {
            // Enhanced caching for activity logs
            $cacheKey = 'activity_logs_' . md5(serialize($request->all()));
            $cacheDuration = 60; // 1 minute cache for activity logs

            if ($request->boolean('refresh_cache', false)) {
                Cache::forget($cacheKey);
            }

            $result = Cache::remember($cacheKey, $cacheDuration, function () use ($request) {
                $query = ActivityLog::query();

                // Enhanced filtering
                $this->applyActivityLogFilters($query, $request);

                // Default ordering
                $sortBy = $request->get('sort_by', 'created_at');
                $sortDirection = $request->get('sort_direction', 'desc');
                $query->orderBy($sortBy, $sortDirection);

                // Pagination
                $perPage = min($request->get('per_page', 20), 100);
                $activityLogs = $query->paginate($perPage);

                // Enhanced data transformation
                $activityData = $this->transformActivityLogs($activityLogs->getCollection());

                return [
                    'logs' => $activityLogs,
                    'transformed_data' => $activityData,
                    'analytics' => $this->getActivityAnalytics($request),
                ];
            });
            
            return ApiResponse::paginated(
                $result['logs'],
                null,
                'Activity logs retrieved successfully',
                [
                    'filters' => $this->getAppliedFilters($request),
                    'available_actions' => $this->getAvailableActions(),
                    'available_models' => $this->getAvailableModels(),
                    'analytics' => $result['analytics'],
                    'export_options' => $this->getExportOptions(),
                    'cached' => !$request->boolean('refresh_cache', false),
                ],
                $result['transformed_data']
            );
            
        } catch (\Exception $e) {
            return ApiResponse::error('Failed to retrieve activity logs', 500, null, $e->getMessage());
        }
    }
    
    /**
     * Apply enhanced filters to activity log query
     */
    private function applyActivityLogFilters($query, Request $request): void
    {
        // User filter
        if ($request->filled('user_id')) {
            $query->where('causer_id', $request->get('user_id'));
        }
        
        // Action filter
        if ($request->filled('action') || $request->filled('event')) {
            $action = $request->get('action') ?: $request->get('event');
            $query->where('event', 'like', "%{$action}%");
        }
        
        // Model type filter
        if ($request->filled('model_type') || $request->filled('subject_type')) {
            $modelType = $request->get('model_type') ?: $request->get('subject_type');
            $query->where('subject_type', 'like', "%{$modelType}%");
        }
        
        // Enhanced date filtering
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->get('date_from'));
        }
        
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->get('date_to'));
        }
        
        // Time range filters
        if ($request->filled('time_range')) {
            $this->applyTimeRangeFilter($query, $request->get('time_range'));
        }
        
        // Priority/importance filter
        if ($request->filled('importance')) {
            $this->applyImportanceFilter($query, $request->get('importance'));
        }
        
        // Enhanced search
        if ($request->filled('search')) {
            $this->applyAdvancedSearch($query, $request->get('search'));
        }
        
        // IP address filter
        if ($request->filled('ip_address')) {
            $query->whereJsonContains('properties->ip_address', $request->get('ip_address'));
        }
        
        // Admin actions only filter
        if ($request->boolean('admin_only', false)) {
            $query->whereHas('causer', function($q) {
                $q->whereIn('role', ['admin', 'super_admin']);
            });
        }
    }
    
    /**
     * Apply time range filter
     */
    private function applyTimeRangeFilter($query, string $timeRange): void
    {
        $now = now();
        
        switch ($timeRange) {
            case 'today':
                $query->whereDate('created_at', $now->toDateString());
                break;
            case 'yesterday':
                $query->whereDate('created_at', $now->subDay()->toDateString());
                break;
            case 'this_week':
                $query->whereBetween('created_at', [$now->startOfWeek(), $now->endOfWeek()]);
                break;
            case 'last_week':
                $start = $now->subWeek()->startOfWeek();
                $end = $now->subWeek()->endOfWeek();
                $query->whereBetween('created_at', [$start, $end]);
                break;
            case 'this_month':
                $query->whereMonth('created_at', $now->month)
                      ->whereYear('created_at', $now->year);
                break;
            case 'last_month':
                $lastMonth = $now->subMonth();
                $query->whereMonth('created_at', $lastMonth->month)
                      ->whereYear('created_at', $lastMonth->year);
                break;
            case 'last_24h':
                $query->where('created_at', '>=', $now->subDay());
                break;
            case 'last_7d':
                $query->where('created_at', '>=', $now->subDays(7));
                break;
            case 'last_30d':
                $query->where('created_at', '>=', $now->subDays(30));
                break;
        }
    }
    
    /**
     * Apply importance filter based on action type
     */
    private function applyImportanceFilter($query, string $importance): void
    {
        $importanceMap = [
            'critical' => ['deleted', 'rejected', 'cancelled', 'maintenance'],
            'high' => ['approved', 'completed', 'activated'],
            'medium' => ['updated', 'created'],
            'low' => ['viewed', 'accessed', 'login'],
        ];
        
        if (isset($importanceMap[$importance])) {
            $events = $importanceMap[$importance];
            $query->where(function($q) use ($events) {
                foreach ($events as $event) {
                    $q->orWhere('event', 'like', "%{$event}%");
                }
            });
        }
    }
    
    /**
     * Apply advanced search across multiple fields
     */
    private function applyAdvancedSearch($query, string $search): void
    {
        $query->where(function ($q) use ($search) {
            $q->where('description', 'like', "%{$search}%")
              ->orWhere('event', 'like', "%{$search}%")
              ->orWhere('subject_type', 'like', "%{$search}%")
              ->orWhereJsonContains('properties', $search)
              ->orWhereHas('causer', function ($userQuery) use ($search) {
                  $userQuery->where('name', 'like', "%{$search}%")
                           ->orWhere('email', 'like', "%{$search}%");
              });
        });
    }
    
    /**
     * Transform activity logs with enhanced data
     */
    private function transformActivityLogs($logs): array
    {
        return $logs->map(function ($log) {
            $properties = is_string($log->properties) ? json_decode($log->properties, true) : $log->properties;
            
            return [
                'id' => $log->id,
                'event' => $log->event,
                'description' => $log->description,
                'subject_type' => $log->subject_type,
                'subject_id' => $log->subject_id,
                'causer_type' => $log->causer_type,
                'causer_id' => $log->causer_id,
                'properties' => $properties,
                'created_at' => $log->created_at->format('Y-m-d H:i:s'),
                'created_at_human' => $log->created_at->diffForHumans(),
                'user' => $log->causer ? [
                    'id' => $log->causer->id,
                    'name' => $log->causer->name,
                    'email' => $log->causer->email,
                    'role' => $log->causer->role ?? 'user',
                ] : null,
                'importance' => $this->getLogImportance($log->event),
                'category' => $this->getLogCategory($log->subject_type, $log->event),
                'icon' => $this->getLogIcon($log->event),
                'color' => $this->getLogColor($log->event),
            ];
        })->toArray();
    }
    
    /**
     * Get activity analytics
     */
    private function getActivityAnalytics(Request $request): array
    {
        $dateFrom = $request->get('date_from', now()->subDays(7)->format('Y-m-d'));
        $dateTo = $request->get('date_to', now()->format('Y-m-d'));
        
        // Activity summary
        $totalActivities = ActivityLog::whereBetween('created_at', [$dateFrom, $dateTo])->count();
        
        // Top users by activity
        $topUsers = ActivityLog::whereBetween('created_at', [$dateFrom, $dateTo])
            ->whereNotNull('causer_id')
            ->with('causer')
            ->select('causer_id', DB::raw('count(*) as activity_count'))
            ->groupBy('causer_id')
            ->orderByDesc('activity_count')
            ->limit(5)
            ->get()
            ->map(function($item) {
                return [
                    'user_id' => $item->causer_id,
                    'user_name' => $item->causer->name ?? 'Unknown',
                    'user_email' => $item->causer->email ?? '',
                    'activity_count' => $item->activity_count,
                ];
            });
        
        // Activity by type
        $activityByType = ActivityLog::whereBetween('created_at', [$dateFrom, $dateTo])
            ->select('subject_type', DB::raw('count(*) as count'))
            ->groupBy('subject_type')
            ->orderByDesc('count')
            ->get()
            ->mapWithKeys(function($item) {
                $modelName = class_basename($item->subject_type);
                return [$modelName => $item->count];
            });
        
        // Activity by event
        $activityByEvent = ActivityLog::whereBetween('created_at', [$dateFrom, $dateTo])
            ->select('event', DB::raw('count(*) as count'))
            ->groupBy('event')
            ->orderByDesc('count')
            ->limit(10)
            ->get()
            ->pluck('count', 'event');
        
        // Daily activity trend
        $dailyTrend = [];
        $start = Carbon::parse($dateFrom);
        $end = Carbon::parse($dateTo);
        
        while ($start <= $end) {
            $dayStart = $start->copy()->startOfDay();
            $dayEnd = $start->copy()->endOfDay();
            
            $count = ActivityLog::whereBetween('created_at', [$dayStart, $dayEnd])->count();
            
            $dailyTrend[] = [
                'date' => $start->format('Y-m-d'),
                'day_name' => $start->format('D'),
                'activity_count' => $count,
            ];
            
            $start->addDay();
        }
        
        return [
            'summary' => [
                'total_activities' => $totalActivities,
                'avg_daily_activities' => round($totalActivities / max(1, $start->diffInDays($end)), 1),
                'unique_users' => ActivityLog::whereBetween('created_at', [$dateFrom, $dateTo])
                    ->distinct('causer_id')->count('causer_id'),
            ],
            'top_users' => $topUsers,
            'activity_by_type' => $activityByType,
            'activity_by_event' => $activityByEvent,
            'daily_trend' => $dailyTrend,
        ];
    }
    
    /**
     * Get applied filters for response
     */
    private function getAppliedFilters(Request $request): array
    {
        return [
            'user_id' => $request->get('user_id'),
            'action' => $request->get('action'),
            'event' => $request->get('event'),
            'model_type' => $request->get('model_type'),
            'subject_type' => $request->get('subject_type'),
            'date_from' => $request->get('date_from'),
            'date_to' => $request->get('date_to'),
            'time_range' => $request->get('time_range'),
            'importance' => $request->get('importance'),
            'search' => $request->get('search'),
            'ip_address' => $request->get('ip_address'),
            'admin_only' => $request->boolean('admin_only', false),
            'sort_by' => $request->get('sort_by', 'created_at'),
            'sort_direction' => $request->get('sort_direction', 'desc'),
        ];
    }
    
    /**
     * Get export options
     */
    private function getExportOptions(): array
    {
        return [
            'formats' => ['csv', 'excel', 'pdf'],
            'fields' => [
                'created_at' => 'Date & Time',
                'event' => 'Event',
                'description' => 'Description',
                'user.name' => 'User Name',
                'user.email' => 'User Email',
                'subject_type' => 'Model Type',
                'properties' => 'Properties',
            ],
        ];
    }
    
    /**
     * Get log importance level
     */
    private function getLogImportance(string $event): string
    {
        $criticalEvents = ['deleted', 'rejected', 'cancelled', 'failed'];
        $highEvents = ['approved', 'completed', 'activated', 'suspended'];
        $mediumEvents = ['updated', 'created', 'modified'];
        
        foreach ($criticalEvents as $critical) {
            if (str_contains(strtolower($event), $critical)) {
                return 'critical';
            }
        }
        
        foreach ($highEvents as $high) {
            if (str_contains(strtolower($event), $high)) {
                return 'high';
            }
        }
        
        foreach ($mediumEvents as $medium) {
            if (str_contains(strtolower($event), $medium)) {
                return 'medium';
            }
        }
        
        return 'low';
    }
    
    /**
     * Get log category
     */
    private function getLogCategory(?string $subjectType, string $event): string
    {
        if (!$subjectType) return 'system';
        
        $modelName = class_basename($subjectType);
        
        return match($modelName) {
            'BorrowRequest', 'VisitRequest', 'TestingRequest' => 'requests',
            'Equipment' => 'equipment',
            'User' => 'users',
            'Article', 'Gallery' => 'content',
            default => 'system',
        };
    }
    
    /**
     * Get log icon
     */
    private function getLogIcon(string $event): string
    {
        return match(true) {
            str_contains($event, 'created') => 'plus-circle',
            str_contains($event, 'updated') => 'edit',
            str_contains($event, 'deleted') => 'trash',
            str_contains($event, 'approved') => 'check-circle',
            str_contains($event, 'rejected') => 'x-circle',
            str_contains($event, 'completed') => 'check',
            str_contains($event, 'cancelled') => 'x',
            str_contains($event, 'login') => 'log-in',
            str_contains($event, 'logout') => 'log-out',
            default => 'activity',
        };
    }
    
    /**
     * Get log color
     */
    private function getLogColor(string $event): string
    {
        return match(true) {
            str_contains($event, 'created') => 'green',
            str_contains($event, 'updated') => 'blue',
            str_contains($event, 'deleted') => 'red',
            str_contains($event, 'approved') => 'green',
            str_contains($event, 'rejected') => 'red',
            str_contains($event, 'completed') => 'green',
            str_contains($event, 'cancelled') => 'orange',
            str_contains($event, 'failed') => 'red',
            default => 'gray',
        };
    }

    /**
     * Get enhanced notifications with system-generated alerts
     */
    public function notifications(Request $request): JsonResponse
    {
        try {
            // Enhanced caching for notifications
            $cacheKey = 'notifications_' . md5(serialize($request->only(['type', 'is_read', 'priority', 'search'])));
            $cacheDuration = 120; // 2 minutes cache
            
            if ($request->boolean('refresh_cache', false)) {
                Cache::forget($cacheKey);
            }
            
            $result = Cache::remember($cacheKey, $cacheDuration, function () use ($request) {
                $query = Notification::orderBy('created_at', 'desc');
                
                // Enhanced filtering
                $this->applyNotificationFilters($query, $request);
                
                $perPage = min($request->get('per_page', 15), 100);
                $notifications = $query->paginate($perPage);
                
                // Transform data with enhanced information
                $notificationData = $this->transformNotifications($notifications->getCollection());
                
                return [
                    'notifications' => $notifications,
                    'transformed_data' => $notificationData,
                ];
            });
            
            // Generate real-time system notifications (not cached)
            $systemNotifications = $this->generateEnhancedSystemNotifications();
            $criticalAlerts = $this->getCriticalAlerts();
            
            return ApiResponse::paginated(
                $result['notifications'],
                null,
                'Notifications retrieved successfully',
                [
                    'filters' => $this->getNotificationFilters($request),
                    'statistics' => $this->getNotificationStatistics(),
                    'system_notifications' => $systemNotifications,
                    'critical_alerts' => $criticalAlerts,
                    'available_types' => $this->getNotificationTypes(),
                    'available_priorities' => $this->getNotificationPriorities(),
                    'notification_settings' => $this->getNotificationSettings(),
                    'cached' => !$request->boolean('refresh_cache', false),
                ],
                $result['transformed_data']
            );
            
        } catch (\Exception $e) {
            return ApiResponse::error('Failed to retrieve notifications', 500, null, $e->getMessage());
        }
    }
    
    /**
     * Apply enhanced notification filters
     */
    private function applyNotificationFilters($query, Request $request): void
    {
        // Type filter
        if ($request->filled('type')) {
            $query->where('type', $request->get('type'));
        }
        
        // Read status filter
        if ($request->filled('is_read')) {
            if ($request->boolean('is_read')) {
                $query->whereNotNull('read_at');
            } else {
                $query->whereNull('read_at');
            }
        }
        
        // Priority filter
        if ($request->filled('priority')) {
            $query->whereJsonContains('data->priority', $request->get('priority'));
        }
        
        // Date range filter
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->get('date_from'));
        }
        
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->get('date_to'));
        }
        
        // Enhanced search
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('type', 'like', "%{$search}%")
                  ->orWhereJsonContains('data->title', $search)
                  ->orWhereJsonContains('data->message', $search)
                  ->orWhereJsonContains('data->description', $search);
            });
        }
    }
    
    /**
     * Transform notifications with enhanced data
     */
    private function transformNotifications($notifications): array
    {
        return $notifications->map(function ($notification) {
            $data = is_string($notification->data) ? json_decode($notification->data, true) : $notification->data;
            
            return [
                'id' => $notification->id,
                'type' => $notification->type,
                'title' => $data['title'] ?? 'Notification',
                'message' => $data['message'] ?? 'You have a new notification',
                'description' => $data['description'] ?? null,
                'data' => $data,
                'priority' => $data['priority'] ?? 'medium',
                'category' => $this->getNotificationCategory($notification->type),
                'is_read' => !is_null($notification->read_at),
                'read_at' => $notification->read_at?->format('Y-m-d H:i:s'),
                'created_at' => $notification->created_at->format('Y-m-d H:i:s'),
                'created_at_human' => $notification->created_at->diffForHumans(),
                'priority_label' => $this->getPriorityLabel($data['priority'] ?? 'medium'),
                'priority_color' => $this->getPriorityColor($data['priority'] ?? 'medium'),
                'action_url' => $data['action_url'] ?? null,
                'icon' => $this->getNotificationIcon($notification->type),
                'expires_at' => isset($data['expires_at']) ? Carbon::parse($data['expires_at'])->format('Y-m-d H:i:s') : null,
                'is_dismissible' => $data['dismissible'] ?? true,
            ];
        })->toArray();
    }
    
    /**
     * Generate enhanced system notifications
     */
    private function generateEnhancedSystemNotifications(): array
    {
        $notifications = [];
        
        // Priority system alerts
        $this->addPendingRequestAlerts($notifications);
        $this->addEquipmentAlerts($notifications);
        $this->addSystemHealthAlerts($notifications);
        $this->addPerformanceAlerts($notifications);
        $this->addSecurityAlerts($notifications);
        
        // Sort by priority
        usort($notifications, function($a, $b) {
            $priorityOrder = ['urgent' => 4, 'high' => 3, 'medium' => 2, 'low' => 1];
            return ($priorityOrder[$b['priority']] ?? 0) <=> ($priorityOrder[$a['priority']] ?? 0);
        });
        
        return array_slice($notifications, 0, 10); // Limit to top 10
    }
    
    /**
     * Add pending request alerts
     */
    private function addPendingRequestAlerts(array &$notifications): void
    {
        // Overdue requests
        $overdueRequests = BorrowRequest::where('status', 'pending')
            ->where('created_at', '<=', now()->subDays(2))
            ->count();
            
        if ($overdueRequests > 0) {
            $notifications[] = [
                'type' => 'overdue_requests',
                'priority' => $overdueRequests > 5 ? 'urgent' : 'high',
                'title' => 'Overdue Pending Requests',
                'message' => "{$overdueRequests} request(s) pending for over 2 days",
                'description' => 'These requests require immediate attention to maintain service quality.',
                'count' => $overdueRequests,
                'action_url' => '/admin/requests?status=pending&overdue=true',
                'category' => 'requests',
                'created_at' => now()->format('Y-m-d H:i:s'),
                'expires_at' => now()->addHours(6)->format('Y-m-d H:i:s'),
            ];
        }
        
        // High volume of pending requests
        $totalPending = BorrowRequest::where('status', 'pending')->count() +
                       VisitRequest::where('status', 'pending')->count() +
                       TestingRequest::where('status', 'pending')->count();
                       
        if ($totalPending > 20) {
            $notifications[] = [
                'type' => 'high_pending_volume',
                'priority' => 'high',
                'title' => 'High Volume of Pending Requests',
                'message' => "{$totalPending} total pending requests",
                'description' => 'Consider allocating additional resources for request processing.',
                'count' => $totalPending,
                'action_url' => '/admin/requests?status=pending',
                'category' => 'requests',
                'created_at' => now()->format('Y-m-d H:i:s'),
            ];
        }
    }
    
    /**
     * Add equipment alerts
     */
    private function addEquipmentAlerts(array &$notifications): void
    {
        // Critical maintenance due
        $criticalMaintenance = Equipment::where('next_maintenance_date', '<=', now())
            ->where('status', 'active')
            ->count();
            
        if ($criticalMaintenance > 0) {
            $notifications[] = [
                'type' => 'critical_maintenance',
                'priority' => 'urgent',
                'title' => 'Critical Equipment Maintenance Overdue',
                'message' => "{$criticalMaintenance} equipment(s) have overdue maintenance",
                'description' => 'Immediate maintenance required to ensure safety and compliance.',
                'count' => $criticalMaintenance,
                'action_url' => '/admin/equipment?maintenance=overdue',
                'category' => 'equipment',
                'created_at' => now()->format('Y-m-d H:i:s'),
            ];
        }
        
        // Equipment out of stock
        $outOfStock = Equipment::where('status', 'active')
            ->where('available_quantity', 0)
            ->count();
            
        if ($outOfStock > 0) {
            $notifications[] = [
                'type' => 'equipment_out_of_stock',
                'priority' => 'high',
                'title' => 'Equipment Out of Stock',
                'message' => "{$outOfStock} equipment(s) are completely unavailable",
                'description' => 'These items cannot be borrowed until returned or restocked.',
                'count' => $outOfStock,
                'action_url' => '/admin/equipment?availability=none',
                'category' => 'equipment',
                'created_at' => now()->format('Y-m-d H:i:s'),
            ];
        }
    }
    
    /**
     * Add system health alerts
     */
    private function addSystemHealthAlerts(array &$notifications): void
    {
        // High system load (many pending actions)
        $pendingLoad = $this->getPendingActionsCount();
        
        if ($pendingLoad > 50) {
            $notifications[] = [
                'type' => 'high_system_load',
                'priority' => 'medium',
                'title' => 'High System Load Detected',
                'message' => "{$pendingLoad} total pending actions across all modules",
                'description' => 'System performance may be impacted. Consider reviewing processing workflows.',
                'count' => $pendingLoad,
                'action_url' => '/admin/dashboard',
                'category' => 'system',
                'created_at' => now()->format('Y-m-d H:i:s'),
            ];
        }
    }
    
    /**
     * Add performance alerts
     */
    private function addPerformanceAlerts(array &$notifications): void
    {
        // Slow processing times
        $avgProcessingTime = $this->getAverageProcessingTime('borrow', now()->subDays(7)->format('Y-m-d'), now()->format('Y-m-d'));
        
        if ($avgProcessingTime['average_hours'] > 48) {
            $notifications[] = [
                'type' => 'slow_processing',
                'priority' => 'medium',
                'title' => 'Slow Request Processing Detected',
                'message' => "Average processing time: {$avgProcessingTime['average_hours']} hours",
                'description' => 'Consider optimizing approval workflows or adding more admin capacity.',
                'action_url' => '/admin/analytics/performance',
                'category' => 'performance',
                'created_at' => now()->format('Y-m-d H:i:s'),
            ];
        }
    }
    
    /**
     * Add security alerts
     */
    private function addSecurityAlerts(array &$notifications): void
    {
        // Multiple failed login attempts (if implemented)
        // Unusual activity patterns
        // This would depend on your security monitoring implementation
        
        // For now, check for inactive admins
        $inactiveAdmins = User::whereIn('role', ['admin', 'super_admin'])
            ->where('last_login_at', '<=', now()->subDays(30))
            ->count();
            
        if ($inactiveAdmins > 0) {
            $notifications[] = [
                'type' => 'inactive_admins',
                'priority' => 'low',
                'title' => 'Inactive Admin Accounts',
                'message' => "{$inactiveAdmins} admin(s) haven't logged in for 30+ days",
                'description' => 'Consider reviewing admin account access and permissions.',
                'count' => $inactiveAdmins,
                'action_url' => '/admin/users?role=admin&status=inactive',
                'category' => 'security',
                'created_at' => now()->format('Y-m-d H:i:s'),
            ];
        }
    }
    
    /**
     * Get critical alerts that require immediate attention
     */
    private function getCriticalAlerts(): array
    {
        $alerts = [];
        
        // System-critical issues only
        $overdueRequests = BorrowRequest::where('status', 'pending')
            ->where('created_at', '<=', now()->subDays(3))
            ->count();
            
        if ($overdueRequests > 0) {
            $alerts[] = [
                'type' => 'critical_overdue',
                'title' => 'Critical Request Delays',
                'message' => "{$overdueRequests} requests overdue by 3+ days",
                'severity' => 'critical',
                'requires_action' => true,
            ];
        }
        
        return $alerts;
    }
    
    /**
     * Get notification statistics
     */
    private function getNotificationStatistics(): array
    {
        return [
            'total_unread' => Notification::whereNull('read_at')->count(),
            'total_today' => Notification::whereDate('created_at', today())->count(),
            'total_this_week' => Notification::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count(),
            'by_priority' => [
                'urgent' => Notification::whereJsonContains('data->priority', 'urgent')->count(),
                'high' => Notification::whereJsonContains('data->priority', 'high')->count(),
                'medium' => Notification::whereJsonContains('data->priority', 'medium')->count(),
                'low' => Notification::whereJsonContains('data->priority', 'low')->count(),
            ],
            'by_type' => Notification::select('type', DB::raw('count(*) as count'))
                ->groupBy('type')
                ->orderByDesc('count')
                ->limit(5)
                ->get()
                ->pluck('count', 'type'),
        ];
    }
    
    /**
     * Get notification filters for response
     */
    private function getNotificationFilters(Request $request): array
    {
        return [
            'type' => $request->get('type'),
            'is_read' => $request->get('is_read'),
            'priority' => $request->get('priority'),
            'date_from' => $request->get('date_from'),
            'date_to' => $request->get('date_to'),
            'search' => $request->get('search'),
        ];
    }
    
    /**
     * Get notification settings
     */
    private function getNotificationSettings(): array
    {
        return [
            'auto_refresh_interval' => 30, // seconds
            'max_display_count' => 50,
            'retention_days' => 90,
            'enable_sound' => true,
            'enable_desktop' => true,
            'priority_thresholds' => [
                'urgent' => 'immediate',
                'high' => '5_minutes',
                'medium' => '30_minutes',
                'low' => '24_hours',
            ],
        ];
    }
    
    /**
     * Get notification category
     */
    private function getNotificationCategory(string $type): string
    {
        return match($type) {
            'pending_requests', 'overdue_requests', 'high_pending_volume' => 'requests',
            'maintenance_due', 'equipment_out_of_stock', 'low_availability' => 'equipment',
            'high_system_load', 'performance_issue' => 'system',
            'inactive_admins', 'security_alert' => 'security',
            'user_activity', 'new_registration' => 'users',
            default => 'general',
        };
    }
    
    /**
     * Get notification icon
     */
    private function getNotificationIcon(string $type): string
    {
        return match($type) {
            'pending_requests', 'overdue_requests' => 'clock',
            'maintenance_due' => 'tool',
            'equipment_out_of_stock', 'low_availability' => 'package',
            'high_system_load' => 'server',
            'security_alert', 'inactive_admins' => 'shield',
            'user_activity' => 'users',
            'performance_issue' => 'trending-down',
            default => 'bell',
        };
    }

    /**
     * Generate system notifications based on current data
     */
    private function generateSystemNotifications(): array
    {
        $notifications = [];

        // Check for pending requests
        $pendingBorrow = BorrowRequest::where('status', 'pending')->count();
        if ($pendingBorrow > 0) {
            $notifications[] = [
                'type' => 'pending_requests',
                'title' => 'Pending Borrow Requests',
                'message' => "{$pendingBorrow} borrow request(s) awaiting approval",
                'priority' => $pendingBorrow > 10 ? 'high' : 'medium',
                'data' => ['count' => $pendingBorrow, 'type' => 'borrow'],
                'action_url' => '/admin/requests/borrow?status=pending',
            ];
        }

        $pendingVisit = VisitRequest::where('status', 'pending')->count();
        if ($pendingVisit > 0) {
            $notifications[] = [
                'type' => 'pending_requests',
                'title' => 'Pending Visit Requests',
                'message' => "{$pendingVisit} visit request(s) awaiting approval",
                'priority' => $pendingVisit > 5 ? 'high' : 'medium',
                'data' => ['count' => $pendingVisit, 'type' => 'visit'],
                'action_url' => '/admin/requests/visit?status=pending',
            ];
        }

        $pendingTesting = TestingRequest::where('status', 'pending')->count();
        if ($pendingTesting > 0) {
            $notifications[] = [
                'type' => 'pending_requests',
                'title' => 'Pending Testing Requests',
                'message' => "{$pendingTesting} testing request(s) awaiting approval",
                'priority' => $pendingTesting > 3 ? 'high' : 'medium',
                'data' => ['count' => $pendingTesting, 'type' => 'testing'],
                'action_url' => '/admin/requests/testing?status=pending',
            ];
        }

        // Check for equipment needing maintenance
        $needsMaintenance = Equipment::where(function($query) {
                $query->where('next_maintenance_date', '<=', now()->addDays(30))
                      ->orWhere('status', 'maintenance');
            })->count();

        if ($needsMaintenance > 0) {
            $notifications[] = [
                'type' => 'maintenance_due',
                'title' => 'Equipment Maintenance Due',
                'message' => "{$needsMaintenance} equipment(s) need maintenance attention",
                'priority' => 'medium',
                'data' => ['count' => $needsMaintenance],
                'action_url' => '/admin/equipment?maintenance=due',
            ];
        }

        // Check for low equipment availability
        $lowStock = Equipment::where('status', 'active')
            ->where('available_quantity', '<=', DB::raw('total_quantity * 0.2'))
            ->where('available_quantity', '>', 0)
            ->count();

        if ($lowStock > 0) {
            $notifications[] = [
                'type' => 'low_availability',
                'title' => 'Low Equipment Availability',
                'message' => "{$lowStock} equipment(s) have low availability (≤20%)",
                'priority' => 'low',
                'data' => ['count' => $lowStock],
                'action_url' => '/admin/equipment?availability=low',
            ];
        }

        return $notifications;
    }

    /**
     * Get available activity actions
     */
    private function getAvailableActions(): array
    {
        return [
            'created' => 'Created',
            'updated' => 'Updated',
            'deleted' => 'Deleted',
            'approved' => 'Approved',
            'rejected' => 'Rejected',
            'activated' => 'Activated',
            'completed' => 'Completed',
            'cancelled' => 'Cancelled',
            'login' => 'Login',
            'logout' => 'Logout',
        ];
    }

    /**
     * Get available model types
     */
    private function getAvailableModels(): array
    {
        return [
            'App\Models\BorrowRequest' => 'Borrow Request',
            'App\Models\VisitRequest' => 'Visit Request',
            'App\Models\TestingRequest' => 'Testing Request',
            'App\Models\Equipment' => 'Equipment',
            'App\Models\User' => 'User',
            'App\Models\Gallery' => 'Gallery',
            'App\Models\Article' => 'Article',
            'App\Models\StaffMember' => 'Staff Member',
        ];
    }

    /**
     * Get available notification types
     */
    private function getNotificationTypes(): array
    {
        return [
            'pending_requests' => 'Pending Requests',
            'maintenance_due' => 'Maintenance Due',
            'low_availability' => 'Low Availability',
            'system_alert' => 'System Alert',
            'user_activity' => 'User Activity',
        ];
    }

    /**
     * Get available notification priorities
     */
    private function getNotificationPriorities(): array
    {
        return [
            'low' => 'Low',
            'medium' => 'Medium',
            'high' => 'High',
            'urgent' => 'Urgent',
        ];
    }

    /**
     * Get priority label
     */
    private function getPriorityLabel(string $priority): string
    {
        $priorities = $this->getNotificationPriorities();
        return $priorities[$priority] ?? $priority;
    }

    /**
     * Get priority color
     */
    private function getPriorityColor(string $priority): string
    {
        return match($priority) {
            'low' => 'gray',
            'medium' => 'blue',
            'high' => 'orange',
            'urgent' => 'red',
            default => 'gray',
        };
    }

    /**
     * Get real-time counters
     */
    private function getRealTimeCounters(): array
    {
        // Basic statistics with current counts
        $pendingBorrow = BorrowRequest::where('status', 'pending')->count();
        $pendingVisit = VisitRequest::where('status', 'pending')->count();
        $pendingTesting = TestingRequest::where('status', 'pending')->count();

        $activeBorrow = BorrowRequest::where('status', 'active')->count();
        $activeVisit = VisitRequest::where('status', 'active')->count();
        $activeTesting = TestingRequest::where('status', 'active')->count();

        $totalEquipment = Equipment::count();
        $availableEquipment = Equipment::where('status', 'active')
            ->where('available_quantity', '>', 0)->count();

        return [
            'total_pending_requests' => $pendingBorrow + $pendingVisit + $pendingTesting,
            'pending_borrow_requests' => $pendingBorrow,
            'pending_visit_requests' => $pendingVisit,
            'pending_testing_requests' => $pendingTesting,
            'total_active_requests' => $activeBorrow + $activeVisit + $activeTesting,
            'active_borrow_requests' => $activeBorrow,
            'active_visit_requests' => $activeVisit,
            'active_testing_requests' => $activeTesting,
            'total_equipment' => $totalEquipment,
            'available_equipment' => $availableEquipment,
            'equipment_utilization_rate' => $totalEquipment > 0
                ? round((($totalEquipment - $availableEquipment) / $totalEquipment) * 100, 1) : 0,
            'recent_activity_count' => $this->getRecentActivityCount(),
        ];
    }

    /**
     * Get equipment analytics
     */
    private function getEquipmentAnalytics(): array
    {
        $totalEquipment = Equipment::count();
        $statusCounts = Equipment::select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->get()
            ->pluck('count', 'status')
            ->toArray();

        // Calculate equipment availability
        $availabilityStats = Equipment::where('status', 'active')
            ->selectRaw('
                COUNT(*) as total_active,
                SUM(CASE WHEN available_quantity > 0 THEN 1 ELSE 0 END) as available_count,
                SUM(CASE WHEN available_quantity = 0 THEN 1 ELSE 0 END) as fully_utilized,
                SUM(CASE WHEN available_quantity <= (total_quantity * 0.2) AND available_quantity > 0 THEN 1 ELSE 0 END) as low_stock
            ')
            ->first();

        return [
            'total_count' => $totalEquipment,
            'status_distribution' => $statusCounts,
            'availability' => [
                'total_active' => $availabilityStats->total_active ?? 0,
                'available' => $availabilityStats->available_count ?? 0,
                'fully_utilized' => $availabilityStats->fully_utilized ?? 0,
                'low_stock' => $availabilityStats->low_stock ?? 0,
                'availability_rate' => $availabilityStats->total_active > 0
                    ? round(($availabilityStats->available_count / $availabilityStats->total_active) * 100, 1) : 0,
            ],
            'maintenance_alerts' => $this->getMaintenanceAlerts(),
        ];
    }

    /**
     * Get request analytics
     */
    private function getRequestAnalytics(string $dateFrom, string $dateTo): array
    {
        $periodStats = [
            'borrow' => BorrowRequest::whereBetween('created_at', [$dateFrom, $dateTo])
                ->selectRaw('
                    COUNT(*) as total,
                    SUM(CASE WHEN status = "pending" THEN 1 ELSE 0 END) as pending,
                    SUM(CASE WHEN status = "approved" THEN 1 ELSE 0 END) as approved,
                    SUM(CASE WHEN status = "active" THEN 1 ELSE 0 END) as active,
                    SUM(CASE WHEN status = "completed" THEN 1 ELSE 0 END) as completed,
                    SUM(CASE WHEN status = "rejected" THEN 1 ELSE 0 END) as rejected,
                    SUM(CASE WHEN status = "cancelled" THEN 1 ELSE 0 END) as cancelled
                ')
                ->first(),
            'visit' => VisitRequest::whereBetween('created_at', [$dateFrom, $dateTo])
                ->selectRaw('
                    COUNT(*) as total,
                    SUM(CASE WHEN status = "pending" THEN 1 ELSE 0 END) as pending,
                    SUM(CASE WHEN status = "approved" THEN 1 ELSE 0 END) as approved,
                    SUM(CASE WHEN status = "active" THEN 1 ELSE 0 END) as active,
                    SUM(CASE WHEN status = "completed" THEN 1 ELSE 0 END) as completed,
                    SUM(CASE WHEN status = "rejected" THEN 1 ELSE 0 END) as rejected,
                    SUM(CASE WHEN status = "cancelled" THEN 1 ELSE 0 END) as cancelled
                ')
                ->first(),
            'testing' => TestingRequest::whereBetween('created_at', [$dateFrom, $dateTo])
                ->selectRaw('
                    COUNT(*) as total,
                    SUM(CASE WHEN status = "pending" THEN 1 ELSE 0 END) as pending,
                    SUM(CASE WHEN status = "approved" THEN 1 ELSE 0 END) as approved,
                    SUM(CASE WHEN status = "active" THEN 1 ELSE 0 END) as active,
                    SUM(CASE WHEN status = "completed" THEN 1 ELSE 0 END) as completed,
                    SUM(CASE WHEN status = "rejected" THEN 1 ELSE 0 END) as rejected,
                    SUM(CASE WHEN status = "cancelled" THEN 1 ELSE 0 END) as cancelled
                ')
                ->first(),
        ];

        $totalRequests = ($periodStats['borrow']->total ?? 0) +
                        ($periodStats['visit']->total ?? 0) +
                        ($periodStats['testing']->total ?? 0);

        return [
            'period_summary' => [
                'total_requests' => $totalRequests,
                'by_type' => [
                    'borrow' => $periodStats['borrow']->total ?? 0,
                    'visit' => $periodStats['visit']->total ?? 0,
                    'testing' => $periodStats['testing']->total ?? 0,
                ],
            ],
            'status_breakdown' => $periodStats,
            'approval_rates' => [
                'borrow' => $this->calculateApprovalRate('borrow', $dateFrom, $dateTo),
                'visit' => $this->calculateApprovalRate('visit', $dateFrom, $dateTo),
                'testing' => $this->calculateApprovalRate('testing', $dateFrom, $dateTo),
            ],
        ];
    }

    /**
     * Get trend data (30-day analysis)
     */
    private function getTrendData(string $dateFrom, string $dateTo): array
    {
        // Daily trends for the specified period
        $dailyTrends = [];
        $start = Carbon::parse($dateFrom);
        $end = Carbon::parse($dateTo);

        while ($start <= $end) {
            $dayStart = $start->copy()->startOfDay();
            $dayEnd = $start->copy()->endOfDay();

            $dailyTrends[] = [
                'date' => $start->format('Y-m-d'),
                'day_name' => $start->format('l'),
                'borrow_requests' => BorrowRequest::whereBetween('created_at', [$dayStart, $dayEnd])->count(),
                'visit_requests' => VisitRequest::whereBetween('created_at', [$dayStart, $dayEnd])->count(),
                'testing_requests' => TestingRequest::whereBetween('created_at', [$dayStart, $dayEnd])->count(),
            ];

            $start->addDay();
        }

        // Monthly trends (last 12 months)
        $monthlyTrends = [];
        for ($i = 11; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $monthStart = $month->copy()->startOfMonth();
            $monthEnd = $month->copy()->endOfMonth();

            $borrowCount = BorrowRequest::whereBetween('created_at', [$monthStart, $monthEnd])->count();
            $visitCount = VisitRequest::whereBetween('created_at', [$monthStart, $monthEnd])->count();
            $testingCount = TestingRequest::whereBetween('created_at', [$monthStart, $monthEnd])->count();

            $monthlyTrends[] = [
                'month' => $month->format('Y-m'),
                'month_name' => $month->format('M Y'),
                'borrow_requests' => $borrowCount,
                'visit_requests' => $visitCount,
                'testing_requests' => $testingCount,
                'total_requests' => $borrowCount + $visitCount + $testingCount,
                'growth_rate' => $i < 11 ? $this->calculateGrowthRate($i, $monthlyTrends) : 0,
            ];
        }

        return [
            'daily_trends' => $dailyTrends,
            'monthly_trends' => $monthlyTrends,
            'equipment_usage_trend' => $this->getEquipmentUsageTrend($dateFrom, $dateTo),
        ];
    }

    /**
     * Get peak activity analysis
     */
    private function getPeakAnalysis(): array
    {
        // Analyze hourly patterns
        $hourlyActivity = DB::table('borrow_requests')
            ->selectRaw('HOUR(created_at) as hour, COUNT(*) as count')
            ->where('created_at', '>=', now()->subDays(30))
            ->groupBy('hour')
            ->orderBy('hour')
            ->get()
            ->pluck('count', 'hour')
            ->toArray();

        // Fill missing hours with 0
        for ($i = 0; $i < 24; $i++) {
            if (!isset($hourlyActivity[$i])) {
                $hourlyActivity[$i] = 0;
            }
        }
        ksort($hourlyActivity);

        // Analyze daily patterns
        $dailyActivity = DB::table('borrow_requests')
            ->selectRaw('DAYOFWEEK(created_at) as day_of_week, COUNT(*) as count')
            ->where('created_at', '>=', now()->subDays(30))
            ->groupBy('day_of_week')
            ->get()
            ->pluck('count', 'day_of_week')
            ->toArray();

        $dayNames = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
        $formattedDailyActivity = [];
        for ($i = 1; $i <= 7; $i++) {
            $formattedDailyActivity[$dayNames[$i-1]] = $dailyActivity[$i] ?? 0;
        }

        return [
            'peak_hours' => $hourlyActivity,
            'peak_days' => $formattedDailyActivity,
            'busiest_hour' => array_search(max($hourlyActivity), $hourlyActivity),
            'busiest_day' => array_search(max($formattedDailyActivity), $formattedDailyActivity),
            'activity_patterns' => $this->getActivityPatterns(),
        ];
    }

    /**
     * Get quick insights
     */
    private function getQuickInsights(string $dateFrom, string $dateTo): array
    {
        // Most requested equipment
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
            ->limit(10)
            ->get();

        // Average processing times
        $avgProcessingTimes = [
            'borrow' => $this->getAverageProcessingTime('borrow', $dateFrom, $dateTo),
            'visit' => $this->getAverageProcessingTime('visit', $dateFrom, $dateTo),
            'testing' => $this->getAverageProcessingTime('testing', $dateFrom, $dateTo),
        ];

        // Low stock alerts
        $lowStockEquipment = Equipment::with('category:id,name')
            ->where('status', 'active')
            ->whereRaw('available_quantity <= (total_quantity * 0.2)')
            ->where('available_quantity', '>', 0)
            ->select('id', 'name', 'total_quantity', 'available_quantity', 'category_id')
            ->orderBy('available_quantity')
            ->limit(10)
            ->get()
            ->map(function ($equipment) {
                return [
                    'id' => $equipment->id,
                    'name' => $equipment->name,
                    'category' => $equipment->category ? $equipment->category->name : 'Unknown',
                    'total_quantity' => $equipment->total_quantity,
                    'available_quantity' => $equipment->available_quantity,
                    'utilization_rate' => round((($equipment->total_quantity - $equipment->available_quantity) / $equipment->total_quantity) * 100, 1),
                ];
            });

        return [
            'most_requested_equipment' => $mostRequestedEquipment,
            'avg_processing_times' => $avgProcessingTimes,
            'low_stock_equipment' => $lowStockEquipment,
            'performance_indicators' => $this->getPerformanceIndicators($dateFrom, $dateTo),
        ];
    }

    /**
     * Get performance metrics
     */
    private function getPerformanceMetrics(string $dateFrom, string $dateTo): array
    {
        $metrics = [
            'request_completion_rates' => [
                'borrow' => $this->getCompletionRate('borrow', $dateFrom, $dateTo),
                'visit' => $this->getCompletionRate('visit', $dateFrom, $dateTo),
                'testing' => $this->getCompletionRate('testing', $dateFrom, $dateTo),
            ],
            'admin_productivity' => $this->getAdminProductivityMetrics($dateFrom, $dateTo),
            'system_efficiency' => [
                'avg_response_time' => $this->getAverageResponseTime($dateFrom, $dateTo),
                'equipment_turnaround' => $this->getEquipmentTurnaroundTime($dateFrom, $dateTo),
                'user_satisfaction_score' => $this->calculateUserSatisfactionScore($dateFrom, $dateTo),
            ],
        ];

        return $metrics;
    }

    /**
     * Get system alerts
     */
    private function getSystemAlerts(): array
    {
        $alerts = [];

        // High priority alerts
        $urgentRequests = BorrowRequest::where('status', 'pending')
            ->where('created_at', '<=', now()->subHours(24))
            ->count();

        if ($urgentRequests > 0) {
            $alerts[] = [
                'type' => 'urgent_pending',
                'priority' => 'high',
                'title' => 'Overdue Pending Requests',
                'message' => "{$urgentRequests} requests pending for over 24 hours",
                'count' => $urgentRequests,
                'action_url' => '/admin/requests?status=pending&overdue=true',
            ];
        }

        // Equipment maintenance alerts
        $maintenanceDue = Equipment::where('next_maintenance_date', '<=', now()->addDays(7))
            ->where('status', 'active')
            ->count();

        if ($maintenanceDue > 0) {
            $alerts[] = [
                'type' => 'maintenance_due',
                'priority' => 'medium',
                'title' => 'Equipment Maintenance Due',
                'message' => "{$maintenanceDue} equipment(s) require maintenance within 7 days",
                'count' => $maintenanceDue,
                'action_url' => '/admin/equipment?maintenance=due',
            ];
        }

        // Low stock alerts
        $lowStock = Equipment::where('status', 'active')
            ->whereRaw('available_quantity <= (total_quantity * 0.1)')
            ->where('available_quantity', '>', 0)
            ->count();

        if ($lowStock > 0) {
            $alerts[] = [
                'type' => 'low_stock',
                'priority' => 'medium',
                'title' => 'Critical Stock Levels',
                'message' => "{$lowStock} equipment(s) at critically low stock (≤10%)",
                'count' => $lowStock,
                'action_url' => '/admin/equipment?stock=critical',
            ];
        }

        return $alerts;
    }

    // Helper methods for calculations

    private function getRecentActivityCount(): int
    {
        $weekAgo = now()->subDays(7);
        return BorrowRequest::where('created_at', '>=', $weekAgo)->count() +
               VisitRequest::where('created_at', '>=', $weekAgo)->count() +
               TestingRequest::where('created_at', '>=', $weekAgo)->count();
    }

    private function getPendingActionsCount(): int
    {
        return BorrowRequest::where('status', 'pending')->count() +
               VisitRequest::where('status', 'pending')->count() +
               TestingRequest::where('status', 'pending')->count();
    }

    private function getSystemHealth(): array
    {
        $activeEquipment = Equipment::where('status', 'active')->count();
        $totalEquipment = Equipment::count();

        return [
            'equipment_health' => $totalEquipment > 0 ? round(($activeEquipment / $totalEquipment) * 100, 1) : 100,
            'pending_requests_load' => $this->calculatePendingLoad(),
            'system_status' => 'operational',
        ];
    }

    private function calculatePendingLoad(): string
    {
        $pendingCount = $this->getPendingActionsCount();

        if ($pendingCount <= 5) return 'low';
        if ($pendingCount <= 15) return 'medium';
        if ($pendingCount <= 30) return 'high';
        return 'critical';
    }

    private function getMaintenanceAlerts(): array
    {
        return Equipment::where('next_maintenance_date', '<=', now()->addDays(30))
            ->where('status', 'active')
            ->select('id', 'name', 'next_maintenance_date', 'status')
            ->orderBy('next_maintenance_date')
            ->limit(5)
            ->get()
            ->toArray();
    }

    private function calculateApprovalRate(string $type, string $dateFrom, string $dateTo): float
    {
        $modelClass = match($type) {
            'borrow' => BorrowRequest::class,
            'visit' => VisitRequest::class,
            'testing' => TestingRequest::class,
            default => BorrowRequest::class,
        };

        $total = $modelClass::whereBetween('created_at', [$dateFrom, $dateTo])
            ->whereIn('status', ['approved', 'rejected', 'completed'])
            ->count();

        $approved = $modelClass::whereBetween('created_at', [$dateFrom, $dateTo])
            ->whereIn('status', ['approved', 'completed'])
            ->count();

        return $total > 0 ? round(($approved / $total) * 100, 1) : 0;
    }

    private function calculateGrowthRate(int $monthIndex, array $monthlyTrends): float
    {
        if ($monthIndex === 0 || !isset($monthlyTrends[$monthIndex - 1])) {
            return 0;
        }

        $current = $monthlyTrends[$monthIndex]['total_requests'];
        $previous = $monthlyTrends[$monthIndex - 1]['total_requests'];

        if ($previous === 0) return $current > 0 ? 100 : 0;

        return round((($current - $previous) / $previous) * 100, 1);
    }

    private function getEquipmentUsageTrend(string $dateFrom, string $dateTo): array
    {
        return DB::table('borrow_request_items')
            ->join('borrow_requests', 'borrow_request_items.borrow_request_id', '=', 'borrow_requests.id')
            ->whereBetween('borrow_requests.created_at', [$dateFrom, $dateTo])
            ->selectRaw('DATE(borrow_requests.created_at) as date, COUNT(*) as usage_count')
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->toArray();
    }

    private function getActivityPatterns(): array
    {
        return [
            'peak_hours' => [9, 10, 11, 14, 15], // Common peak hours
            'low_activity_hours' => [0, 1, 2, 3, 4, 5, 6, 22, 23],
            'peak_days' => ['Monday', 'Tuesday', 'Wednesday'],
            'low_activity_days' => ['Saturday', 'Sunday'],
        ];
    }

    private function getAverageProcessingTime(string $type, string $dateFrom, string $dateTo): array
    {
        $modelClass = match($type) {
            'borrow' => BorrowRequest::class,
            'visit' => VisitRequest::class,
            'testing' => TestingRequest::class,
            default => BorrowRequest::class,
        };

        $processed = $modelClass::whereBetween('created_at', [$dateFrom, $dateTo])
            ->whereNotNull('reviewed_at')
            ->selectRaw('AVG(TIMESTAMPDIFF(HOUR, created_at, reviewed_at)) as avg_hours')
            ->first();

        $avgHours = $processed->avg_hours ?? 0;

        return [
            'average_hours' => round($avgHours, 1),
            'average_days' => round($avgHours / 24, 1),
            'performance_rating' => $this->getProcessingPerformanceRating($avgHours),
        ];
    }

    private function getProcessingPerformanceRating(float $hours): string
    {
        if ($hours <= 2) return 'excellent';
        if ($hours <= 8) return 'good';
        if ($hours <= 24) return 'average';
        if ($hours <= 72) return 'poor';
        return 'critical';
    }

    private function getPerformanceIndicators(string $dateFrom, string $dateTo): array
    {
        $totalRequests = BorrowRequest::whereBetween('created_at', [$dateFrom, $dateTo])->count() +
                        VisitRequest::whereBetween('created_at', [$dateFrom, $dateTo])->count() +
                        TestingRequest::whereBetween('created_at', [$dateFrom, $dateTo])->count();

        $processedRequests = BorrowRequest::whereBetween('created_at', [$dateFrom, $dateTo])
                                ->whereIn('status', ['approved', 'completed', 'rejected'])->count() +
                            VisitRequest::whereBetween('created_at', [$dateFrom, $dateTo])
                                ->whereIn('status', ['approved', 'completed', 'rejected'])->count() +
                            TestingRequest::whereBetween('created_at', [$dateFrom, $dateTo])
                                ->whereIn('status', ['approved', 'completed', 'rejected'])->count();

        return [
            'processing_efficiency' => $totalRequests > 0 ? round(($processedRequests / $totalRequests) * 100, 1) : 0,
            'equipment_availability_rate' => $this->getEquipmentAvailabilityRate(),
            'user_activity_score' => $this->calculateUserActivityScore($dateFrom, $dateTo),
        ];
    }

    private function getCompletionRate(string $type, string $dateFrom, string $dateTo): float
    {
        $modelClass = match($type) {
            'borrow' => BorrowRequest::class,
            'visit' => VisitRequest::class,
            'testing' => TestingRequest::class,
            default => BorrowRequest::class,
        };

        $total = $modelClass::whereBetween('created_at', [$dateFrom, $dateTo])->count();
        $completed = $modelClass::whereBetween('created_at', [$dateFrom, $dateTo])
            ->where('status', 'completed')->count();

        return $total > 0 ? round(($completed / $total) * 100, 1) : 0;
    }

    private function getAdminProductivityMetrics(string $dateFrom, string $dateTo): array
    {
        $adminActions = ActivityLog::whereBetween('created_at', [$dateFrom, $dateTo])
            ->whereHas('user', function($query) {
                $query->whereIn('role', ['admin', 'super_admin']);
            })
            ->count();

        $activeAdmins = User::whereIn('role', ['admin', 'super_admin'])
            ->where('is_active', true)
            ->count();

        return [
            'total_admin_actions' => $adminActions,
            'active_admins' => $activeAdmins,
            'avg_actions_per_admin' => $activeAdmins > 0 ? round($adminActions / $activeAdmins, 1) : 0,
        ];
    }

    private function getAverageResponseTime(string $dateFrom, string $dateTo): float
    {
        // Calculate average time from request creation to first admin action
        $avgResponseTime = DB::table('borrow_requests')
            ->whereBetween('created_at', [$dateFrom, $dateTo])
            ->whereNotNull('reviewed_at')
            ->selectRaw('AVG(TIMESTAMPDIFF(HOUR, created_at, reviewed_at)) as avg_hours')
            ->value('avg_hours');

        return round($avgResponseTime ?? 0, 1);
    }

    private function getEquipmentTurnaroundTime(string $dateFrom, string $dateTo): float
    {
        // Calculate average time equipment is borrowed
        $avgTurnaround = BorrowRequest::whereBetween('created_at', [$dateFrom, $dateTo])
            ->where('status', 'completed')
            ->selectRaw('AVG(TIMESTAMPDIFF(DAY, borrow_date, return_date)) as avg_days')
            ->value('avg_days');

        return round($avgTurnaround ?? 0, 1);
    }

    private function calculateUserSatisfactionScore(string $dateFrom, string $dateTo): float
    {
        // Calculate satisfaction based on completion rates and processing times
        $completionRate = $this->getCompletionRate('borrow', $dateFrom, $dateTo);
        $avgProcessingHours = $this->getAverageProcessingTime('borrow', $dateFrom, $dateTo)['average_hours'];

        // Score based on completion rate (60%) and processing speed (40%)
        $completionScore = $completionRate;
        $speedScore = max(0, 100 - ($avgProcessingHours * 2)); // Penalty for slow processing

        return round(($completionScore * 0.6) + ($speedScore * 0.4), 1);
    }

    private function getEquipmentAvailabilityRate(): float
    {
        $total = Equipment::where('status', 'active')->count();
        $available = Equipment::where('status', 'active')
            ->where('available_quantity', '>', 0)
            ->count();

        return $total > 0 ? round(($available / $total) * 100, 1) : 0;
    }

    private function calculateUserActivityScore(string $dateFrom, string $dateTo): float
    {
        $requests = BorrowRequest::whereBetween('created_at', [$dateFrom, $dateTo])->count() +
                   VisitRequest::whereBetween('created_at', [$dateFrom, $dateTo])->count() +
                   TestingRequest::whereBetween('created_at', [$dateFrom, $dateTo])->count();

        $days = Carbon::parse($dateFrom)->diffInDays(Carbon::parse($dateTo)) + 1;
        $dailyAverage = $requests / $days;

        // Score based on daily average (normalized to 0-100 scale)
        return min(100, round($dailyAverage * 10, 1));
    }
}
