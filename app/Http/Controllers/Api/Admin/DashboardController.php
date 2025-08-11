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
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Get dashboard statistics
     */
    public function statistics(Request $request): JsonResponse
    {
        try {
            // Date range for filtering (default to last 30 days for trends)
            $dateFrom = $request->get('date_from', now()->subDays(30)->format('Y-m-d'));
            $dateTo = $request->get('date_to', now()->format('Y-m-d'));
            
            // Request statistics
            $pendingBorrowRequests = BorrowRequest::where('status', 'pending')->count();
            $pendingVisitRequests = VisitRequest::where('status', 'pending')->count();
            $pendingTestingRequests = TestingRequest::where('status', 'pending')->count();
            
            $activeBorrowRequests = BorrowRequest::where('status', 'active')->count();
            $activeVisitRequests = VisitRequest::where('status', 'active')->count();
            $activeTestingRequests = TestingRequest::where('status', 'active')->count();
            
            // Equipment statistics
            $totalEquipment = Equipment::count();
            $availableEquipment = Equipment::available()->count();
            $maintenanceEquipment = Equipment::where('status', 'maintenance')->count();
            $retiredEquipment = Equipment::where('status', 'retired')->count();
            
            // Recent activity (last 7 days)
            $recentRequests = BorrowRequest::whereBetween('created_at', [now()->subDays(7), now()])->count() +
                             VisitRequest::whereBetween('created_at', [now()->subDays(7), now()])->count() +
                             TestingRequest::whereBetween('created_at', [now()->subDays(7), now()])->count();
            
            // Monthly trends (last 6 months)
            $monthlyData = [];
            for ($i = 5; $i >= 0; $i--) {
                $month = now()->subMonths($i);
                $monthStart = $month->startOfMonth()->copy();
                $monthEnd = $month->endOfMonth()->copy();
                
                $monthlyData[] = [
                    'month' => $month->format('Y-m'),
                    'month_name' => $month->format('M Y'),
                    'borrow_requests' => BorrowRequest::whereBetween('created_at', [$monthStart, $monthEnd])->count(),
                    'visit_requests' => VisitRequest::whereBetween('created_at', [$monthStart, $monthEnd])->count(),
                    'testing_requests' => TestingRequest::whereBetween('created_at', [$monthStart, $monthEnd])->count(),
                ];
            }
            
            // Equipment utilization (top 5 most requested equipment)
            $topEquipment = DB::table('borrow_request_items')
                ->join('equipment', 'borrow_request_items.equipment_id', '=', 'equipment.id')
                ->join('borrow_requests', 'borrow_request_items.borrow_request_id', '=', 'borrow_requests.id')
                ->whereBetween('borrow_requests.created_at', [$dateFrom, $dateTo])
                ->select(
                    'equipment.id',
                    'equipment.name',
                    DB::raw('COUNT(borrow_request_items.id) as request_count'),
                    DB::raw('SUM(borrow_request_items.quantity_requested) as total_quantity_requested')
                )
                ->groupBy('equipment.id', 'equipment.name')
                ->orderByDesc('request_count')
                ->limit(5)
                ->get();
            
            // Status distribution
            $borrowRequestStatus = BorrowRequest::select('status', DB::raw('count(*) as count'))
                ->groupBy('status')
                ->get()
                ->pluck('count', 'status')
                ->toArray();
                
            $visitRequestStatus = VisitRequest::select('status', DB::raw('count(*) as count'))
                ->groupBy('status')
                ->get()
                ->pluck('count', 'status')
                ->toArray();
            
            $testingRequestStatus = TestingRequest::select('status', DB::raw('count(*) as count'))
                ->groupBy('status')
                ->get()
                ->pluck('count', 'status')
                ->toArray();
            
            // User statistics
            $totalUsers = User::count();
            $activeAdmins = User::where('is_active', true)->whereIn('role', ['admin', 'super_admin'])->count();
            
            $stats = [
                // Summary cards
                'summary' => [
                    'pending_borrow_requests' => $pendingBorrowRequests,
                    'pending_visit_requests' => $pendingVisitRequests,
                    'pending_testing_requests' => $pendingTestingRequests,
                    'total_pending_requests' => $pendingBorrowRequests + $pendingVisitRequests + $pendingTestingRequests,
                    'active_requests' => $activeBorrowRequests + $activeVisitRequests + $activeTestingRequests,
                    'total_equipment' => $totalEquipment,
                    'available_equipment' => $availableEquipment,
                    'equipment_utilization_rate' => $totalEquipment > 0 ? round((($totalEquipment - $availableEquipment) / $totalEquipment) * 100, 1) : 0,
                    'recent_activity_count' => $recentRequests,
                ],
                
                // Equipment status breakdown
                'equipment_status' => [
                    'total' => $totalEquipment,
                    'available' => $availableEquipment,
                    'maintenance' => $maintenanceEquipment,
                    'retired' => $retiredEquipment,
                    'in_use' => $totalEquipment - $availableEquipment - $maintenanceEquipment - $retiredEquipment,
                ],
                
                // Request status distributions
                'request_status' => [
                    'borrow_requests' => $borrowRequestStatus,
                    'visit_requests' => $visitRequestStatus,
                    'testing_requests' => $testingRequestStatus,
                ],
                
                // Monthly trends
                'monthly_trends' => $monthlyData,
                
                // Top equipment usage
                'top_equipment' => $topEquipment,
                
                // System statistics
                'system' => [
                    'total_users' => $totalUsers,
                    'active_admins' => $activeAdmins,
                    'database_last_updated' => now()->format('Y-m-d H:i:s'),
                ],
                
                // Filters applied
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
     * Get activity logs
     */
    public function activityLogs(Request $request): JsonResponse
    {
        try {
            $query = ActivityLog::with(['user'])
                ->orderBy('created_at', 'desc');
            
            // Apply filters
            if ($request->filled('user_id')) {
                $query->where('user_id', $request->get('user_id'));
            }
            
            if ($request->filled('action')) {
                $query->where('action', $request->get('action'));
            }
            
            if ($request->filled('model_type')) {
                $query->where('model_type', $request->get('model_type'));
            }
            
            if ($request->filled('date_from')) {
                $query->whereDate('created_at', '>=', $request->get('date_from'));
            }
            
            if ($request->filled('date_to')) {
                $query->whereDate('created_at', '<=', $request->get('date_to'));
            }
            
            if ($request->filled('search')) {
                $search = $request->get('search');
                $query->where(function ($q) use ($search) {
                    $q->where('description', 'like', "%{$search}%")
                      ->orWhere('model_type', 'like', "%{$search}%")
                      ->orWhereHas('user', function ($userQuery) use ($search) {
                          $userQuery->where('name', 'like', "%{$search}%")
                                   ->orWhere('email', 'like', "%{$search}%");
                      });
                });
            }
            
            $perPage = min($request->get('per_page', 20), 100);
            $activityLogs = $query->paginate($perPage);
            
            // Transform data
            $activityData = $activityLogs->getCollection()->map(function ($log) {
                return [
                    'id' => $log->id,
                    'action' => $log->action,
                    'description' => $log->description,
                    'model_type' => $log->model_type,
                    'model_id' => $log->model_id,
                    'changes' => $log->changes,
                    'ip_address' => $log->ip_address,
                    'user_agent' => $log->user_agent,
                    'created_at' => $log->created_at->format('Y-m-d H:i:s'),
                    'created_at_human' => $log->created_at->diffForHumans(),
                    'user' => $log->user ? [
                        'id' => $log->user->id,
                        'name' => $log->user->name,
                        'email' => $log->user->email,
                        'role' => $log->user->role ?? 'user',
                    ] : null,
                ];
            });
            
            return ApiResponse::paginated(
                $activityLogs,
                null,
                'Activity logs retrieved successfully',
                [
                    'filters' => [
                        'user_id' => $request->get('user_id'),
                        'action' => $request->get('action'),
                        'model_type' => $request->get('model_type'),
                        'date_from' => $request->get('date_from'),
                        'date_to' => $request->get('date_to'),
                        'search' => $request->get('search'),
                    ],
                    'available_actions' => $this->getAvailableActions(),
                    'available_models' => $this->getAvailableModels(),
                ],
                $activityData
            );
            
        } catch (\Exception $e) {
            return ApiResponse::error('Failed to retrieve activity logs', 500, null, $e->getMessage());
        }
    }
    
    /**
     * Get notifications
     */
    public function notifications(Request $request): JsonResponse
    {
        try {
            $query = Notification::orderBy('created_at', 'desc');
            
            // Apply filters
            if ($request->filled('type')) {
                $query->where('type', $request->get('type'));
            }
            
            if ($request->filled('is_read')) {
                if ($request->boolean('is_read')) {
                    $query->read();
                } else {
                    $query->unread();
                }
            }
            
            if ($request->filled('search')) {
                $search = $request->get('search');
                $query->where(function ($q) use ($search) {
                    $q->where('type', 'like', "%{$search}%")
                      ->orWhereJsonContains('data->title', $search)
                      ->orWhereJsonContains('data->message', $search);
                });
            }
            
            $perPage = min($request->get('per_page', 15), 100);
            $notifications = $query->paginate($perPage);
            
            // Transform data
            $notificationData = $notifications->getCollection()->map(function ($notification) {
                return [
                    'id' => $notification->id,
                    'type' => $notification->type,
                    'title' => $notification->title ?? $notification->getData('title', 'Notification'),
                    'message' => $notification->message ?? $notification->getData('message', 'You have a new notification'),
                    'data' => $notification->data,
                    'priority' => $notification->getData('priority', 'medium'),
                    'is_read' => $notification->isRead(),
                    'created_at' => $notification->created_at->format('Y-m-d H:i:s'),
                    'created_at_human' => $notification->created_at->diffForHumans(),
                    'priority_label' => $this->getPriorityLabel($notification->getData('priority', 'medium')),
                    'priority_color' => $this->getPriorityColor($notification->getData('priority', 'medium')),
                    'action_url' => $notification->action_url,
                ];
            });
            
            // Generate system notifications based on current data
            $systemNotifications = $this->generateSystemNotifications();
            
            return ApiResponse::paginated(
                $notifications,
                null,
                'Notifications retrieved successfully',
                [
                    'filters' => [
                        'type' => $request->get('type'),
                        'is_read' => $request->get('is_read'),
                        'priority' => $request->get('priority'),
                        'search' => $request->get('search'),
                    ],
                    'unread_count' => Notification::unread()->count(),
                    'system_notifications' => $systemNotifications,
                    'available_types' => $this->getNotificationTypes(),
                    'available_priorities' => $this->getNotificationPriorities(),
                ],
                $notificationData
            );
            
        } catch (\Exception $e) {
            return ApiResponse::error('Failed to retrieve notifications', 500, null, $e->getMessage());
        }
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
}