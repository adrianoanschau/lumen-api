<?php

namespace Grazziotin\GrazziotinApi\Services;

use Exception;
use Grazziotin\GrazziotinApi\Contracts\IRepository;
use Grazziotin\GrazziotinApi\Contracts\IService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class GrazziotinApiService implements IService
{
    /**
     * @var IRepository
     */
    protected $repository;

    /**
     * BaseService constructor.
     * @param IRepository $repository
     */
    public function __construct(
        IRepository $repository
    )
    {
        $this->repository = $repository;
    }

    /**
     * @param Collection $attributes
     * @return Model
     * @throws Exception
     */
    public function create(Collection $attributes): Model
    {
        return $this->repository->newQuery()->create($attributes->toArray());
    }

    /**
     * @param string $id
     * @param Collection $attributes
     * @throws Exception
     */
    public function update(string $id, Collection $attributes): void
    {
        $model = $this->repository->findById($id);
        $model->update($attributes->toArray());
    }

    /**
     * @param string $id
     * @throws Exception
     */
    public function delete(string $id): void
    {
        $model = $this->repository->findById($id);
        $model->delete();
    }
}
