<?php

namespace Humweb\Core\Data;

use Illuminate\Database\Eloquent\Model;

/**
 * LGL\Core\Content\Models\Relatable
 *
 * @property int                                                $id
 * @property int                                                $source_id
 * @property string                                             $source_type
 * @property int                                                $related_id
 * @property string                                             $related_type
 * @property-read \Illuminate\Database\Eloquent\Model|\Eloquent $related
 * @property-read \Illuminate\Database\Eloquent\Model|\Eloquent $source
 * @method static \Illuminate\Database\Query\Builder|\LGL\Core\Content\Models\Relatable whereSourceType($value)
 * @method static \Illuminate\Database\Query\Builder|\LGL\Core\Content\Models\Relatable whereSourceId($value)
 * @method static \Illuminate\Database\Query\Builder|\LGL\Core\Content\Models\Relatable whereRelatedType($value)
 * @method static \Illuminate\Database\Query\Builder|\LGL\Core\Content\Models\Relatable whereRelatedId($value)
 * @mixin \Eloquent
 */
class Relatable extends Model
{
    /** @var bool */
    public $incrementing = false;
    /** @var bool */
    public $timestamps = false;
    protected $table = 'content_related';
    /** @var array */
    protected $guarded = [];
    /** @var string|null */
    protected $primaryKey = null;


    public function related()
    {
        return $this->morphTo('related');
    }


    public function source()
    {
        return $this->morphTo('source');
    }


    public function getTable()
    {
        return 'content_related';
    }


    public function getRelatedValues()
    {
        return [
            'type' => $this->related_type,
            'id'   => $this->related_id,
        ];
    }
}