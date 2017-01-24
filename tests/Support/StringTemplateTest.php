<?php

namespace Humweb\Tests\Core\Support;

use Humweb\Core\Support\StringTemplate;
use Humweb\Tests\Core\TestCase;

class StringTemplateTest extends TestCase
{

    /**
     * @test
     */
    public function it_compiles_single_line_template_string()
    {
        $string   = 'Hello My name is {{$name}} and I like: {{ $like }}';
        $template = new StringTemplate($string, ['name' => 'joe', 'like' => 'apples']);

        $this->assertEquals('Hello My name is joe and I like: apples', $template->compile());
    }


    /**
     * @test
     */
    public function it_compiles_multi_line_template_string()
    {
        $string   = 'Hello My name is {{$name}}'.PHP_EOL.' and I like: {{ $like }}';
        $template = new StringTemplate($string, ['name' => 'joe', 'like' => 'apples']);

        $this->assertEquals('Hello My name is joe'.PHP_EOL.' and I like: apples', $template->compile());
    }
}
