<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\ApiResponse;
use App\Models\BorrowRequest;
use App\Models\VisitRequest;
use App\Models\TestingRequest;
use App\Models\Equipment;
use App\Models\Category;
use App\Models\BorrowRequestItem;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class ReportsController extends Controller
{
    /**
     * Get equipment usage statistics and analytics
     */
    public function equipmentUsage(Request $request): JsonResponse
    {
        try {
            // Validate request parameters
            $validated = $request->validate([
                'date_from' => 'nullable|date|before_or_equal:today',
                'date_to' => 'nullable|date|after_or_equal:date_from|before_or_equal:today',
                'category_id' => 'nullable|exists:categories,id',
                'equipment_id' => 'nullable|exists:equipment,id',
                'status' => 'nullable|string|in:active,maintenance,retired'
            ]);

            // Set default date range (last 6 months)
            $dateFrom = $validated['date_from'] ?? now()->subMonths(6)->format('Y-m-d');
            $dateTo = $validated['date_to'] ?? now()->format('Y-m-d');

            // Build base query for equipment usage data
            $baseQuery = $this->buildEquipmentUsageQuery($dateFrom, $dateTo, $validated);

            // Get equipment usage statistics
            $mostUsedEquipment = $this->getMostUsedEquipment($baseQuery, 10);
            $leastUsedEquipment = $this->getLeastUsedEquipment($baseQuery, 10);
            $usageByCategory = $this->getUsageByCategoryData($dateFrom, $dateTo, $validated);
            $availabilityTrends = $this->getAvailabilityTrends($dateFrom, $dateTo, $validated);
            $peakUsageAnalysis = $this->getPeakUsageAnalysis($dateFrom, $dateTo, $validated);
            $utilizationMetrics = $this->getUtilizationMetrics($dateFrom, $dateTo, $validated);

            // Prepare response data
            $reportData = [
                'summary' => [
                    'total_equipment' => Equipment::when($validated['category_id'] ?? null, function ($query, $categoryId) {
                        return $query->where('category_id', $categoryId);
                    })->count(),
                    'active_equipment' => Equipment::where('status', 'active')
                        ->when($validated['category_id'] ?? null, function ($query, $categoryId) {
                            return $query->where('category_id', $categoryId);
                        })->count(),
                    'total_requests_period' => $baseQuery->count(),
                    'total_equipment_borrowed' => $baseQuery->sum('quantity_requested'),
                    'date_range' => [
                        'from' => $dateFrom,
                        'to' => $dateTo,
                        'days' => Carbon::parse($dateFrom)->diffInDays(Carbon::parse($dateTo)) + 1
                    ]
                ],
                'most_used_equipment' => $mostUsedEquipment,
                'least_used_equipment' => $leastUsedEquipment,
                'usage_by_category' => $usageByCategory,
                'availability_trends' => $availabilityTrends,
                'peak_usage' => $peakUsageAnalysis,
                'utilization_metrics' => $utilizationMetrics
            ];

            Log::info('Equipment usage report generated', [
                'admin_user_id' => $request->user()->id,
                'date_range' => [$dateFrom, $dateTo],
                'filters' => $validated
            ]);

            return ApiResponse::success(
                $reportData,
                'Equipment usage report generated successfully',
                200,
                [
                    'generated_at' => now()->format('Y-m-d H:i:s'),
                    'filters_applied' => $validated,
                    'report_type' => 'equipment_usage'
                ]
            );

        } catch (\Illuminate\Validation\ValidationException $e) {
            return ApiResponse::validationError($e->errors());

        } catch (\Exception $e) {
            Log::error('Failed to generate equipment usage report', [
                'error' => $e->getMessage(),
                'admin_user_id' => $request->user()->id,
                'filters' => $validated ?? []
            ]);

            return ApiResponse::error(
                'Failed to generate equipment usage report',
                500,
                null,
                $e->getMessage()
            );
        }
    }

    /**
     * Get request analytics and trends
     */
    public function requestAnalytics(Request $request): JsonResponse
    {
        try {
            // Validate request parameters
            $validated = $request->validate([
                'date_from' => 'nullable|date|before_or_equal:today',
                'date_to' => 'nullable|date|after_or_equal:date_from|before_or_equal:today',
                'request_type' => 'nullable|string|in:borrow,visit,testing',
                'status' => 'nullable|string|in:pending,approved,rejected,active,completed,cancelled',
                'grouping' => 'nullable|string|in:daily,weekly,monthly'
            ]);

            // Set defaults
            $dateFrom = $validated['date_from'] ?? now()->subMonths(3)->format('Y-m-d');
            $dateTo = $validated['date_to'] ?? now()->format('Y-m-d');
            $grouping = $validated['grouping'] ?? 'daily';

            // Get analytics data
            $volumeTrends = $this->getRequestVolumeTrends($dateFrom, $dateTo, $grouping, $validated);
            $typeDistribution = $this->getRequestTypeDistribution($dateFrom, $dateTo, $validated);
            $processingMetrics = $this->getProcessingMetrics($dateFrom, $dateTo, $validated);
            $successRates = $this->getSuccessRates($dateFrom, $dateTo, $validated);
            $popularEquipment = $this->getPopularEquipmentFromRequests($dateFrom, $dateTo, $validated);
            $peakPeriods = $this->getRequestPeakPeriods($dateFrom, $dateTo, $validated);

            // Calculate summary statistics
            $totalRequests = $this->getTotalRequestsCount($dateFrom, $dateTo, $validated);
            $averageProcessingTime = $processingMetrics['average_processing_time'] ?? 0;

            $reportData = [
                'summary' => [
                    'total_requests' => $totalRequests,
                    'date_range' => [
                        'from' => $dateFrom,
                        'to' => $dateTo,
                        'days' => Carbon::parse($dateFrom)->diffInDays(Carbon::parse($dateTo)) + 1
                    ],
                    'average_processing_time_hours' => round($averageProcessingTime, 2),
                    'grouping' => $grouping
                ],
                'volume_trends' => $volumeTrends,
                'type_distribution' => $typeDistribution,
                'processing_metrics' => $processingMetrics,
                'success_rates' => $successRates,
                'popular_equipment' => $popularEquipment,
                'peak_periods' => $peakPeriods
            ];

            Log::info('Request analytics report generated', [
                'admin_user_id' => $request->user()->id,
                'date_range' => [$dateFrom, $dateTo],
                'filters' => $validated
            ]);

            return ApiResponse::success(
                $reportData,
                'Request analytics report generated successfully',
                200,
                [
                    'generated_at' => now()->format('Y-m-d H:i:s'),
                    'filters_applied' => $validated,
                    'report_type' => 'request_analytics'
                ]
            );

        } catch (\Illuminate\Validation\ValidationException $e) {
            return ApiResponse::validationError($e->errors());

        } catch (\Exception $e) {
            Log::error('Failed to generate request analytics report', [
                'error' => $e->getMessage(),
                'admin_user_id' => $request->user()->id,
                'filters' => $validated ?? []
            ]);

            return ApiResponse::error(
                'Failed to generate request analytics report',
                500,
                null,
                $e->getMessage()
            );
        }
    }

    /**
     * Build base query for equipment usage data
     */
    private function buildEquipmentUsageQuery(string $dateFrom, string $dateTo, array $filters)
    {
        $query = BorrowRequestItem::query()
            ->join('equipment', 'borrow_request_items.equipment_id', '=', 'equipment.id')
            ->join('borrow_requests', 'borrow_request_items.borrow_request_id', '=', 'borrow_requests.id')
            ->whereBetween('borrow_requests.created_at', [$dateFrom . ' 00:00:00', $dateTo . ' 23:59:59']);

        // Apply filters
        if ($filters['category_id'] ?? null) {
            $query->where('equipment.category_id', $filters['category_id']);
        }

        if ($filters['equipment_id'] ?? null) {
            $query->where('equipment.id', $filters['equipment_id']);
        }

        if ($filters['status'] ?? null) {
            $query->where('equipment.status', $filters['status']);
        }

        return $query;
    }

    /**
     * Get most used equipment
     */
    private function getMostUsedEquipment($baseQuery, int $limit): array
    {
        return $baseQuery->clone()
            ->select([
                'equipment.id',
                'equipment.name',
                'equipment.model',
                'equipment.total_quantity',
                'equipment.available_quantity',
                DB::raw('COUNT(borrow_request_items.id) as request_count'),
                DB::raw('SUM(borrow_request_items.quantity_requested) as total_quantity_requested'),
                DB::raw('ROUND((SUM(borrow_request_items.quantity_requested) / equipment.total_quantity * 100), 2) as utilization_rate')
            ])
            ->groupBy(['equipment.id', 'equipment.name', 'equipment.model', 'equipment.total_quantity', 'equipment.available_quantity'])
            ->orderByDesc('request_count')
            ->limit($limit)
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'name' => $item->name,
                    'model' => $item->model,
                    'request_count' => $item->request_count,
                    'total_quantity_requested' => $item->total_quantity_requested,
                    'utilization_rate' => $item->utilization_rate,
                    'total_quantity' => $item->total_quantity,
                    'available_quantity' => $item->available_quantity
                ];
            })
            ->toArray();
    }

    /**
     * Get least used equipment (includes equipment with zero usage)
     */
    private function getLeastUsedEquipment($baseQuery, int $limit): array
    {
        // Get equipment with usage
        $usedEquipment = $baseQuery->clone()
            ->select([
                'equipment.id',
                'equipment.name',
                'equipment.model',
                'equipment.total_quantity',
                'equipment.available_quantity',
                DB::raw('COUNT(borrow_request_items.id) as request_count'),
                DB::raw('SUM(borrow_request_items.quantity_requested) as total_quantity_requested')
            ])
            ->groupBy(['equipment.id', 'equipment.name', 'equipment.model', 'equipment.total_quantity', 'equipment.available_quantity'])
            ->orderBy('request_count')
            ->get();

        // Get unused equipment
        $unusedEquipment = Equipment::select([
                'id', 'name', 'model', 'total_quantity', 'available_quantity'
            ])
            ->whereNotIn('id', $usedEquipment->pluck('id'))
            ->where('status', 'active')
            ->limit($limit)
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'name' => $item->name,
                    'model' => $item->model,
                    'request_count' => 0,
                    'total_quantity_requested' => 0,
                    'utilization_rate' => 0,
                    'total_quantity' => $item->total_quantity,
                    'available_quantity' => $item->available_quantity
                ];
            });

        // Combine and take the least used
        $combined = $usedEquipment->map(function ($item) {
                return [
                    'id' => $item->id,
                    'name' => $item->name,
                    'model' => $item->model,
                    'request_count' => $item->request_count,
                    'total_quantity_requested' => $item->total_quantity_requested,
                    'utilization_rate' => round(($item->total_quantity_requested / $item->total_quantity) * 100, 2),
                    'total_quantity' => $item->total_quantity,
                    'available_quantity' => $item->available_quantity
                ];
            })
            ->concat($unusedEquipment)
            ->sortBy('request_count')
            ->take($limit);

        return $combined->values()->toArray();
    }

    /**
     * Get usage statistics by equipment category
     */
    private function getUsageByCategoryData(string $dateFrom, string $dateTo, array $filters): array
    {
        $query = Category::with(['equipment'])
            ->leftJoin('equipment', 'categories.id', '=', 'equipment.category_id')
            ->leftJoin('borrow_request_items', 'equipment.id', '=', 'borrow_request_items.equipment_id')
            ->leftJoin('borrow_requests', 'borrow_request_items.borrow_request_id', '=', 'borrow_requests.id');

        if (!($filters['category_id'] ?? null)) {
            $query->whereBetween('borrow_requests.created_at', [$dateFrom . ' 00:00:00', $dateTo . ' 23:59:59']);
        }

        return $query->select([
                'categories.id',
                'categories.name',
                DB::raw('COUNT(DISTINCT equipment.id) as total_equipment'),
                DB::raw('COUNT(borrow_request_items.id) as total_requests'),
                DB::raw('SUM(borrow_request_items.quantity_requested) as total_quantity_requested'),
                DB::raw('SUM(equipment.total_quantity) as total_capacity')
            ])
            ->groupBy(['categories.id', 'categories.name'])
            ->orderByDesc('total_requests')
            ->get()
            ->map(function ($category) {
                $utilizationRate = $category->total_capacity > 0 
                    ? round(($category->total_quantity_requested / $category->total_capacity) * 100, 2)
                    : 0;

                return [
                    'category_id' => $category->id,
                    'category_name' => $category->name,
                    'total_equipment' => $category->total_equipment,
                    'total_requests' => $category->total_requests,
                    'total_quantity_requested' => $category->total_quantity_requested,
                    'total_capacity' => $category->total_capacity,
                    'utilization_rate' => $utilizationRate
                ];
            })
            ->toArray();
    }

    /**
     * Get equipment availability trends over time
     */
    private function getAvailabilityTrends(string $dateFrom, string $dateTo, array $filters): array
    {
        // This is a simplified version - in a real implementation, you might want to store historical data
        $periods = collect();
        $current = Carbon::parse($dateFrom);
        $end = Carbon::parse($dateTo);

        while ($current->lte($end)) {
            $periodStart = $current->copy()->startOfWeek();
            $periodEnd = $current->copy()->endOfWeek();

            $requestsInPeriod = BorrowRequestItem::query()
                ->join('borrow_requests', 'borrow_request_items.borrow_request_id', '=', 'borrow_requests.id')
                ->whereBetween('borrow_requests.created_at', [$periodStart, $periodEnd])
                ->when($filters['category_id'] ?? null, function ($query, $categoryId) {
                    return $query->join('equipment', 'borrow_request_items.equipment_id', '=', 'equipment.id')
                                 ->where('equipment.category_id', $categoryId);
                })
                ->sum('quantity_requested');

            $periods->push([
                'period' => $current->format('Y-m-d'),
                'period_label' => $current->format('M d'),
                'requests_count' => $requestsInPeriod,
                'week_start' => $periodStart->format('Y-m-d'),
                'week_end' => $periodEnd->format('Y-m-d')
            ]);

            $current->addWeek();
        }

        return $periods->toArray();
    }

    /**
     * Get peak usage analysis (time patterns)
     */
    private function getPeakUsageAnalysis(string $dateFrom, string $dateTo, array $filters): array
    {
        $baseQuery = BorrowRequest::whereBetween('created_at', [$dateFrom . ' 00:00:00', $dateTo . ' 23:59:59']);

        // Peak days of week
        $dayOfWeekData = $baseQuery->clone()
            ->select([
                DB::raw('DAYOFWEEK(created_at) as day_number'),
                DB::raw('DAYNAME(created_at) as day_name'),
                DB::raw('COUNT(*) as request_count')
            ])
            ->groupBy([DB::raw('DAYOFWEEK(created_at)'), DB::raw('DAYNAME(created_at)')])
            ->orderByDesc('request_count')
            ->get()
            ->toArray();

        // Peak hours
        $hourlyData = $baseQuery->clone()
            ->select([
                DB::raw('HOUR(created_at) as hour'),
                DB::raw('COUNT(*) as request_count')
            ])
            ->groupBy([DB::raw('HOUR(created_at)')])
            ->orderBy('hour')
            ->get()
            ->map(function ($item) {
                return [
                    'hour' => $item->hour,
                    'hour_label' => sprintf('%02d:00', $item->hour),
                    'request_count' => $item->request_count
                ];
            })
            ->toArray();

        // Peak months
        $monthlyData = $baseQuery->clone()
            ->select([
                DB::raw('MONTH(created_at) as month'),
                DB::raw('MONTHNAME(created_at) as month_name'),
                DB::raw('YEAR(created_at) as year'),
                DB::raw('COUNT(*) as request_count')
            ])
            ->groupBy([DB::raw('YEAR(created_at)'), DB::raw('MONTH(created_at)'), DB::raw('MONTHNAME(created_at)')])
            ->orderByDesc('request_count')
            ->get()
            ->toArray();

        return [
            'peak_days_of_week' => $dayOfWeekData,
            'peak_hours' => $hourlyData,
            'peak_months' => $monthlyData
        ];
    }

    /**
     * Get equipment utilization metrics
     */
    private function getUtilizationMetrics(string $dateFrom, string $dateTo, array $filters): array
    {
        $query = Equipment::query()
            ->leftJoin('borrow_request_items', 'equipment.id', '=', 'borrow_request_items.equipment_id')
            ->leftJoin('borrow_requests', 'borrow_request_items.borrow_request_id', '=', 'borrow_requests.id')
            ->whereBetween('borrow_requests.created_at', [$dateFrom . ' 00:00:00', $dateTo . ' 23:59:59']);

        if ($filters['category_id'] ?? null) {
            $query->where('equipment.category_id', $filters['category_id']);
        }

        if ($filters['status'] ?? null) {
            $query->where('equipment.status', $filters['status']);
        }

        $metrics = $query->select([
                DB::raw('AVG(CASE WHEN equipment.total_quantity > 0 THEN (equipment.total_quantity - equipment.available_quantity) / equipment.total_quantity * 100 ELSE 0 END) as avg_utilization_rate'),
                DB::raw('MAX(CASE WHEN equipment.total_quantity > 0 THEN (equipment.total_quantity - equipment.available_quantity) / equipment.total_quantity * 100 ELSE 0 END) as max_utilization_rate'),
                DB::raw('MIN(CASE WHEN equipment.total_quantity > 0 THEN (equipment.total_quantity - equipment.available_quantity) / equipment.total_quantity * 100 ELSE 0 END) as min_utilization_rate'),
                DB::raw('COUNT(DISTINCT equipment.id) as total_equipment_analyzed')
            ])
            ->first();

        return [
            'average_utilization_rate' => round($metrics->avg_utilization_rate ?? 0, 2),
            'max_utilization_rate' => round($metrics->max_utilization_rate ?? 0, 2),
            'min_utilization_rate' => round($metrics->min_utilization_rate ?? 0, 2),
            'total_equipment_analyzed' => $metrics->total_equipment_analyzed ?? 0
        ];
    }

    /**
     * Get request volume trends over time
     */
    private function getRequestVolumeTrends(string $dateFrom, string $dateTo, string $grouping, array $filters): array
    {
        $format = match($grouping) {
            'daily' => '%Y-%m-%d',
            'weekly' => '%Y-%u',
            'monthly' => '%Y-%m',
            default => '%Y-%m-%d'
        };

        // Base queries for all request types
        $borrowQuery = BorrowRequest::whereBetween('created_at', [$dateFrom . ' 00:00:00', $dateTo . ' 23:59:59']);
        $visitQuery = VisitRequest::whereBetween('created_at', [$dateFrom . ' 00:00:00', $dateTo . ' 23:59:59']);
        $testingQuery = TestingRequest::whereBetween('created_at', [$dateFrom . ' 00:00:00', $dateTo . ' 23:59:59']);

        // Apply status filter if provided
        if ($filters['status'] ?? null) {
            $borrowQuery->where('status', $filters['status']);
            $visitQuery->where('status', $filters['status']);
            $testingQuery->where('status', $filters['status']);
        }

        // Get data based on request type filter
        $trends = collect();

        if (!($filters['request_type'] ?? null) || $filters['request_type'] === 'borrow') {
            $borrowTrends = $borrowQuery->select([
                    DB::raw("DATE_FORMAT(created_at, '{$format}') as period"),
                    DB::raw('COUNT(*) as borrow_count')
                ])
                ->groupBy(DB::raw("DATE_FORMAT(created_at, '{$format}')"))
                ->get();
            $trends = $trends->concat($borrowTrends);
        }

        if (!($filters['request_type'] ?? null) || $filters['request_type'] === 'visit') {
            $visitTrends = $visitQuery->select([
                    DB::raw("DATE_FORMAT(created_at, '{$format}') as period"),
                    DB::raw('COUNT(*) as visit_count')
                ])
                ->groupBy(DB::raw("DATE_FORMAT(created_at, '{$format}')"))
                ->get();
            $trends = $trends->concat($visitTrends);
        }

        if (!($filters['request_type'] ?? null) || $filters['request_type'] === 'testing') {
            $testingTrends = $testingQuery->select([
                    DB::raw("DATE_FORMAT(created_at, '{$format}') as period"),
                    DB::raw('COUNT(*) as testing_count')
                ])
                ->groupBy(DB::raw("DATE_FORMAT(created_at, '{$format}')"))
                ->get();
            $trends = $trends->concat($testingTrends);
        }

        // Combine and format data
        return $trends->groupBy('period')
            ->map(function ($items, $period) use ($grouping) {
                $borrowCount = $items->sum('borrow_count') ?? 0;
                $visitCount = $items->sum('visit_count') ?? 0;
                $testingCount = $items->sum('testing_count') ?? 0;

                return [
                    'period' => $period,
                    'period_label' => $this->formatPeriodLabel($period, $grouping),
                    'borrow_requests' => $borrowCount,
                    'visit_requests' => $visitCount,
                    'testing_requests' => $testingCount,
                    'total_requests' => $borrowCount + $visitCount + $testingCount
                ];
            })
            ->sortBy('period')
            ->values()
            ->toArray();
    }

    /**
     * Get request type distribution
     */
    private function getRequestTypeDistribution(string $dateFrom, string $dateTo, array $filters): array
    {
        $statusFilter = $filters['status'] ?? null;

        $borrowCount = BorrowRequest::whereBetween('created_at', [$dateFrom . ' 00:00:00', $dateTo . ' 23:59:59'])
            ->when($statusFilter, fn($query) => $query->where('status', $statusFilter))
            ->count();

        $visitCount = VisitRequest::whereBetween('created_at', [$dateFrom . ' 00:00:00', $dateTo . ' 23:59:59'])
            ->when($statusFilter, fn($query) => $query->where('status', $statusFilter))
            ->count();

        $testingCount = TestingRequest::whereBetween('created_at', [$dateFrom . ' 00:00:00', $dateTo . ' 23:59:59'])
            ->when($statusFilter, fn($query) => $query->where('status', $statusFilter))
            ->count();

        $total = $borrowCount + $visitCount + $testingCount;

        return [
            [
                'type' => 'borrow',
                'label' => 'Equipment Borrow',
                'count' => $borrowCount,
                'percentage' => $total > 0 ? round(($borrowCount / $total) * 100, 2) : 0
            ],
            [
                'type' => 'visit',
                'label' => 'Lab Visit',
                'count' => $visitCount,
                'percentage' => $total > 0 ? round(($visitCount / $total) * 100, 2) : 0
            ],
            [
                'type' => 'testing',
                'label' => 'Testing Service',
                'count' => $testingCount,
                'percentage' => $total > 0 ? round(($testingCount / $total) * 100, 2) : 0
            ]
        ];
    }

    /**
     * Get processing metrics (time analysis)
     */
    private function getProcessingMetrics(string $dateFrom, string $dateTo, array $filters): array
    {
        // Get all requests with processing times
        $requests = collect();

        if (!($filters['request_type'] ?? null) || $filters['request_type'] === 'borrow') {
            $borrowRequests = BorrowRequest::whereBetween('created_at', [$dateFrom . ' 00:00:00', $dateTo . ' 23:59:59'])
                ->whereNotNull('reviewed_at')
                ->when($filters['status'] ?? null, fn($query, $status) => $query->where('status', $status))
                ->select(['submitted_at', 'reviewed_at', 'status', DB::raw("'borrow' as type")])
                ->get();
            $requests = $requests->concat($borrowRequests);
        }

        if (!($filters['request_type'] ?? null) || $filters['request_type'] === 'visit') {
            $visitRequests = VisitRequest::whereBetween('created_at', [$dateFrom . ' 00:00:00', $dateTo . ' 23:59:59'])
                ->whereNotNull('reviewed_at')
                ->when($filters['status'] ?? null, fn($query, $status) => $query->where('status', $status))
                ->select(['submitted_at', 'reviewed_at', 'status', DB::raw("'visit' as type")])
                ->get();
            $requests = $requests->concat($visitRequests);
        }

        if (!($filters['request_type'] ?? null) || $filters['request_type'] === 'testing') {
            $testingRequests = TestingRequest::whereBetween('created_at', [$dateFrom . ' 00:00:00', $dateTo . ' 23:59:59'])
                ->whereNotNull('reviewed_at')
                ->when($filters['status'] ?? null, fn($query, $status) => $query->where('status', $status))
                ->select(['submitted_at', 'reviewed_at', 'status', DB::raw("'testing' as type")])
                ->get();
            $requests = $requests->concat($testingRequests);
        }

        // Calculate processing times
        $processingTimes = $requests->map(function ($request) {
            return [
                'type' => $request->type,
                'status' => $request->status,
                'processing_time_hours' => Carbon::parse($request->submitted_at)->diffInHours(Carbon::parse($request->reviewed_at))
            ];
        });

        $avgProcessingTime = $processingTimes->avg('processing_time_hours') ?? 0;
        $minProcessingTime = $processingTimes->min('processing_time_hours') ?? 0;
        $maxProcessingTime = $processingTimes->max('processing_time_hours') ?? 0;

        // Processing times by type
        $byType = $processingTimes->groupBy('type')->map(function ($items, $type) {
            return [
                'type' => $type,
                'count' => $items->count(),
                'avg_processing_time' => round($items->avg('processing_time_hours') ?? 0, 2),
                'min_processing_time' => $items->min('processing_time_hours') ?? 0,
                'max_processing_time' => $items->max('processing_time_hours') ?? 0
            ];
        })->values();

        return [
            'average_processing_time' => round($avgProcessingTime, 2),
            'min_processing_time' => $minProcessingTime,
            'max_processing_time' => $maxProcessingTime,
            'total_processed_requests' => $processingTimes->count(),
            'by_request_type' => $byType->toArray()
        ];
    }

    /**
     * Get success/rejection rates
     */
    private function getSuccessRates(string $dateFrom, string $dateTo, array $filters): array
    {
        $results = [];

        $requestTypes = ['borrow', 'visit', 'testing'];
        if ($filters['request_type'] ?? null) {
            $requestTypes = [$filters['request_type']];
        }

        foreach ($requestTypes as $type) {
            $modelClass = match($type) {
                'borrow' => BorrowRequest::class,
                'visit' => VisitRequest::class,
                'testing' => TestingRequest::class,
            };

            $total = $modelClass::whereBetween('created_at', [$dateFrom . ' 00:00:00', $dateTo . ' 23:59:59'])->count();
            $approved = $modelClass::whereBetween('created_at', [$dateFrom . ' 00:00:00', $dateTo . ' 23:59:59'])
                ->whereIn('status', ['approved', 'active', 'completed'])->count();
            $rejected = $modelClass::whereBetween('created_at', [$dateFrom . ' 00:00:00', $dateTo . ' 23:59:59'])
                ->where('status', 'rejected')->count();
            $completed = $modelClass::whereBetween('created_at', [$dateFrom . ' 00:00:00', $dateTo . ' 23:59:59'])
                ->where('status', 'completed')->count();

            $results[] = [
                'request_type' => $type,
                'total_requests' => $total,
                'approved_count' => $approved,
                'rejected_count' => $rejected,
                'completed_count' => $completed,
                'approval_rate' => $total > 0 ? round(($approved / $total) * 100, 2) : 0,
                'rejection_rate' => $total > 0 ? round(($rejected / $total) * 100, 2) : 0,
                'completion_rate' => $approved > 0 ? round(($completed / $approved) * 100, 2) : 0
            ];
        }

        return $results;
    }

    /**
     * Get popular equipment from all requests
     */
    private function getPopularEquipmentFromRequests(string $dateFrom, string $dateTo, array $filters): array
    {
        return BorrowRequestItem::query()
            ->join('equipment', 'borrow_request_items.equipment_id', '=', 'equipment.id')
            ->join('borrow_requests', 'borrow_request_items.borrow_request_id', '=', 'borrow_requests.id')
            ->join('categories', 'equipment.category_id', '=', 'categories.id')
            ->whereBetween('borrow_requests.created_at', [$dateFrom . ' 00:00:00', $dateTo . ' 23:59:59'])
            ->when($filters['status'] ?? null, function ($query, $status) {
                return $query->where('borrow_requests.status', $status);
            })
            ->select([
                'equipment.id',
                'equipment.name',
                'equipment.model',
                'categories.name as category_name',
                DB::raw('COUNT(borrow_request_items.id) as request_count'),
                DB::raw('SUM(borrow_request_items.quantity_requested) as total_quantity_requested')
            ])
            ->groupBy(['equipment.id', 'equipment.name', 'equipment.model', 'categories.name'])
            ->orderByDesc('request_count')
            ->limit(15)
            ->get()
            ->map(function ($item) {
                return [
                    'equipment_id' => $item->id,
                    'equipment_name' => $item->name,
                    'equipment_model' => $item->model,
                    'category_name' => $item->category_name,
                    'request_count' => $item->request_count,
                    'total_quantity_requested' => $item->total_quantity_requested
                ];
            })
            ->toArray();
    }

    /**
     * Get peak periods for requests
     */
    private function getRequestPeakPeriods(string $dateFrom, string $dateTo, array $filters): array
    {
        // This combines all request types unless filtered
        $queries = [];

        if (!($filters['request_type'] ?? null) || $filters['request_type'] === 'borrow') {
            $queries[] = BorrowRequest::whereBetween('created_at', [$dateFrom . ' 00:00:00', $dateTo . ' 23:59:59'])
                ->when($filters['status'] ?? null, fn($q) => $q->where('status', $filters['status']))
                ->select(['created_at']);
        }

        if (!($filters['request_type'] ?? null) || $filters['request_type'] === 'visit') {
            $queries[] = VisitRequest::whereBetween('created_at', [$dateFrom . ' 00:00:00', $dateTo . ' 23:59:59'])
                ->when($filters['status'] ?? null, fn($q) => $q->where('status', $filters['status']))
                ->select(['created_at']);
        }

        if (!($filters['request_type'] ?? null) || $filters['request_type'] === 'testing') {
            $queries[] = TestingRequest::whereBetween('created_at', [$dateFrom . ' 00:00:00', $dateTo . ' 23:59:59'])
                ->when($filters['status'] ?? null, fn($q) => $q->where('status', $filters['status']))
                ->select(['created_at']);
        }

        // Combine all queries
        $allRequests = collect();
        foreach ($queries as $query) {
            $allRequests = $allRequests->concat($query->get());
        }

        // Group by hour of day
        $hourlyPeaks = $allRequests->groupBy(function ($request) {
            return Carbon::parse($request->created_at)->format('H');
        })->map(function ($requests, $hour) {
            return [
                'hour' => (int) $hour,
                'hour_label' => sprintf('%02d:00', $hour),
                'request_count' => $requests->count()
            ];
        })->sortBy('hour')->values();

        // Group by day of week
        $weeklyPeaks = $allRequests->groupBy(function ($request) {
            return Carbon::parse($request->created_at)->dayOfWeek;
        })->map(function ($requests, $dayOfWeek) {
            $dayNames = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
            return [
                'day_of_week' => $dayOfWeek,
                'day_name' => $dayNames[$dayOfWeek],
                'request_count' => $requests->count()
            ];
        })->sortBy('day_of_week')->values();

        return [
            'hourly_peaks' => $hourlyPeaks->toArray(),
            'weekly_peaks' => $weeklyPeaks->toArray(),
            'total_requests_analyzed' => $allRequests->count()
        ];
    }

    /**
     * Get total requests count with filters
     */
    private function getTotalRequestsCount(string $dateFrom, string $dateTo, array $filters): int
    {
        $total = 0;

        if (!($filters['request_type'] ?? null) || $filters['request_type'] === 'borrow') {
            $total += BorrowRequest::whereBetween('created_at', [$dateFrom . ' 00:00:00', $dateTo . ' 23:59:59'])
                ->when($filters['status'] ?? null, fn($query) => $query->where('status', $filters['status']))
                ->count();
        }

        if (!($filters['request_type'] ?? null) || $filters['request_type'] === 'visit') {
            $total += VisitRequest::whereBetween('created_at', [$dateFrom . ' 00:00:00', $dateTo . ' 23:59:59'])
                ->when($filters['status'] ?? null, fn($query) => $query->where('status', $filters['status']))
                ->count();
        }

        if (!($filters['request_type'] ?? null) || $filters['request_type'] === 'testing') {
            $total += TestingRequest::whereBetween('created_at', [$dateFrom . ' 00:00:00', $dateTo . ' 23:59:59'])
                ->when($filters['status'] ?? null, fn($query) => $query->where('status', $filters['status']))
                ->count();
        }

        return $total;
    }

    /**
     * Format period label based on grouping
     */
    private function formatPeriodLabel(string $period, string $grouping): string
    {
        return match($grouping) {
            'daily' => Carbon::parse($period)->format('M d, Y'),
            'weekly' => 'Week ' . $period,
            'monthly' => Carbon::createFromFormat('Y-m', $period)->format('M Y'),
            default => $period
        };
    }
}