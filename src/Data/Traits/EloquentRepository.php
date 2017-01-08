<?php

namespace Humweb\Core\Data\Traits;

/**
 * Repository.php.
 *
 * Date: 11/15/14
 * Time: 12:49 AM
 */
trait EloquentRepository
{
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
