<?php

namespace Grazziotin\GrazziotinApi\Repositories\Concerns;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

trait Filterable
{
    protected $filters = [];

    public function filterable($model)
    {
        $filters = request()->get('filter') ?? [];
        $filters = collect(Arr::only($filters, array_keys($this->filters)));
        $filters->each(function ($value, $filter) use (&$model) {
            $type = $this->filters[$filter];
            if (get_class($model) === Builder::class) {
                $attr = $model->getModel()->getAttributeName($filter);
            } else {
                $attr = $model->getAttributeName($filter);
            }
            if ($type === 'custom') {
                $method = "get" . Str::studly($filter) . "Filter";
                if (method_exists($this, $method)) {
                    $model = $this->{$method}($model, $attr, $value);
                }
                return;
            }
            $model = $this->where($type, $model, $attr, $value);
        });

        return $model;
    }

    private function where($type, $model, $attr, $value)
    {
        switch($type) {
            case 'string':
                return $model->whereRaw("LOWER($attr) LIKE (?)", ["'%$value%'"]);
            default:
                return $model->where($attr, $value);
        }
    }

}
