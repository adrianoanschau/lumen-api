<?php

namespace Grazziotin\GrazziotinApi\Services;

use Grazziotin\GrazziotinApi\Contracts\IRepository;
use Grazziotin\GrazziotinApi\Contracts\IService;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class GrazziotinApiService implements IService
{
    protected $connectionsToTransact = ['oracle', 'default', 'nl'];

    /**
     * @var mixed
     */
    private $database;

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

    protected function beginTransaction()
    {
        $this->database = app()->make('db');

        foreach (($this->connectionsToTransact ?? [null]) as $name) {
            $this->database->connection($name)->beginTransaction();
        }
    }

    protected function commit()
    {
        $database = $this->database;

        foreach (($this->connectionsToTransact ?? [null]) as $name) {
            $connection = $database->connection($name);
            $connection->commit();
        }
    }

    protected function rollback()
    {
        $database = $this->database;

        foreach (($this->connectionsToTransact ?? [null]) as $name) {
            $connection = $database->connection($name);
            $connection->rollBack();
            $connection->disconnect();
        }
    }

    /**
     * @param Collection $attributes
     * @return Model
     * @throws Exception
     */
    public function create(Collection $attributes): Model
    {
        try {
            $this->beginTransaction();
            $data = $this->repository->newQuery()->create($attributes->toArray());
            $this->commit();
            return $data;
        } catch (Exception $e) {
            $this->rollback();
            throw $e;
        }
    }

    /**
     * @param string $id
     * @param Collection $attributes
     * @throws Exception
     */
    public function update(string $id, Collection $attributes): void
    {
        try {
            $this->beginTransaction();
            $model = $this->repository->findById($id);
            $model->update($attributes->toArray());
            $this->commit();
        } catch (Exception $e) {
            $this->rollback();
            throw $e;
        }
    }

    /**
     * @param string $id
     * @throws Exception
     */
    public function delete(string $id): void
    {
        try {
            $this->beginTransaction();
            $model = $this->repository->findById($id);
            $model->delete();
            $this->commit();
        } catch (Exception $e) {
            $this->rollback();
            throw $e;
        }
    }
}
