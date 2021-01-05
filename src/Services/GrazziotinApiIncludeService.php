<?php

namespace Grazziotin\GrazziotinApi\Services;

use Grazziotin\GrazziotinApi\Http\Resources\Base\GrazziotinApiResource;
use Illuminate\Pagination\LengthAwarePaginator;

class GrazziotinApiIncludeService
{
    private static function uniqueResource($collection)
    {
        if (is_array($collection)) {
            $collection = collect($collection);
        }
        return $collection->filter(function ($item) {
            return isset($item['id']) && isset($item['type']);
        })->unique(function ($item) {
            return $item['id'].$item['type'];
        })->values();
    }

    private static function includes($include, $resource)
    {
        if (!isset($include)) {
            return null;
        }
        $includes = collect();
        collect($include)->each(function ($name) use ($resource, &$includes) {
            if(get_parent_class($resource) === GrazziotinApiResource::class) {
                $included = $resource->include($name);
                if (isset($included) && !$included->isEmpty()) {
                    $includes = $includes->concat($included);
                }
            }
        });
        if (count($includes) === 0) {
            return null;
        }
        return GrazziotinApiIncludeService::uniqueResource($includes);
    }

    public static function include($resource, $include)
    {
        if (!isset($resource->resource)) {
            return [];
        }
        $collection = collect();
        if ($resource->resource instanceof LengthAwarePaginator) {
            $collection = collect($resource->resource->items());
        } else {
            $collection->push($resource);
        }
        $included = [];
        $collection->each(function ($item) use (&$included, &$send, $include) {
            $includes = GrazziotinApiIncludeService::includes($include, $item);
            if ($includes) {
                $included = GrazziotinApiIncludeService::uniqueResource(
                    collect($included)->concat($includes)
                );
            }
        });
        return $included;
    }

}
