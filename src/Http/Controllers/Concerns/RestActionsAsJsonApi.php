<?php

namespace Grazziotin\GrazziotinApi\Http\Controllers\Concerns;

use Grazziotin\GrazziotinApi\Services\GrazziotinApiIncludeService;
use Illuminate\Http\Response;

trait RestActionsAsJsonApi
{
    protected $repository;
    protected $resource_class;
    protected $rules = [];
    protected $include = [];

    protected function send($data = null, $status = Response::HTTP_OK)
    {
//        if ($data && isset($data['data'])) {
//            $data['meta'] = $links = $this->repository->meta($data['data']);
//            $data['links'] = $links = $this->repository->links($data['data']);
//        }
        return response()->json($data, $status);
    }

    protected function noContent()
    {
        return $this->send(null, Response::HTTP_NO_CONTENT);
    }

    protected function collection($data)
    {
        if ($this->resource_class) {
            return $this->resource_class::collection($data);
        }
        return $data;
    }

    private function resource($data)
    {
        if ($this->resource_class) {
            return new $this->resource_class($data);
        }
        return $data;
    }

    public function sendCollection($collection, $extra = [])
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

    public function sendResource($resource, $extra = [])
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
