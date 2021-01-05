<?php

namespace Grazziotin\GrazziotinApi\Http\Resources;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Str;

class GrazziotinApiResource extends JsonResource
{
    public $type;
    public $attributes;
    public $relations;

    public function __get($key)
    {
        $magic_method = "get" . Str::studly($key). "Attribute";
        $value = null;
        if (get_class($this->resource) !== Collection::class || !$this->resource->isEmpty()) {
            $value = $this->resource->{$key};
        }
        if (method_exists($this, $magic_method)) {
            return $this->{$magic_method}($value);
        }
        return $value;
    }

    public function toArray($request)
    {
        if ($this->type && $this->attributes) {
            return $this->getData(isset($this->relations));
        }

        return parent::toArray($request);
    }

    public function getBasicData()
    {
        if (is_null($this->resource)) {
            return null;
        }
        if(get_class($this->resource) === Collection::class) {
            return get_class($this)::collection($this->resource)
                ->map(function ($resource) {
                    return $resource->getBasicData();
                });
        }
        return [
            'id' => $this->id,
            'type' => $this->type,
        ];
    }

    public function getAttributes()
    {
        if (is_null($this->resource)) {
            return null;
        }
        return collect(array_flip($this->attributes))
            ->map(function ($_, $attr) {
                return $this->{$attr};
            });
    }

    public function getData($withRelations = false)
    {
        $data = $this->getBasicData();
        $data['attributes'] = $this->getAttributes();
        if ($withRelations) {
            $data['relationships'] = $this->getRelationships();
        }
        return $data;
    }

    public function getRelationships()
    {
        if (is_null($this->resource)) {
            return null;
        }
        $collection = [];
        collect($this->relations)
            ->each(function ($class, $relation) use (&$collection) {
                $collection[$relation] = [
                    'data' => (new $class($this->{$relation}))->getBasicData()
                ];
            });
        return $collection;
    }

    public function include($name)
    {
        $name = collect(Str::of($name)->split('/[.]/'))->map(function ($piece) {
            if (Str::plural($piece) === $piece) {
                return "$piece.*";
            }
            return $piece;
        })->join('.');

        $model = data_get($this->resource, $name);
        $resource = $this;
        collect(Str::of($name)->split('/[.]/'))
            ->each(function ($piece) use (&$resource) {
                if ($resource->relations && isset($resource->relations[$piece])) {
                    $model = data_get($resource->resource, $piece);
                    $resource = new $resource->relations[$piece]($model);
                }
            });

        if (!is_array($model)) {
            $model = [$model];
        }

        return collect($model)->map(function ($inc) use($resource) {
            $res = new $resource($inc);
            $data = $res->getBasicData();
            $data['attributes'] = $res->getAttributes();
            $data['relationships'] = $res->getRelationships();
            return $data;
        });
    }

}
