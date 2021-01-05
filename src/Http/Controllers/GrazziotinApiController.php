<?php

namespace Grazziotin\GrazziotinApi\Http\Controllers;

use Grazziotin\GrazziotinApi\Services\GrazziotinApiIncludeService;
use Laravel\Lumen\Routing\Controller as BaseController;

class GrazziotinApiController extends BaseController
{
    protected $repository;
    protected $resource_class;
    protected $rules = [];
    protected $include = [];

    protected function send($data = null, $status = Response::HTTP_OK)
    {
        return response()->json($data, $status);
    }

    protected function sendCollection($collection, $extra = [])
    {
        $include = isset($extra['include']) ? $extra['include'] : null;
        $status = isset($extra['status']) ? $extra['status'] : Response::HTTP_OK;
        $send = [
            'data' => $collection,
        ];
        $included = GrazziotinApiIncludeService::include($collection, $include);
        if (count($included) > 0) {
            $send['included'] = $included;
        }

        return $this->send($send, $status);
    }

    protected function sendResource($resource, $extra = [])
    {
        $include = isset($extra['include']) ? $extra['include'] : null;
        $status = isset($extra['status']) ? $extra['status'] : Response::HTTP_OK;
        $send = [
            'data' => $resource,
        ];
        $included = GrazziotinApiIncludeService::include($resource, $include);
        if ($included) {
            $send['included'] = $included;
        }
        return $this->send($send, $status);
    }
}
