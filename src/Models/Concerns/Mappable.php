<?php

namespace Grazziotin\GrazziotinApi\Models\Concerns;

trait Mappable
{
    public function getAttributeName($key)
    {
        if (isset($this->map) && isset($this->map[$key])) {
            $key = $this->map[$key];
        }
        return $key;
    }

    public function __get($key)
    {
        return parent::__get($this->getAttributeName($key));
    }

}
