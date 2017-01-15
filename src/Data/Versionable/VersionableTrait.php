<?php namespace Humweb\Core\Data\Versionable;

trait VersionableTrait
{

    /**
     * @var bool
     */
    private $updating;

    /**
     * @var array
     */
    private $versionableDirtyData;

    /**
     * @var string
     */
    private $reason;


    /**
     * Initialize model events
     */
    public static function boot()
    {
        parent::boot();

        static::saving(function ($model) {
            $model->versionablePreSave();
        });

        static::saved(function ($model) {
            $model->versionablePostSave();
        });
    }


    /**
     * Attribute mutator for "reason"
     * Prevent "reason" to become a database attribute of model
     *
     * @param string $value
     */
    public function setReasonAttribute($value)
    {
        $this->reason = $value;
    }


    /**
     * @return mixed
     */
    public function getCurrentVersion()
    {
        return $this->versions()->orderBy(Version::CREATED_AT, 'DESC')->first();
    }


    /**
     * @return mixed
     */
    public function versions()
    {
        return $this->morphMany(Version::class, 'versionable');
    }


    /**
     * @param $version_id
     *
     * @return null
     */
    public function getVersionModel($version_id)
    {
        $version = $this->versions()->where("version_id", "=", $version_id)->first();
        if ( ! is_null($version)) {
            return $version->getModel();
        } else {
            return null;
        }
    }


    /**
     * Restore the model and make it the current version
     *
     * @return bool
     */
    public function restoreVersion()
    {
        unset($this->{$this->getCreatedAtColumn()});
        unset($this->{$this->getUpdatedAtColumn()});
        if (function_exists('getDeletedAtColumn')) {
            unset($this->{$this->getDeletedAtColumn()});
        }

        return $this->save();
    }


    /**
     * Pre save hook to determine if versioning is enabled and if we're updating
     * the model
     */
    public function versionablePreSave()
    {
        if ($this->versionsEnabled()) {
            $this->versionableDirtyData = $this->getDirty();
            $this->updating             = $this->exists;
        }
    }


    public function versionsEnabled()
    {
        return ! isset($this->versionsEnabled) || $this->versionsEnabled === true;
    }


    /**
     * Save a new version
     */
    public function versionablePostSave()
    {

        if ($this->validateVersionable()) {

            // Save a new version
            $version = Version::create([
                'versionable_id'   => $this->getKey(),
                'versionable_type' => get_class($this),
                'user_id'          => $this->author_id,
                'model_data'       => serialize($this->getAttributes()),
                'reason'           => ( ! empty($this->reason)) ? $this->reason : ''
            ]);

            $this->cleanupVersions();
        }
    }


    /**
     * @return bool
     */
    private function validateVersionable()
    {

        if ($this->versionsEnabled() && ! $this->updating) {
            return true;
        } elseif ($this->versionsEnabled() && $this->updating) {
            $versionableData = $this->versionableDirtyData;
            unset($versionableData[$this->getUpdatedAtColumn()]);

            if (function_exists('getDeletedAtColumn')) {
                unset($versionableData[$this->getDeletedAtColumn()]);
            }

            if (isset($this->dontVersionFields)) {
                foreach ($this->dontVersionFields as $fieldName) {
                    unset($versionableData[$fieldName]);
                }
            }

            return (count($versionableData) > 0);
        }

        return false;
    }


    public function cleanupVersions($limit = 6)
    {
        $versions = $this->versions()->select('id')->orderBy('id', 'desc')->skip($limit)->take($limit)->get();
        foreach ($versions as $version) {
            $version->delete();
        }
    }


    /**
     * @return int|null
     */
    private function getAuthUserId()
    {
        return $this->author_id;
    }

}