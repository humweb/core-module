<?php namespace Humweb\Core\Data\Traits;


trait DynamicCaller {

    /**
     * The Class to be called
     *
     * @var mixed
     */
    protected $_callerClass;

    /**
     * Checks if method exists
     *
     * @param  string  $name
     * @return bool
     */
    public function hasCallableMethod($name)
    {
        return method_exists($this->_callerClass, $name);
    }


    /**
     * Dynamically handle calls to the class.
     *
     * @param  string  $method
     * @param  array   $parameters
     * @return mixed
     *
     * @throws \BadMethodCallException
     */
    public function __call($method, $parameters)
    {
        if ($this->hasCallableMethod($method))
        {
            return call_user_func_array([$this->getCallableClass(), $method], $parameters);
        }

        throw new \BadMethodCallException("Method {$method} does not exist.");
    }

    /**
     * @return mixed
     */
    public function getCallableClass()
    {
        return $this->_callerClass;
    }

    /**
     * @param mixed $callerClass
     */
    public function setCallableClass($callerClass)
    {
        $this->_callerClass = $callerClass;
    }

}
