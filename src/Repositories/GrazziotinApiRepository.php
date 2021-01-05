<?php

namespace Grazziotin\GrazziotinApi\Repositories;

use Grazziotin\GrazziotinApi\Contracts\IRepository;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class GrazziotinApiRepository implements IRepository
{
    /**
     * @var Model
     */
    private $model;

    /**
     * BaseRepository constructor.
     * @param Model $model
     */
    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    /**
     * @param int $id
     * @param array|string[] $columns
     * @param array $relations
     * @return Model
     */
    public function findById(int $id, array $columns = ['*'], array $relations = []): Model
    {
        return $this->findByCriteria(['id' => $id], $columns, $relations);
    }

    /**
     * @param array $criteria
     * @param array|string[] $columns
     * @param array $relations
     * @return Model
     */
    public function findByCriteria(array $criteria, array $columns = ['*'], array $relations = []): Model
    {
        return $this->newQuery()->select($columns)->with($relations)->where($criteria)->firstOrFail();
    }

    /**
     * @param array $criteria
     * @param array|string[] $columns
     * @param array $relations
     * @return Collection
     */
    public function getByCriteria(array $criteria, array $columns = ['*'], array $relations = []): Collection
    {
        return $this->newQuery()->select($columns)->with($relations)->where($criteria)->get();
    }

    /**
     * @return Builder
     */
    public function newQuery(): Builder
    {
        return $this->model->newQuery();
    }
}
