<?php

namespace Humweb\Tests\Core;

class TestCase extends \Orchestra\Testbench\TestCase
{

    protected $runMigrations = false;


    /**
     * Setup the test environment.
     */
    public function setUp()
    {
        parent::setUp();

        if ($this->runMigrations === true) {
            $this->loadMigrationsFrom([
                '--database' => 'testing',
                '--realpath' => realpath(__DIR__.'/database/migrations'),
            ]);
        }
    }


    /**
     * Define environment setup.
     *
     * @param  \Illuminate\Foundation\Application $app
     *
     * @return void
     */
    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('database.default', 'testing');
    }
}