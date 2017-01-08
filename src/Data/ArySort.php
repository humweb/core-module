<?php namespace Humweb\Core\Data;

/**
 * ArrSort
 *
 * @package Humweb\Core\Data
 */
class ArySort
{
    /**
     * Ascending
     *
     * Variable to utilize ascending sort
     *
     * @var integer
     */
    const ASC = 'ASC';

    /**
     * Descending
     *
     * Variable to utilize descending sort
     *
     * @var integer
     */
    const DESC = 'DESC';

    protected $items = [];


    /**
     * ArySort constructor.
     *
     * @param array $items
     */
    public function __construct(array $items)
    {
        $this->items = (array)$items;
    }


    /**
     * Create sortable object
     *
     * @param array $items
     *
     * @return static
     */
    public static function create($items = [])
    {
        return new static($items);
    }


    /**
     * Sort by field
     *
     * @param string $field
     * @param array  $ary
     * @param string $direction
     *
     * @return bool
     */
    public function by($field = '', $direction = 'ASC')
    {

        uasort($this->items, function ($a, $b) use ($field, $direction) {

            $a = $a->$field;
            $b = $b->$field;

            if ($a == $b) {
                return 0;
            } elseif ($direction == ArySort::DESC) {
                return $a > $b ? -1 : 1;
            } else {
                return $a < $b ? -1 : 1;
            }
        });

        return $this->items;
    }
}