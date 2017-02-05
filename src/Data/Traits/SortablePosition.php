<?php

namespace Humweb\Core\Data\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Query\Builder;

/**
 * SortablePosition
 *
 * @package App\Core\Data\Traits
 */
trait SortablePosition
{
    /**
     * Array of current config values
     *
     * @var array
     */
    private $sortableConfig = [
        'column'         => 'position',
        'scope'          => null,
        'insertPosition' => 'bottom'
    ];


    /**
     * Boot trait
     */
    public static function bootSortablePosition()
    {
        //Bind to model events
        static::deleting(function ($model) {
            $field = $model->getScopeField();
            $model->scopeCondition()->where($model->positionColumn(), '>', $model->getSortablePosition())->decrement($model->positionColumn());
        });

        static::updating(function (Model $model) {

            $oldPos = $model->getSortablePosition(true);
            $newPos = $model->getSortablePosition();

            if ($model->hasScopeChanged()) {
                $model->handleSortableScopeChanged($oldPos, $newPos);
            } elseif ($oldPos != $newPos) {

                $q          = $model->newQuery();
                $field      = $model->getScopeField();
                $primaryKey = $model->getQualifiedKeyName();

                if ( ! empty($field)) {
                    $q->where($field, $model->getAttribute($field));
                }

                if ($oldPos < $newPos) {
                    $q->where($model->positionColumn(), '>', $oldPos)->where($model->positionColumn(), '<=', $newPos)->where($primaryKey, '!=', $model->id)->decrement($model->positionColumn());
                } else {
                    $q->where($model->positionColumn(), '>=', $newPos)->where($model->positionColumn(), '<', $oldPos)->where($primaryKey, '!=', $model->id)->increment($model->positionColumn());
                }
            }
        });

        static::creating(function ($model) {
            $model->setSortablePosition($model->getNextPosition());
        });
    }


    /**
     * Required to override options and kick off the Sortable's automatic list management.
     *
     * @param  array $options [column=>string, scope=>string|BelongsTo|Builder, lowestPosition=>int, insertPosition=>string]
     *
     * @return void
     */
    public function initSortable($options = [])
    {
        //Update config with options
        $this->sortableConfig = array_replace($this->sortableConfig, $options);
    }


    public function handleSortableScopeChanged($oldPos, $newPos)
    {
        $oldPos = $this->getSortablePosition(true);
        $newPos = $this->getSortablePosition();

        // Move up items in old group
        $this->scopeCondition(true)->where($this->positionColumn(), '>', $oldPos)->decrement($this->positionColumn());

        if (is_null($newPos)) {
            $this->setSortablePosition($this->getNextPosition());
        } else {
            // Move down items below in new group
            $this->scopeCondition()->where($this->positionColumn(), '>=', $newPos)->increment($this->positionColumn());
        }
    }


    public function getScopeValue($old)
    {
        $scope = $this->getSortableScope();

        return $old ? $this->getOriginal($scope) : $this->getAttribute($scope);
    }


    /**
     * Returns the raw WHERE clause to be used as the Sortable scope
     *
     * @param bool $old
     *
     * @return null|string
     * @throws \Exception
     */
    private function scopeCondition($old = false)
    {
        $scope = $this->getSortableScope();

        if (is_null($scope)) {
            return $this;
        }

        if (is_string($scope)) {
            $id = $old ? $this->getOriginal($scope) : $this->getAttribute($scope);

            return $this->newQuery()->where($scope, $id);
        } elseif ($scope instanceof BelongsTo) {
            return $this->sortableBelongsToScope($scope, $old);
        } else {
            throw new \Exception('Sortable scope parameter must be a String, BelongsTo relationship, or Builder instance.');
        }
    }


    /**
     * Get the value of the 'scope' option
     *
     * @return mixed Can be a string, an Eloquent BelongsTo, or an Eloquent Builder
     */
    public function getSortableScope()
    {
        return $this->sortableConfig['scope'];
    }


    /**
     * @param $scope
     *
     * @throws \Exception
     */
    protected function sortableBelongsToScope($scope, $old = false)
    {
        $relationshipId = $old ? $this->getOriginal($scope->getForeignKey()) : $this->getAttribute($scope->getForeignKey());

        if ( ! is_null($relationshipId)) {
            return $this->newQuery()->where($scope->getForeignKey(), $relationshipId);
        }

        throw new \Exception('The Sortable scope is a "belongsTo" relationship, but the foreign key is null.');
    }


    public function getNextPosition()
    {
        return $this->scopeCondition()->max($this->positionColumn()) + 1;
    }


    /**
     * Eloquent scope based on the processed scope option
     *
     * @param  $query Builder instance
     *
     * @return Builder instance
     */
    public function scopeSortableScope($query, $field, $val, $op = '=')
    {
        return $query->where($field, $op, $val);
    }


    /**
     * Returns the value of the 'insertPosition' option
     *
     * @return string
     */
    public function getInsertPosition()
    {
        return $this->sortableConfig['insertPosition'];
    }


    /**
     * Returns the value of the model's current position
     *
     * @param bool $old
     *
     * @return int
     */
    public function getSortablePosition($old = false)
    {
        return $old ? $this->getOriginal($this->positionColumn()) : $this->getAttribute($this->positionColumn());
    }


    /**
     * Get the name of the position 'column' option
     *
     * @return string
     */
    public function positionColumn()
    {
        return $this->sortableConfig['column'];
    }


    /**
     * Sets the value of the model's position
     *
     * @param int $position
     *
     * @return void
     */
    public function setSortablePosition($position)
    {
        $this->setAttribute($this->positionColumn(), $position);
    }


    /**
     * Returns whether the scope has changed during the course of interaction with the model
     *
     * @return boolean
     */
    private function hasScopeChanged()
    {
        $scope = $this->getSortableScope();

        if (is_string($scope)) {
            return $this->getOriginal($scope) != $this->getAttribute($scope);
        }

        if ($scope instanceof BelongsTo) {
            return $this->getOriginal($scope->getForeignKey()) != $this->getAttribute($scope->getForeignKey());
        }

        return false;
    }


    private function getScopeField()
    {
        $scope = $this->getSortableScope();

        if (is_string($scope)) {
            return $scope;
        } elseif ($scope instanceof BelongsTo) {
            return $scope->getForeignKey();
        }

        return false;
    }


    /**
     * Creates an instance of the current class scope as a list
     *
     * @return mixed
     */
    private function sortableList()
    {
        $model = new self();
        $model->setSortableConfig('scope', $this->scopeCondition());

        return $model->sortableScope();
    }

    /* Private Methods */

    /**
     * Updates a sortable config value
     *
     * @param string
     * @param mixed
     *
     * @return void
     */
    public function setSortableConfig($key, $value)
    {
        $this->sortableConfig[$key] = $value;
    }

}