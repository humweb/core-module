<?php

namespace Humweb\Core\Data;

use Illuminate\Support\Collection;

/**
 * JsonFile
 *
 * @property string json
 * @package Humweb\Core\Data
 */
class JsonFile
{

    /**
     * @var \Illuminate\Support\Collection
     */
    protected $json;

    /**
     * @var string
     */
    protected $file;


    /**
     * JsonFile constructor.
     *
     * @param string $file
     */
    public function __construct($file)
    {
        $this->file = $file;
        $this->json = file_exists($file) ? collect($this->read($file)) : collect();
    }


    /**
     * @param string $file
     *
     * @return static
     */
    static function open($file)
    {
        return new static($file);
    }


    protected function read($file)
    {
        return json_decode(file_get_contents($file));
    }


    public function write()
    {
        file_put_contents($this->file, json_encode($this->json->all()));
    }


    /**
     * @param $method
     * @param $args
     *
     * @return Collection
     */
    public function __call($method, $args)
    {
        if (method_exists($this->json, $method)) {
            return call_user_func_array([$this->json => $method], $args);
        }
        throw new \BadMethodCallException('Method not found: '.$method);
    }


    /**
     * @param \Illuminate\Support\Collection $json
     *
     * @return $this
     */
    public function setCollection($json)
    {
        $this->json = $json;

        return $this;
    }
}