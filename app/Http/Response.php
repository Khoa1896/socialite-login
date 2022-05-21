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
}
