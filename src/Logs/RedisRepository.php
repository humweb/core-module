<?php namespace Humweb\Core\Logs;

use Carbon\Carbon;
use Illuminate\Support\Facades\Redis;

/**
 * RedisRepository
 *
 * @property  redis
 * @package Humweb\Core\Logs
 */
class RedisRepository
{
    protected $redis;

    protected $keys    = [];
    protected $servers = [];
    protected $delimiter;


    /**
     * RedisRepository constructor.
     */
    public function __construct($redis = 'logs', $delimiter = ':')
    {
        $this->redis     = Redis::connection($redis);
        $this->delimiter = $delimiter;
    }


    public function getKeys($pattern = 'logs:*')
    {
        if (is_null($pattern)) {
            $pattern = 'logs:*';
        }

        if (empty($this->keys)) {

            if (strpos($pattern, '-') !== false) {
            }
            $this->keys = $this->redis->keys($pattern);

            foreach ($this->keys as $key) {
                $tokens                    = explode($this->delimiter, $key);
                $this->servers[$tokens[1]] = 1;
            }
            $this->servers = array_keys($this->servers);
        }

        return $this->keys;
    }


    public function get($key, $start = 0, $end = 25, $level = '*')
    {

        $parsedLogs = [];

        $it = null;

        if (strpos($key, '-') !== false) {
            $dates = explode(':', $key);

            $prefix = $dates[0].':'.$dates[1].':';

            $dates = $dates[2];
            list($startDate, $endDate) = explode('-', $dates);
            $date     = Carbon::createFromFormat('Y.m.d', $startDate);
            $lastDate = Carbon::createFromFormat('Y.m.d', $endDate);
            $days     = $date->diffInDays($lastDate);

            $newKeys = [];
            for ($i = 0; $i <= $days; $i++) {
                $arrKeys = $this->redis->scan(0, 'match', $prefix.$date->format('Y.m.d').':'.$level);

                foreach ($arrKeys[1] as $key) {
                    $newKeys[] = $key;
                }
                $date->addDay();
            }
            $arrKeys = $newKeys;
        } else {
            $arrKeys = $this->redis->scan(0, 'match', $key.':'.$level);
        }

        $parsedLogs = $this->parseLogs($arrKeys, $parsedLogs);

        return $parsedLogs;
    }


    public function parseLogs($keys = [], $parsedLogs = [])
    {

        if (isset($keys[1]) && is_array($keys[1])) {
            return $this->parseLogs($keys[1], $parsedLogs);
        } else {
            foreach ($keys as $key) {

                $logs = $this->redis->lrange($key, 0, 1000);

                foreach ($logs as $log) {
                    $log          = json_decode($log);
                    $parsedLogs[] = $log;
                }
            }

            return $parsedLogs;
        }
    }


    public function sort($ary, $asc = true)
    {
        if ($asc) {
            return array_values($ary);
        } else {
            return array_reverse($ary);
        }
    }


    public function count($key)
    {
        $this->redis->llen($key);
    }


    public function getServers($pattern = 'logs:*')
    {
        if (empty($this->servers)) {
            $this->getKeys($pattern);
        }

        return $this->servers;
    }
}