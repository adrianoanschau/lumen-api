<?php

namespace Grazziotin\GrazziotinApi\Models\Concerns;

use Awobaz\Compoships\Compoships;
use Exception;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

trait HasCompositePrimaryKey
{
    use Compoships;

    public function getIdAttribute()
    {
        return collect($this->getKeyName())->map(function ($key) {
            $key = $this->{$key};
            if (is_object($key) && get_class($key) === Carbon::class) {
                return $key->timestamp;
            }
            return $key;
        })->join('-');
    }

    public function getIncrementing()
    {
        return false;
    }

    protected function setKeysForSaveQuery($query)
    {
        foreach ($this->getKeyName() as $key) {
            if ( ! isset($this->$key)) {
                throw new Exception(__METHOD__ . 'Missing part of the primary key: ' . $key);
            }

            $query->where($key, '=', $this->$key);
        }
        return $query;
    }

    public static function find($ids, $columns = ['*'])
    {
        $me    = new self;
        $ids = collect(Str::of($ids)->split('/[-]/'))
            ->keyBy(function ($value, &$key) use ($me) {
                return $me->getKeyName()[$key];
            })->toArray();
        $query = $me->newQuery();
        foreach ($me->getKeyName() as $key) {
            $value = $ids[$key];
            if (isset($me->casts[$key]) && Str::contains($me->casts[$key], ['date', 'datetime'])) {
                $value = Carbon::createFromTimestamp($value);
            }
            $query->where($key, '=', $value);
        }

        return $query->first($columns);
    }
}
