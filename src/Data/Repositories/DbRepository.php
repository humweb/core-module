<?php

namespace Humweb\Core\Data\Repositories;

use Humweb\Core\Contracts\Data\CrudRepositoryInterface;
use Humweb\Core\Contracts\Data\QueryBuilderRepositoryInterface;
use Humweb\Core\Data\Traits\QueryBuilderRepository;

/**
 * QueryBuilderRepository.
 *
 * Date: 11/15/14
 * Time: 12:56 AM
 */
class DbRepository implements QueryBuilderRepositoryInterface, CrudRepositoryInterface
{
    use QueryBuilderRepository;

    /**
     * @var string
     */
    protected $table;

    /**
     * @var string
     */
    protected $primaryKey;


    public function all()
    {
        return $this->createQuery()->get();
    }


    public function find($id)
    {
        return $this->createQuery()->where($this->getPrimaryKey(), $id)->get();
    }


    public function create(array $data)
    {
        return $this->createQuery()->insert($data);
    }


    public function update($id, array $data)
    {
        return $this->createQuery()->where($this->getPrimaryKey(), $id)->update($data);
    }


    public function delete($id)
    {
        return $this->createQuery()->where($this->getPrimaryKey(), $id)->delete();
    }


    public function deleteMany(array $ids)
    {
        return $this->createQuery()->whereIn($this->getPrimaryKey(), $ids)->delete();
    }
}
