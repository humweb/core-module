<?php

namespace Humweb\Core\Data\Repositories;

use Humweb\Core\Contracts\Data\CrudRepositoryInterface;
use Humweb\Core\Contracts\Data\ModelRepositoryInterface;
use Humweb\Core\Data\Traits\EloquentRepository as EloquentRepositoryTrait;

/**
 * EloquentRepository.php.
 *
 * Date: 11/15/14
 * Time: 12:56 AM
 */
class EloquentRepository implements ModelRepositoryInterface, CrudRepositoryInterface
{

    protected $model;

    public function all()
    {
        return $this->createModel()
            ->orderBy('created_at', 'desc')
            ->all();
    }

    public function allPaged($pages = 15)
    {
        return $this->createModel()
            ->orderBy('created_at', 'desc')
            ->paginate($pages);
    }

    public function find($id)
    {
        return $this->createModel()->find($id);
    }

    public function create(array $data)
    {
        return $this->createModel()->create($data);
    }

    public function update($id, array $data)
    {
        list($model, $pk) = $this->createModelAndGetKey();

        return $model->where($pk, $id)->update($data);
    }

    public function delete($id)
    {
        list($model, $pk) = $this->createModelAndGetKey();

        return $this->createModel()->destroy($id);
    }

    public function deleteMany(array $ids)
    {
        list($model, $pk) = $this->createModelAndGetKey();

        return $model->whereIn($pk, $ids)->delete();
    }

    /**
     * Create a new instance of the model.
     *
     * @param array $data
     *
     * @return mixed
     */
    public function createModel(array $data = [])
    {
        $class = '\\'.ltrim($this->model, '\\');

        return new $class($data);
    }

    /**
     * Returns the model.
     *
     * @return string
     */
    public function getModel()
    {
        return $this->model;
    }

    /**
     * Runtime override of the model.
     *
     * @param string $model
     *
     * @return $this
     */
    public function setModel($model)
    {
        $this->model = $model;

        return $this;
    }

    /**
     * @return mixed
     */
    public function createModelAndGetKey()
    {
        $model = $this->createModel();
        $pk = $model->getKeyName();

        return array($model, $pk);
    }
}
