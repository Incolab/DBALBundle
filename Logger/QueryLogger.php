<?php

namespace Incolab\DBALBundle\Logger;

use Doctrine\DBAL\Logging\DebugStack;
use Psr\Log\LoggerInterface;
use Symfony\Component\Stopwatch\Stopwatch;

class QueryLogger extends DebugStack {


    protected $logger;
    protected $stopwatch;
    protected $name;

    /**
     * Constructor.
     *
     * @param string $name    A Connection name
     * @param LoggerInterface $logger    A LoggerInterface instance
     * @param Stopwatch       $stopwatch A Stopwatch instance
     */
    public function __construct($name, LoggerInterface $logger = null, Stopwatch $stopwatch = null) {
        $this->name = $name;
        $this->logger = $logger;
        $this->stopwatch = $stopwatch;
    }


    public function startQuery($sql, array $params = null, array $types = null) {
        parent::startQuery($sql, $params, $types);

        if(!is_null($this->stopwatch)) {
            $this->stopwatch->start('dbal::'.$this->name);
        }

        if (!is_null($this->logger)) {
            $context = array("connection" => $this->name,"params" => is_null($params) ? array() : $params);
            $this->logger->debug($sql,$context);
        }
    }

    public function stopQuery() {
        parent::stopQuery();
        if(!is_null($this->stopwatch)) {
            $this->stopwatch->stop('dbal::'.$this->name);
        }
    }

}