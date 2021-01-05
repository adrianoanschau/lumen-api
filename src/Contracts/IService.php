<?php

namespace Grazziotin\GrazziotinApi\Contracts;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

interface IService
{
    /**
     * @param Collection $attributes
     * @return Model
     */
    public function create(Collection $attributes);

    /**
     * @param string $id
     * @param Collection $attributes
     */
    public function update(string $id, Collection $attributes): void;

    /**
     * @param string $id
     */
    public function delete(string $id): void;
}
