<?php

namespace Humweb\Core\Support;

/**
 * StringTemplate
 *
 * @package Humweb\Core\Support
 */
class StringTemplate
{
    protected $template;
    protected $data = [];


    /**
     * StringTemplate constructor.
     *
     * @param string $template
     * @param array  $data
     */
    public function __construct($template = '', array $data)
    {
        $this->template = $template;
        $this->data     = $data;
    }


    public function compile()
    {
        $replace = [];
        $values  = [];

        foreach ($this->data as $key => $value) {
            $values[]  = $value;
            $replace[] = '/{{\s*\$('.$key.')\s*}}/mi';
        }

        return preg_replace($replace, $values, $this->template);
    }

}