<?php namespace Humweb\Core\Logs;

/**
 * RedisRepository
 *
 * @property  client
 * @package Humweb\Core\Logs
 */
class ElasticRepository
{
    protected $client;

    protected $keys    = [];
    protected $servers = [];
    protected $delimiter;


    /**
     * RedisRepository constructor.
     */
    public function __construct($delimiter = ':')
    {
        //$this->client = app()->make('searchClient');
        $this->delimiter = $delimiter;
    }


    public function getKeys($pattern = 'logger-*')
    {
        if (is_null($pattern)) {
            $pattern = 'logger-*';
        }

        if (empty($this->keys)) {

            $this->keys = array_keys($this->client->indices()->getMapping([
                'index' => 'logger-*'
            ]));
        }

        return $this->keys;
    }


    public function get($key, $start = 0, $end = 25)
    {
        $parsedLogs = [];
        $logs       = $this->client->lrange($key, $start, $end);
        foreach ($logs as $log) {
            $parsedLogs[] = json_decode($log);
        }

        return $parsedLogs;
    }


    public function count($key)
    {
        $this->client->llen($key);
    }


    public function getServers()
    {
        if (empty($this->servers)) {
            $results = $this->client->search([
                'body' => [
                    "aggs" => [
                        "servers" => [
                            "terms" => [
                                "field" => "server",
                                "size"  => 0
                            ]
                        ]
                    ]
                ]
            ]);

            $servers = array_pluck($results['aggregations']['servers']['buckets'], 'key');

            $this->servers = array_combine($servers, $servers);
        }

        return $this->servers;
    }

}