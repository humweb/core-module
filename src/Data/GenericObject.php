<?php

namespace App\Data;

class GenericObject
{
    /**
     * All of the attributes.
     *
     * @var array
     */
    protected $attributes;

    protected $required = [];


    /**
     * Create a new generic object.
     *
     * @param array $attributes
     *
     * @return \App\Data\GenericObject
     */
    public function __construct(array $attributes)
    {
        $this->attributes = $attributes;
        $this->enforceRequiredProperties();
    }


    public function enforceRequiredProperties()
    {
        if (count($this->required) > 0) {

            $missing = [];

            foreach ($this->required as $prop) {

                if ( ! isset($this->attributes[$prop])) {

                    $missing[] = $prop;
                }
            }

            if (count($missing) > 0) {
                throw new \Exception('Missing required properties: '.implode(', ', $missing));
            }
        }
    }


    /**
     * Dynamically access the attributes.
     *
     * @param string $key
     *
     * @return mixed
     */
    public function __get($key)
    {
        return $this->attributes[$key];
    }


    /**
     * Dynamically set an attribute.
     *
     * @param string $key
     * @param mixed  $value
     */
    public function __set($key, $value)
    {
        $this->attributes[$key] = $value;
    }


    /**
     * Dynamically check if a value is set.
     *
     * @param string $key
     *
     * @return bool
     */
    public function __isset($key)
    {
        return isset($this->attributes[$key]);
    }


    /**
     * Dynamically unset a value.
     *
     * @param string $key
     *
     * @return bool
     */
    public function __unset($key)
    {
        unset($this->attributes[$key]);
    }
}
