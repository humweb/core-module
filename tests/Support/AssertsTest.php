<?php

namespace Humweb\Tests\Core\Support;

use DateTime;
use Humweb\Core\Support\Asserts;
use Humweb\Tests\Core\TestCase;

class AssertsTest extends TestCase
{

    /**
     * @test
     * @dataProvider getAsserts
     */
    public function testAsserts($method, $args = [], $success = false)
    {
        if ( ! $success) {
            $this->setExpectedException('\Exception');
        }

        call_user_func_array([Asserts::class, $method], $args);
    }


    public function getAsserts()
    {
        return [
            'boolean pass'      => ['boolean', [true], true],
            'boolean fail'      => ['boolean', ['abc'], false],
            'float pass'        => ['float', [1.23], true],
            'float fail'        => ['float', [123], false],
            'string pass'       => ['string', ['123'], true],
            'string fail'       => ['string', [123], false],
            'integer pass'      => ['integer', [123], true],
            'integer fail'      => ['integer', ['123'], false],
            'isEmpty pass'      => ['isEmpty', [''], true],
            'isEmpty fail'      => ['isEmpty', ['123'], false],
            'isEmpty fail'      => ['isEmpty', [['123']], false],
            'isEmpty pass'      => ['isEmpty', [[]], true],
            'notEmpty fail'     => ['notEmpty', [''], false],
            'notEmpty pass'     => ['notEmpty', ['123'], true],
            'notEmpty pass'     => ['notEmpty', [['123']], true],
            'notEmpty fail'     => ['notEmpty', [[]], false],
            'isArray pass'      => ['isArray', [['123']], true],
            'isArray fail'      => ['isArray', ['123'], false],
            'null pass'         => ['null', [null], true],
            'null fail'         => ['null', ['123'], false],
            'notNull fail'      => ['notNull', [null], false],
            'notNull pass'      => ['notNull', ['123'], true],
            'classExists pass'  => ['classExists', ['DateTime'], true],
            'classExists fail'  => ['classExists', ['FooBarNonExistent'], false],
            'isInstanceOf pass' => ['isInstanceOf', [(new DateTime), 'DateTime'], true],
            'isInstanceOf fail' => ['isInstanceOf', [(new DateTime), 'Humweb\Core\Support\Asserts'], false],
        ];
    }

}
