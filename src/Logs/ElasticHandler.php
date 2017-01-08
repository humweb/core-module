<?php namespace Humweb\Core\Logs;

/**
 * ElasticHandler
 *
 * @package Humweb\Core\Logs
 */


use Monolog\Formatter\FormatterInterface;
use Monolog\Formatter\ElasticaFormatter;
use Monolog\Logger;

/**
 * Elastic Search handler
 *
 * Usage example:
 *
 *    $client = new \Elastica\Client();
 *    $options = array(
 *        'index' => 'elastic_index_name',
 *        'type' => 'elastic_doc_type',
 *    );
 *    $handler = new ElasticSearchHandler($client, $options);
 *    $log = new Logger('application');
 *    $log->pushHandler($handler);
 *
 * @author Jelle Vink <jelle.vink@gmail.com>
 */
class ElasticHandler extends AbstractProcessingHandler
{
    /**
     * @var Client
     */
    protected $client;

    /**
     * @var array Handler config options
     */
    protected $options = array();

    /**
     * @param Client  $client  Elastica Client object
     * @param array   $options Handler configuration
     * @param integer $level   The minimum logging level at which this handler will be triggered
     * @param Boolean $bubble  Whether the messages that are handled can bubble up the stack or not
     */
    public function __construct(Client $client, array $options = array(), $level = Logger::DEBUG, $bubble = true)
    {
        parent::__construct($level, $bubble);
        $this->client = $client;
        $this->options = array_merge(
            array(
                'index'          => 'monolog',      // Elastic index name
                'type'           => 'record',       // Elastic document type
                'ignore_error'   => false,          // Suppress Elastica exceptions
            ),
            $options
        );
    }

    /**
     * {@inheritDoc}
     */
    protected function write(array $record)
    {
        $this->bulkSend(array($record['formatted']));
    }

    /**
     * {@inheritdoc}
     */
    public function setFormatter(FormatterInterface $formatter)
    {
        if ($formatter instanceof ElasticaFormatter) {
            return parent::setFormatter($formatter);
        }
        throw new \InvalidArgumentException('ElasticSearchHandler is only compatible with ElasticaFormatter');
    }

    /**
     * Getter options
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * {@inheritDoc}
     */
    protected function getDefaultFormatter()
    {
        return new ElasticaFormatter($this->options['index'], $this->options['type']);
    }

    /**
     * {@inheritdoc}
     */
    public function handleBatch(array $records)
    {
        $documents = $this->getFormatter()->formatBatch($records);
        $this->bulkSend($documents);
    }

    /**
     * Use Elasticsearch bulk API to send list of documents
     * @param  array             $documents
     * @throws \RuntimeException
     */
    protected function bulkSend(array $documents)
    {
        try {
            $this->client->addDocuments($documents);
        } catch (ExceptionInterface $e) {
            if (!$this->options['ignore_error']) {
                throw new \RuntimeException("Error sending messages to Elasticsearch", 0, $e);
            }
        }
    }
}
