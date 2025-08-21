<?php

namespace App\Http\Resources;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Pagination\LengthAwarePaginator;

class ApiResponse
{
    /**
     * Create a successful API response
     */
    public static function success(
        mixed $data = null,
        string $message = 'Operation completed successfully',
        int $statusCode = 200,
        array $meta = []
    ): JsonResponse {
        $response = [
            'success' => true,
            'message' => $message,
        ];

        if ($data !== null) {
            $response['data'] = $data;
        }

        if (!empty($meta)) {
            $response['meta'] = $meta;
        }

        return response()->json($response, $statusCode);
    }

    /**
     * Create an error API response
     */
    public static function error(
        string $message = 'An error occurred',
        int $statusCode = 500,
        mixed $errors = null,
        mixed $debug = null
    ): JsonResponse {
        $response = [
            'success' => false,
            'message' => $message,
        ];

        if ($errors !== null) {
            $response['errors'] = $errors;
        }

        // Include debug info only in debug mode
        if ($debug !== null && config('app.debug')) {
            $response['debug'] = $debug;
        }

        return response()->json($response, $statusCode);
    }

    /**
     * Create a paginated API response
     */
    public static function paginated(
        LengthAwarePaginator $paginator,
        string $resourceClass,
        string $message = 'Data retrieved successfully',
        array $additionalMeta = []
    ): JsonResponse {
        $data = $resourceClass::collection($paginator->items());
        
        $meta = [
            'pagination' => [
                'current_page' => $paginator->currentPage(),
                'last_page' => $paginator->lastPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
                'from' => $paginator->firstItem(),
                'to' => $paginator->lastItem(),
                'has_more_pages' => $paginator->hasMorePages(),
                'prev_page_url' => $paginator->previousPageUrl(),
                'next_page_url' => $paginator->nextPageUrl(),
            ]
        ];

        // Merge additional meta data
        $meta = array_merge($meta, $additionalMeta);

        return self::success($data, $message, 200, $meta);
    }

    /**
     * Create a collection API response (non-paginated)
     */
    public static function collection(
        $collection,
        string $resourceClass,
        string $message = 'Data retrieved successfully',
        array $meta = []
    ): JsonResponse {
        $data = $resourceClass::collection($collection);
        
        return self::success($data, $message, 200, $meta);
    }

    /**
     * Create a single resource API response
     */
    public static function resource(
        $resource,
        string $resourceClass,
        string $message = 'Data retrieved successfully',
        int $statusCode = 200,
        array $meta = []
    ): JsonResponse {
        $data = new $resourceClass($resource);
        
        return self::success($data, $message, $statusCode, $meta);
    }

    /**
     * Create a created resource response
     */
    public static function created(
        mixed $data = null,
        string $message = 'Resource created successfully',
        array $meta = []
    ): JsonResponse {
        return self::success($data, $message, 201, $meta);
    }

    /**
     * Create a no content response
     */
    public static function noContent(string $message = 'Operation completed successfully'): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message,
        ], 204);
    }

    /**
     * Create a validation error response
     */
    public static function validationError(
        array $errors,
        string $message = 'Validation failed'
    ): JsonResponse {
        return self::error($message, 422, $errors);
    }

    /**
     * Create a not found response
     */
    public static function notFound(string $message = 'Resource not found'): JsonResponse
    {
        return self::error($message, 404);
    }

    /**
     * Create an unauthorized response
     */
    public static function unauthorized(string $message = 'Unauthorized access'): JsonResponse
    {
        return self::error($message, 401);
    }

    /**
     * Create a forbidden response
     */
    public static function forbidden(string $message = 'Access forbidden'): JsonResponse
    {
        return self::error($message, 403);
    }
}