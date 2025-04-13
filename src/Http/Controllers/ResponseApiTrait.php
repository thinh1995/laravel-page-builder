<?php

declare(strict_types=1);

namespace Thinhnx\LaravelPageBuilder\Http\Controllers;

use Closure;
use Exception;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Pagination\LengthAwarePaginator as DefaultLengthAwarePaginator;
use Illuminate\Support\Collection as SupportCollection;
use League\Fractal\Manager;
use League\Fractal\Pagination\IlluminatePaginatorAdapter;
use League\Fractal\Resource\Collection;
use League\Fractal\Resource\Item;
use League\Fractal\Resource\NullResource;
use League\Fractal\Serializer\DataArraySerializer;
use League\Fractal\TransformerAbstract;
use StdClass;

trait ResponseApiTrait
{
    /**
     * Status code of response
     *
     * @var int
     */
    protected $statusCode = 200;

    /**
     * Fractal manager instance
     *
     * @var Manager
     */
    protected $fractal;

    /**
     * Getter for statusCode
     *
     * @return mixed
     */
    public function getStatusCode()
    {
        return $this->statusCode;
    }

    /**
     * Setter for statusCode
     *
     * @param int $statusCode Value to set
     *
     * @return self
     */
    public function setStatusCode(int $statusCode)
    {
        $this->statusCode = $statusCode;

        return $this;
    }

    /**
     * Send custom data response
     *
     * @param $status
     * @param $message
     *
     * @return JsonResponse
     */
    public function sendCustomResponse($status, $message)
    {
        return response()->json(['status' => $status, 'message' => $message], $status);
    }

    /**
     * Send generic error response
     *
     * @param $message
     *
     * @return JsonResponse
     */
    public function sendErrorResponse($message)
    {
        return response()->json(['status' => 400, 'message' => $message], 400);
    }

    /**
     * Send this response when api user provide fields that doesn't exist in our application
     *
     * @param $errors
     *
     * @return mixed
     */
    public function sendUnknownFieldResponse($errors)
    {
        return response()->json((['status' => 400, 'unknown_fields' => $errors]), 400);
    }

    /**
     * Send this response when api user provide filter that doesn't exist in our application
     *
     * @param $errors
     *
     * @return mixed
     */
    public function sendInvalidFilterResponse($errors)
    {
        return response()->json((['status' => 400, 'invalid_filters' => $errors]), 400);
    }

    /**
     * Send this response when api user provide incorrect data type for the field
     *
     * @param $errors
     *
     * @return mixed
     */
    public function sendInvalidFieldResponse($errors)
    {
        return response()->json((['status' => 400, 'invalid_fields' => $errors]), 400);
    }

    /**
     * Send this response when a api user try access a resource that they don't belong
     *
     * @return string
     */
    public function sendForbiddenResponse()
    {
        return response()->json(['status' => 403, 'message' => 'Forbidden'], 403);
    }

    /**
     * Send 404 not found response
     *
     * @param string $message
     *
     * @return JsonResponse
     */
    public function sendNotFoundResponse(string $message = '')
    {
        if ($message === '') {
            $message = 'The requested resource was not found';
        }

        return response()->json(['status' => 404, 'message' => $message], 404);
    }

    /**
     * Send empty data response
     *
     * @return JsonResponse
     */
    public function sendEmptyDataResponse()
    {
        return $this->respondWithArray(['data' => new StdClass()]);
    }

    /**
     * Return a json response from the application
     *
     * @param array $array
     * @param array $headers
     *
     * @return JsonResponse
     */
    protected function respondWithArray(array $array, array $headers = [])
    {
        return response()->json($array, $this->statusCode, $headers);
    }

    /**
     * Set fractal Manager instance
     *
     * @param Manager $fractal
     *
     * @return void
     */
    protected function setFractal(Manager $fractal)
    {
        $request = app('request');
        $include = $request->query('include');
        $exclude = $request->query('exclude');
        $fields  = $request->query('fields');

        if ($include) {
            $fractal->parseIncludes($include);
        }
        if ($exclude) {
            $fractal->parseExcludes($exclude);
        }
        if ($fields) {
            $fractal->parseFieldsets($fields);
        }
        $fractal->setSerializer(new DataArraySerializer());
        $this->fractal = $fractal;
    }

    /**
     * @param array|LengthAwarePaginator|EloquentCollection $collection
     * @param Closure|TransformerAbstract                   $callback
     * @param string                                        $resourceKey
     *
     * @return JsonResponse
     * @throws Exception
     */
    protected function respondWithCollection($collection, $callback, $resourceKey)
    {
        if (method_exists($callback, "collection")) {
            $resource = $callback->collection($collection, $callback, $resourceKey);
        } else {
            $resource = new Collection($collection, $callback, $resourceKey);
        }

        // set empty data pagination
        if (empty($collection)) {
            $collection = new DefaultLengthAwarePaginator([], 0, 10);
            $resource   = new Collection($collection, $callback, $resourceKey);
        }
        $resource->setPaginator(new IlluminatePaginatorAdapter($collection));
        $rootScope = $this->fractal->createData($resource);

        return $this->respondWithArray($rootScope->toArray());
    }

    /**
     * Return collection response from the application
     *
     * @param array|SupportCollection     $collection
     * @param Closure|TransformerAbstract $callback
     * @param string                      $resourceKey
     *
     * @return JsonResponse
     */
    protected function respondAllWithCollection($collection, $callback, $resourceKey)
    {
        $resource  = $collection ? new Collection($collection, $callback, $resourceKey) : new NullResource();
        $rootScope = $this->fractal->createData($resource);

        return $this->respondWithArray($rootScope->toArray());
    }

    /**
     * Return single item response from the application
     *
     * @param Model|array                 $item
     * @param Closure|TransformerAbstract $callback
     * @param string                      $resourceKey
     *
     * @return JsonResponse
     */
    protected function respondWithItem($item, $callback, $resourceKey)
    {
        $resource  = $item ? new Item($item, $callback, $resourceKey) : new NullResource();
        $rootScope = $this->fractal->createData($resource);

        return $this->respondWithArray($rootScope->toArray());
    }
}
