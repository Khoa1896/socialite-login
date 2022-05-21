<?php

namespace App\Http;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Response as HttpResponse;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class Response extends HttpResponse
{
    /**
     * @param mixed $resource
     * @return ResponseFactory|HttpResponse
     */
    public static function single($resource)
    {
        return static::ok($resource, $count = 1);
    }

    /**
     * @param mixed $data
     * @param int $recordCount
     *
     * @return ResponseFactory|HttpResponse
     */
    public static function ok($data = [], int $recordCount = 0, string $message = null)
    {
        return new static([
            'success' => true,
            'record_count' => $recordCount,
            'data' => $data,
            'message' => $message
        ]);
    }

    public static function collect($data = [])
    {
        return new static([
            'success' => true,
            'record_count' => count($data),
            'data' => $data
        ]);
    }

    public static function collection(Collection $collection, $total = null)
    {
        return [
            'success' => true,
            'record_count' => $total ?: $collection->count(),
            'data' => $collection
        ];
    }

    public static function paginated(LengthAwarePaginator $paginator, $transformer = null)
    {
        /** @var Collection $items */
        $items = $paginator->getCollection();

        if (is_callable($transformer)) {
            $items->transform($transformer);
        } else if (is_string($transformer) && class_exists($transformer)) {
            $items = $items->mapInto($transformer);
        }

        return new static([
            'success' => true,
            'record_count' => $paginator->total(),
            'data' => $items,
            'page' => $paginator->currentPage(),
            'next' => $paginator->nextPageUrl(),
            'from' => $paginator->firstItem(),
            'to' => $paginator->lastItem(),
            'total_page' => $paginator->lastPage(),
            'per_page' => $paginator->perPage(),
        ]);
    }

    public static function unauthorized(string $code = 'unauthorized', string $message = null)
    {
        return static::error($code, $message, HttpResponse::HTTP_UNAUTHORIZED);
    }

    public static function forbidden(string $code = 'forbidden', string $message = null)
    {
        return static::error($code, $message, Response::HTTP_FORBIDDEN);
    }

    public static function error(string $code = 'unknown_error', string $message = null, int $statusCode = 500)
    {
        return new static([
            'success' => false,
            'message' => $message ?? ucwords(Str::snake($code, ' ')),
            'record_count' => 0,
            'data' => [],
            'error_code' => $code,
        ], $statusCode);
    }

    public static function conflict(string $code = 'conflict', string $message = null)
    {
        return static::error($code, $message, Response::HTTP_CONFLICT);
    }

    public static function badRequest(string $code = 'bad_request', string $message = null)
    {
        return static::error($code, $message, HttpResponse::HTTP_BAD_REQUEST);
    }

    public static function validationError($errors= [], $message = 'The given data was invalid.'){

        return response()->json([
            'success' => false,
            'record_count' => 0,
            'data' => [],
            'error_code' => 422,
            'message' => $message,
            'errors' => $errors,

        ], 422);
    }
}
