<?php

declare(strict_types=1);

namespace App\Repositories;

use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

abstract class CustomRepository implements RepositoryInterface
{
    /**
     * @var Model
     */
    private Model $model;

    /**
     * @var string
     */
    protected string $objectName = 'object';

    /**
     * @return Collection|static[]
     */
    public function all()
    {
        return $this->model::all();
    }

    /**
     * @param array $data
     * @param array $files
     *
     * @return mixed
     */
    public function create(array $data, $files = [])
    {
        return $this->model::create($data);
    }

    /**
     * @param array $data
     * @param Model $record
     * @param array $files
     *
     * @return bool
     */
    public function update(array $data, Model $record, $files = []): bool
    {
        return $record->update($data);
    }

    /**
     * @param Model $record
     *
     * @return bool|null
     *
     * @throws Exception
     */
    public function delete(Model $record): ?bool
    {
        return $record->delete();
    }

    /**
     * @param Collection $entities
     *
     * @return int
     */
    public function batchDelete(Collection $entities): int
    {
        return $this->model::destroy($entities->pluck('id')->toArray());
    }

    /**
     * @param $id
     *
     * @return mixed
     */
    public function get($id)
    {
        return $this->model::findOrFail($id);
    }

    /**
     * @return Model
     */
    public function getModel(): Model
    {
        return $this->model;
    }

    /**
     * @param Model $model
     *
     * @return $this
     */
    public function setModel(Model $model): self
    {
        $this->model = $model;

        return $this;
    }

    /**
     * @return string
     */
    public function getObjectName(): string
    {
        return $this->objectName;
    }

    /**
     * @param array|string $relations
     *
     * @return Builder|static
     */
    public function with($relations)
    {
        return $this->model::with($relations);
    }

    /**
     * @return Builder
     */
    public function createQuery(): Builder
    {
        return $this->model->newQuery()->select(
            $this->model->getTable() . '.*'
        );
    }
}
