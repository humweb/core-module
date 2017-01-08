<?php

namespace Humweb\Core\Data\Traits;

use Illuminate\Support\Facades\DB;

/**
 * QueryBuilderRepository.
 */
trait QueryBuilderRepository
{
    /**
     * Create a new instance of the model.
     *
     * @return Illuminate\Database\Query\Builder
     */
    public function createQuery()
    {
        return DB::table($this->getRepositoryTable());
    }

    /**
     * Returns the model.
     *
     * @return string
     */
    public function getRepositoryTable()
    {
        return $this->table;
    }

    /**
     * Runtime override of the model.
     *
     * @param string $table
     *
     * @return $this
     */
    public function setRepositoryTable($table)
    {
        $this->table = $table;
    }

    /**
     * Set primary key.
     *
     * @param string $key
     *
     * @return $this
     */
    public function setPrimaryKey($key = null)
    {
        $this->primaryKey = $key ?: 'id';
    }

    public function getPrimaryKey()
    {
        $this->primaryKey ?: 'id';
    }
}
