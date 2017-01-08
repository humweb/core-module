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

    /**
     * Boot trait
     */
    public static function bootSortablePosition()
    {
        //Bind to model events
        static::deleting(function ($model) {
            $pos   = $model->getAttribute('position');
            $field = $model->getScopeField();
            $model->scopeCondition()->where('position', '>', $model->position)->decrement('position');
        });


        static::updating(function (Model $model) {

            if ($model->hasScopeChanged()) {
                $model->handleSortableScopeChanged();
            } else {

                $oldPos = $model->getOriginal('position');
                $newPos = $model->getAttribute('position');

                if ($oldPos == $newPos) {
                    return;
                }

                $q          = $model->newQuery();
                $field      = $model->getScopeField();
                $primaryKey = $model->getQualifiedKeyName();

                if ( ! empty($field)) {
                    $q->where($field, $model->getAttribute($field));
                }

                if ($oldPos < $newPos) {
                    $q->where('position', '>', $oldPos)->where('position', '<=', $newPos)->where($primaryKey, '!=', $model->id)->decrement('position');
                } else {
                    $q->where('position', '>=', $newPos)->where('position', '<', $oldPos)->where($primaryKey, '!=', $model->id)->increment('position');
                }
            }
        });

        static::creating(function ($model) {
            $model->position = $model->getNextPosition();
        });
    }

    public function handleSortableScopeChanged()
    {
        $oldPos = $this->getOriginal('position');
        $newPos = $this->getAttribute('position');

        // Move up items in old group
        $this->scopeCondition(true)->where('position', '>', $oldPos)->decrement('position');

        // Move down items below in new group
        $this->scopeCondition()->where('position', '>=', $newPos)->increment('position');
    }

    public function getNextPosition()
    {
        return $this->scopeCondition()->max('position') + 1;
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
            throw new \Exception('Sortable scope parameter must be a String, an Eloquent BelongsTo object, or a Query Builder object.');
        }
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
        }

        if ($scope instanceof BelongsTo) {
            return $scope->getForeignKey();
        }

        return false;
    }

    /**
     * An Eloquent scope based on the processed scope option
     *
     * @param  $query An Eloquent Query Builder instance
     *
     * @return Builder instance
     */
    public function scopeSortableScope($query, $field, $val, $op = '=')
    {
        return $query->where($field, $op, $val);
    }

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
     * Get the value of the 'scope' option
     *
     * @return mixed Can be a string, an Eloquent BelongsTo, or an Eloquent Builder
     */
    public function getSortableScope()
    {
        return $this->sortableConfig['scope'];
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
     * @return int
     */
    public function getSortablePosition()
    {
        return $this->getAttribute($this->positionColumn());
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

    /* Private Methods */

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

}