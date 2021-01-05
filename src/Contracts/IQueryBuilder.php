<?php

namespace Grazziotin\GrazziotinApi\Contracts;

use Illuminate\Database\Eloquent\Builder;

interface IQueryBuilder
{
    /**
     * @return Builder
     */
    public function newQuery(): Builder;
}
