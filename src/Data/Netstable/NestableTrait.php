<?php

namespace Humweb\Core\Data\Nestable;

trait NestableTrait
{
    /**
     * Return a custom nested collection.
     *
     * @param array $models
     *
     * @return NestableCollection
     */
    public function newCollection(array $models = [])
    {
        return new NestableCollection($models);
    }
}
