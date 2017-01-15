<?php

namespace Humweb\Core\Contracts\Data;

/**
 * Interface ModelRepositoryInterface.
 */
interface ModelRepositoryInterface
{
    /**
     * Create a new instance of the model.
     *
     * @param array $data
     *
     * @return mixed
     */
    public function createModel(array $data = []);


    /**
     * Returns the model.
     *
     * @return string
     */
    public function getModel();


    /**
     * Runtime override of the model.
     *
     * @param string $model
     *
     * @return $this
     */
    public function setModel($model);
}
