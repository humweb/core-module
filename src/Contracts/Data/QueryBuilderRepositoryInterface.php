<?php

namespace Humweb\Core\Contracts\Data;

/**
 * Interface QueryBuilderRepositoryInterface.
 */
interface QueryBuilderRepositoryInterface
{
    /**
     * Create a new instance of the Query Builder.
     *
     * @return mixed
     */
    public function createQuery();

    /**
     * Returns the model.
     *
     * @return string
     */
    public function getRepositoryTable();

    /**
     * Runtime override of the model.
     *
     * @param string $table
     *
     * @return $this
     */
    public function setRepositoryTable($table);

    /**
     * Set primary key.
     *
     * @param string $key
     *
     * @return $this
     */
    public function setRepositoryPrimaryKey($key);
}
