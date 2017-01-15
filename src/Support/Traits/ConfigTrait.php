<?php

namespace Humweb\Core\Support\Traits;

/**
 * SettingsTrait.php.
 *
 * Date: 3/12/15
 * Time: 9:52 PM
 */
trait ConfigTrait
{
    protected $config = [];


    public function fillConfig($config = [])
    {
        $this->config = $config;
    }


    public function getConfig($key = null)
    {
        return $key !== null ? $this->config[$key] : $this->config;
    }


    public function setConfig($key, $val = null)
    {
        $this->config[$key] = $val;
    }


    public function configKeyExists($key)
    {
        return isset($this->config[$key]);
    }
}
