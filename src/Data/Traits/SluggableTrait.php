<?php

namespace Humweb\Core\Data\Traits;

/**
 * SluggableTrait
 *
 * @package LGL\Core\Support\Traits
 */

use Illuminate\Database\Eloquent\Model;

trait SluggableTrait
{
    protected $slugOptions = [
        'maxlen'     => 200,
        'unique'     => true,
        'slug_field' => '',
        'from_field' => '',
    ];

    protected $runtimeSlugOptions = [];


    /**
     * Boot the trait.
     */
    protected static function bootSluggableTrait()
    {
        static::creating(function (Model $model) {
            $model->addSlug();
        });

        static::updating(function (Model $model) {
            $model->addSlug();
        });
    }


    public function getSlugOptions()
    {
        $options = $this->slugOptions;

        if (count($this->runtimeSlugOptions)) {
            $options = array_merge($options, $this->runtimeSlugOptions);
        }

        return $options;
    }


    /**
     * @param  string|array $key
     * @param null          $value
     *
     * @return $this
     */
    public function setSlugOptions($key, $value = null)
    {
        if (is_array($key)) {
            $this->runtimeSlugOptions = $key;
        } else {
            $this->runtimeSlugOptions[$key] = $value;
        }

        return $this;
    }


    /**
     * Add the slug to the model.
     */
    protected function addSlug()
    {
        $this->slugOptions = $this->getSlugOptions();

        $this->ensureValidSlugOptions();

        $slug = $this->generateNonUniqueSlug();

        if ($this->slugOptions['unique'] == true) {
            $slug = $this->makeSlugUnique($slug);
        }

        $this->setAttribute($this->slugOptions['slug_field'], $slug);
    }


    /**
     * Generate a non unique slug for this record.
     */
    protected function generateNonUniqueSlug()
    {
        if ($this->hasCustomSlugBeenUsed()) {

            return $this->getAttribute($this->slugOptions['slug_field']);
        }

        return str_slug($this->getSlugSourceString());
    }


    /**
     * Determine if a custom slug has been saved.
     */
    protected function hasCustomSlugBeenUsed()
    {
        $slugField = $this->slugOptions['slug_field'];

        return $this->getOriginal($slugField) != $this->getAttribute($slugField);
    }


    /**
     * Get the string that should be used as base for the slug.
     */
    protected function getSlugSourceString()
    {
        if (is_array($this->slugOptions['from_field'])) {
            $self = $this;

            $slugSourceString = collect($this->slugOptions['from_field'])->map(function ($fieldName) use ($self) {
                return $self->{$fieldName} ?: '';
            })->implode('-');
        } else {
            $slugSourceString = $this->getAttribute($this->slugOptions['from_field']);
        }

        return substr($slugSourceString, 0, $this->slugOptions['maxlen']);
    }


    /**
     * Make the given slug unique.
     */
    protected function makeSlugUnique($slug)
    {
        $originalSlug = $slug;
        $i            = 1;

        while ($this->checkSlugUnique($slug) || $slug === '') {
            $slug = $originalSlug.'-'.$i++;
        }

        return $slug;
    }


    /**
     * Determine if a record exists with the given slug.
     */
    protected function checkSlugUnique($slug)
    {
        return static::where($this->slugOptions['slug_field'], $slug)->where($this->getKeyName(), '!=', $this->getKey() ?: '0')->count() > 0;
    }


    /**
     * This function will throw an exception when any of the options is missing or invalid.
     */
    protected function ensureValidSlugOptions()
    {
        if ( ! isset($this->slugOptions['from_field'])) {
            throw \Exception('[Sluggable Options] Missing slug from field');
        }

        if ( ! isset($this->slugOptions['slug_field'])) {
            throw \Exception('[Sluggable Options] Missing slug to field');
        }

        if ($this->slugOptions['maxlen'] <= 0) {
            throw \Exception('[Sluggable Options] Max length for slug is: '.$this->slugOptions['maxlen']);
        }
    }
}