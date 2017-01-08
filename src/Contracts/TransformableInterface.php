<?php

namespace Humweb\Core\Contracts;

interface TransformableInterface
{
    /**
     * Prepare a new or cached transformer instance.
     *
     * @return mixed
     */
    public function transform();
}
