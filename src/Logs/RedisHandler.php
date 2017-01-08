<?php namespace Humweb\Core\Logs;

use DateTime;
use Illuminate\Redis\Database;
use Monolog\Formatter\JsonFormatter;
use Monolog\Handler\AbstractProcessingHandler;
use Monolog\Logger;

/**
 * Logs to a Redis key using rpush
 *
 * usage example:
 *
 *   $log = new Logger('application');
 *   $redis = new RedisHandler(new Predis\Client("tcp://localhost:6379"), "logs", "prod");
 *   $log->pushHandler($redis);
 *
 */
class RedisHandler extends AbstractProcessingHandler
{
    private $redisClient;
    private $redisKey;


    # redis instance, key to use
    public function __construct($redis = null, $key, $level = Logger::DEBUG, $bubble = true)
    {

        $this->redisClient = (new Database(app()['config']['database.redis']))->connection('logs');
        $this->redisKey    = $key;

        parent::__construct($level, $bubble);
    }


    public function handle(array $record)
    {
        if ( ! $this->isHandling($record)) {
            return false;
        }

        $record              = $this->processRecord($record);
        $record['server']    = gethostname();
        $record['date']      = $record['datetime']->format(DateTime::ISO8601);
        $record['formatted'] = $this->getFormatter()->format($record);

        $this->write($record);

        return false === $this->bubble;
    }


    protected function write(array $record)
    {
        $id = $this->redisClient->rpush($this->redisKey.':'.$record['level'], $record["formatted"]);
    }


    /**
     * {@inheritDoc}
     */
    protected function getDefaultFormatter()
    {
        return new JsonFormatter();
    }
}
