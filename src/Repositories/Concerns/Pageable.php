<?php

namespace Grazziotin\GrazziotinApi\Repositories\Concerns;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

trait Pageable
{
    private $pageSizeLimit = 100;
    private $pageSizeParam = 'page.size';
    private $pageNameParam = 'page.number';
    private $pageSize = 10;

    private function getPageSize()
    {
        $pageSize = request($this->pageSizeParam, $this->pageSize);
        return (int) min($pageSize, $this->pageSizeLimit);
    }

    private function replaceUrl($url)
    {
        return preg_replace('/%5D/', ']', preg_replace('/%5B/', '[', $url));
    }

    private function url(LengthAwarePaginator $resource, $page)
    {
        $parameters = collect([
            'page' => [
                'number' => $page,
                'size' => $this->getPageSize(),
            ],
        ])->merge(request()->except('page'))->toArray();

        return $this->replaceUrl($resource->path()
            .(Str::contains($resource->path(), '?') ? '&' : '?')
            .Arr::query($parameters));
    }

    private function prevPageUrl(LengthAwarePaginator $resource)
    {
        if ($resource->currentPage() > 1) {
            return $this->url($resource, $resource->currentPage() - 1);
        }
    }

    private function nextPageUrl(LengthAwarePaginator $resource)
    {
        if ($resource->hasMorePages()) {
            return $this->url($resource, $resource->currentPage() + 1);
        }
    }

    public function meta($data = null)
    {
        $meta = [];
        if (isset($data->resource) &&
            get_class($data->resource) === LengthAwarePaginator::class) {
            $meta['totalPages'] = $data->resource->lastPage();
            $meta['total'] = $data->resource->total();
            $meta['currentPage'] = $data->resource->currentPage();
            $meta['pageSize'] = $this->getPageSize();
        }
        return $meta;
    }

    public function links($data = null)
    {
        $links = [];
        if (isset($data->resource) &&
            get_class($data->resource) === LengthAwarePaginator::class) {
            $links['first'] = $this->url($data->resource, 1);
            $links['last'] = $this->url($data->resource, $data->resource->lastPage());
            $links['prev'] = $this->prevPageUrl($data->resource);
            $links['next'] = $this->nextPageUrl($data->resource);
        }
        return $links;
    }

}
