<?php

namespace Grazziotin\GrazziotinApi\Contracts;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

interface IRepository extends IQueryBuilder
{
    /**
     * @param int $id
     * @param array|string[] $columns
     * @param array $relations
     * @return Model
     */
    public function findById(int $id, array $columns = ['*'], array $relations = []): Model;

    /**
     * @param array $criteria
     * @param array|string[] $columns
     * @param array $relations
     * @return Model
     */
    public function findByCriteria(array $criteria, array $columns = ['*'], array $relations = []): Model;

    /**
     * @param array $criteria
     * @param array|string[] $columns
     * @param array $relations
     * @return Collection
     */
    public function getByCriteria(array $criteria, array $columns = ['*'], array $relations = []): Collection;
}
