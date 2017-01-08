<?php namespace Humweb\Core\Data\Repositories;
/**
 * Manager
 * 
 * @package App\Support\Repositories
 */
class Manager {

    protected $app;
    protected $repositories = [];
    /**
     * @var string
     */
    protected $contract;

    /**
     * Manager constructor.
     *
     * @param        $app
     * @param string $contract
     */
    public function __construct($app, $contract = '')
    {
        $this->app = $app;
        $this->contract = $contract;
    }

    public function bindRepository()
    {
        $env = $this->getEnv();
//        dd($env);
        $repositories = $this->getRepositories();

        if (isset($repositories[$env]))
        {
            $this->app->bind($this->getContract(), $repositories[$env]);
        }
        elseif (isset($repositories['*']))
        {
            $this->app->bind($this->getContract(), $repositories['*']);
        }
        else
        {
            throw new \Exception('Repository not found for environment.');
        }
    }

    public function getEnv()
    {
        return getenv('APP_ENV');
    }

    /**
     * @return array
     */
    public function getRepositories()
    {
        return $this->repositories;
    }

    /**
     * @return string
     */
    public function getContract()
    {
        return $this->contract;
    }

}