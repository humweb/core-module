<?php

namespace Humweb\Core\Data\Versionable;

use Humweb\Auth\Users\User;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Version
 *
 * @package LGL\Core\Versionable
 * @property integer                                            $id
 * @property integer                                            $versionable_id
 * @property string                                             $versionable_type
 * @property integer                                            $user_id
 * @property string                                             $model_data
 * @property string                                             $reason
 * @property \Carbon\Carbon                                     $created_at
 * @property \Carbon\Carbon                                     $updated_at
 * @property-read \Illuminate\Database\Eloquent\Model|\Eloquent $versionable
 * @property-read mixed                                         $responsible_user
 * @method static \Illuminate\Database\Query\Builder|\Humweb\Core\Data\Versionable\Version whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\Humweb\Core\Data\Versionable\Version whereVersionableId($value)
 * @method static \Illuminate\Database\Query\Builder|\Humweb\Core\Data\Versionable\Version whereVersionableType($value)
 * @method static \Illuminate\Database\Query\Builder|\Humweb\Core\Data\Versionable\Version whereUserId($value)
 * @method static \Illuminate\Database\Query\Builder|\Humweb\Core\Data\Versionable\Version whereModelData($value)
 * @method static \Illuminate\Database\Query\Builder|\Humweb\Core\Data\Versionable\Version whereReason($value)
 * @method static \Illuminate\Database\Query\Builder|\Humweb\Core\Data\Versionable\Version whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\Humweb\Core\Data\Versionable\Version whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Version extends Model
{

    /**
     * @var string
     */
    public $table = "content_versions";

    protected $guarded = [];


    /**
     * Sets up the relation
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function versionable()
    {
        return $this->morphTo();
    }


    /**
     * Return the user responsible for this version
     *
     * @return mixed
     */
    public function getResponsibleUserAttribute()
    {
        return User::find($this->user_id);
    }


    /**
     * Return the versioned model
     *
     * @return mixed
     */
    public function getModel()
    {
        $model = new $this->versionable_type();
        Model::unguard();
        $model->fill(unserialize($this->model_data));
        $model->exists = true;
        Model::reguard();

        return $model;
    }

}