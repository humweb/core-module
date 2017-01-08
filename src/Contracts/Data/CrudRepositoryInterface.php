<?php

namespace Humweb\Core\Contracts\Data;

/**
 * Interface AccessibleRepository.
 */
interface CrudRepositoryInterface
{
    public function all();
    public function find($id);
    public function create(array $data);

    public function update($id, array $data);

    public function delete($id);

    public function deleteMany(array $ids);
}
